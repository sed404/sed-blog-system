<?php
/** Root-level public text endpoints. This service never executes file contents. */
final class RemoteFiles
{
    public const MAX_BYTES = 1048576;
    private const GUARD = "<?php http_response_code(404); exit; ?>\n";
    private string $root;
    private string $state;

    public function __construct(string $root)
    {
        $resolved = realpath($root);
        if ($resolved === false || !is_dir($resolved . '/includes')) {
            throw new RuntimeException('网站目录不可用。');
        }
        $this->root = $resolved;
        $this->state = $resolved . '/includes/remote-files-state.php';
    }

    public static function filename(string $stem, string $extension): string
    {
        $extension = strtolower(trim($extension));
        $stem = trim($stem);
        if (!preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_-]{0,79}\z/D', $stem)) {
            throw new InvalidArgumentException('文件名须为 1–80 位英文字母、数字、下划线或短横线，不含路径和点。');
        }
        if (!preg_match('/\A[a-z][a-z0-9]{0,15}\z/D', $extension)) {
            throw new InvalidArgumentException('后缀须为 1–16 位英文字母或数字，以字母开头，不含点。');
        }
        // Reject executable/active formats and server configuration handlers, including versioned PHP.
        if (preg_match('/\A(?:php\d*|phtml?|phtm|phar|phps|inc|cgi|fcgi|pl|py|rb|sh|bash|asp\w*|asa|asax|ashx|asmx|axd|cshtml|vbhtml|jsp\w*|do|action|cfm\w*|shtml?|shtm|stm|html?|xhtml|svg|svgz|js|mjs|cjs|css|wasm|swf|htaccess|htpasswd|config|exe|dll|com|bat|cmd|ps1|vbs|wsf)\z/iD', $extension)) {
            throw new InvalidArgumentException('不支持脚本、网页或服务器配置后缀，请使用 txt、json、xml、cfg 等文本后缀。');
        }
        if (preg_match('/\A(?:con|prn|aux|nul|com[1-9]|lpt[1-9])\z/iD', $stem)) {
            throw new InvalidArgumentException('请勿使用系统保留文件名。');
        }
        return $stem . '.' . $extension;
    }

    private function path(string $filename): string
    {
        $parts = explode('.', $filename);
        if (count($parts) !== 2 || self::filename($parts[0], $parts[1]) !== $filename) {
            throw new InvalidArgumentException('文件名无效。');
        }
        $path = $this->root . '/' . $filename;
        clearstatcache(true, $path);
        $stat = @lstat($path);
        if (is_link($path) || ($stat && ($stat['nlink'] ?? 1) > 1) || (file_exists($path) && !is_file($path))) {
            throw new RuntimeException('不能管理目录、符号链接或硬链接。');
        }
        return $path;
    }

    private function locked(callable $callback)
    {
        $path = $this->root . '/includes/remote-files-lock.php';
        if (is_link($path)) throw new RuntimeException('锁文件路径无效。');
        $handle = @fopen($path, 'c+');
        if (!$handle) throw new RuntimeException('无法写入后台管理数据，请检查 includes 目录权限。');
        try {
            if (!flock($handle, LOCK_EX)) throw new RuntimeException('暂时无法锁定文件，请重试。');
            if (fstat($handle)['size'] === 0) { fwrite($handle, self::GUARD); fflush($handle); }
            return $callback();
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function entries(): array
    {
        $entries = [];
        if (is_link($this->state)) throw new RuntimeException('管理数据路径无效。');
        if (is_file($this->state)) {
            $raw = @file_get_contents($this->state);
            if ($raw === false || !str_starts_with($raw, self::GUARD)) throw new RuntimeException('管理数据无法读取。');
            try { $entries = json_decode(substr($raw, strlen(self::GUARD)), true, 64, JSON_THROW_ON_ERROR); }
            catch (JsonException $error) { throw new RuntimeException('管理数据损坏，请恢复备份后重试。'); }
            if (!is_array($entries)) throw new RuntimeException('管理数据格式错误。');
        }
        foreach (['wangzhan.txt' => '网站配置', 'banquan.txt' => '版权信息'] as $file => $title) {
            if (!isset($entries[$file]) && is_file($this->root . '/' . $file)) {
                $entries[$file] = ['project' => '现有 App', 'description' => $title];
            }
        }
        foreach ($entries as $file => $entry) {
            if (!is_string($file) || !is_array($entry) || !is_string($entry['project'] ?? null) || !is_string($entry['description'] ?? null)) {
                throw new RuntimeException('管理数据格式错误。');
            }
        }
        return $entries;
    }

    private function content(string $file): string
    {
        $path = $this->path($file);
        if (!is_file($path)) throw new RuntimeException('文件不存在，请先恢复该文件。');
        $handle = @fopen($path, 'rb');
        if (!$handle) throw new RuntimeException('无法读取文件，请检查读取权限。');
        try { $content = stream_get_contents($handle, self::MAX_BYTES + 1); }
        finally { fclose($handle); }
        if ($content === false) throw new RuntimeException('无法读取文件。');
        self::validateContent($content);
        return $content;
    }

    private static function validateContent(string $content): void
    {
        if (strlen($content) > self::MAX_BYTES) throw new InvalidArgumentException('单个文件内容不能超过 1 MB。');
        if (!preg_match('//u', $content) || str_contains($content, "\0")) {
            throw new InvalidArgumentException('仅支持 UTF-8 文本，请先转换文件编码。');
        }
    }

    private function revision(string $file, array $entry, string $content): string
    {
        return hash('sha256', $file . "\0" . json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\0" . $content);
    }

    private function readEntry(string $file, array $entry): array
    {
        $content = $this->content($file);
        return $entry + ['filename' => $file, 'content' => $content, 'bytes' => strlen($content), 'modified' => (int) filemtime($this->path($file)), 'revision' => $this->revision($file, $entry, $content), 'error' => ''];
    }

    public function all(): array
    {
        return $this->locked(function () {
            $items = [];
            foreach ($this->entries() as $file => $entry) {
                try { $item = $this->readEntry($file, $entry); unset($item['content']); }
                catch (RuntimeException | InvalidArgumentException $error) { $item = $entry + ['filename' => $file, 'bytes' => 0, 'modified' => 0, 'revision' => '', 'error' => $error->getMessage()]; }
                $items[] = $item;
            }
            usort($items, static fn($a, $b) => strcmp($a['filename'], $b['filename']));
            return $items;
        });
    }

    public function get(string $file): array
    {
        return $this->locked(function () use ($file) {
            $entries = $this->entries();
            if (!isset($entries[$file])) throw new InvalidArgumentException('该文件未纳入后端管理。');
            return $this->readEntry($file, $entries[$file]);
        });
    }

    private function atomicWrite(string $path, string $content): void
    {
        $temporary = dirname($path) . '/.remote-' . bin2hex(random_bytes(12)) . (str_ends_with($path, '.php') ? '.php' : '.tmp');
        try {
            $written = @file_put_contents($temporary, $content, LOCK_EX);
            if ($written !== strlen($content)) throw new RuntimeException('写入失败，请检查目录权限及磁盘空间。');
            $mode = is_file($path) ? (fileperms($path) & 0777) : 0644;
            if (!@chmod($temporary, $mode) || !@rename($temporary, $path)) throw new RuntimeException('无法替换文件，请检查目录及文件权限。');
        } finally {
            if (is_file($temporary)) @unlink($temporary);
        }
    }

    private function saveEntries(array $entries): void
    {
        $this->atomicWrite($this->state, self::GUARD . json_encode($entries, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    public function save(string $action, string $file, string $project, string $description, string $content, string $revision = ''): void
    {
        if (!in_array($action, ['create', 'update'], true)) throw new InvalidArgumentException('操作无效。');
        self::validateContent($content);
        $project = trim($project); $description = trim($description);
        if ($project === '' || strlen($project) > 240 || strlen($description) > 1000 || !preg_match('//u', $project . $description)) {
            throw new InvalidArgumentException('请填写项目名称（最多 80 个汉字），说明最多 300 个汉字。');
        }
        $this->locked(function () use ($action, $file, $project, $description, $content, $revision) {
            $path = $this->path($file);
            $entries = $this->entries();
            $oldContent = null;
            if ($action === 'create') {
                foreach (array_merge(array_keys($entries), scandir($this->root) ?: []) as $existing) {
                    if (strcasecmp($existing, $file) === 0) throw new InvalidArgumentException('文件名已存在；请编辑原文件或使用其他名称，不能覆盖已有网站文件。');
                }
            } else {
                if (!isset($entries[$file])) throw new InvalidArgumentException('该文件未纳入后端管理。');
                $oldContent = $this->content($file);
                if ($revision === '' || !hash_equals($this->revision($file, $entries[$file], $oldContent), $revision)) {
                    throw new RuntimeException('文件已被其他窗口或服务器修改。请先复制当前编辑内容，再重新打开文件核对，避免覆盖他人修改。');
                }
            }
            $entries[$file] = ['project' => $project, 'description' => $description];
            $this->atomicWrite($path, $content);
            try { $this->saveEntries($entries); }
            catch (Throwable $error) {
                if ($oldContent === null) { @unlink($path); }
                else { $this->atomicWrite($path, $oldContent); }
                throw $error;
            }
        });
    }
}
