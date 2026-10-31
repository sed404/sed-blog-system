<?php

function life_validate_external_image_url(string $url): ?string
{
    $url = trim($url);
    if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) return null;
    $parts = parse_url($url);
    $scheme = strtolower((string) ($parts['scheme'] ?? ''));
    return in_array($scheme, ['http', 'https'], true) && !empty($parts['host']) ? $url : null;
}

function life_validate_external_media_url(string $url): ?string
{
    return life_validate_external_image_url($url);
}

function life_store_uploaded_media(array $file, string $prefix = 'media'): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return '';
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || (int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > 100 * 1024 * 1024) throw new RuntimeException('视频大小必须在 100MB 以内');
    $mime = '';
    if (function_exists('finfo_open')) { $f = finfo_open(FILEINFO_MIME_TYPE); $mime = $f ? (string) finfo_file($f, (string) $file['tmp_name']) : ''; if ($f) finfo_close($f); }
    $extensions = ['video/mp4'=>'mp4','video/webm'=>'webm','video/ogg'=>'ogv','video/quicktime'=>'mov'];
    if (!isset($extensions[$mime])) throw new RuntimeException('仅支持 MP4、WebM、OGG 和 MOV 视频');
    $relativeDirectory = 'user/life/' . date('Y/m'); $directory = dirname(__DIR__) . '/' . $relativeDirectory;
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) throw new RuntimeException('无法创建媒体目录');
    $safePrefix = preg_replace('/[^a-z0-9_-]/i', '', $prefix) ?: 'media'; $target = $directory . '/' . $safePrefix . '-' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!@move_uploaded_file((string) $file['tmp_name'], $target) && !@copy((string) $file['tmp_name'], $target)) throw new RuntimeException('无法保存视频');
    @chmod($target, 0644); return './' . $relativeDirectory . '/' . basename($target);
}

function life_store_uploaded_image(array $file, string $prefix = 'image'): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return '';
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new RuntimeException('图片上传失败');
    if ((int) ($file['size'] ?? 0) <= 0 || (int) $file['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('图片大小必须在 8MB 以内');
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    if (!is_file($tmpName) || !is_readable($tmpName)) throw new RuntimeException('上传文件无效');

    $imageInfo = @getimagesize($tmpName);
    $mime = is_array($imageInfo) ? (string) ($imageInfo['mime'] ?? '') : '';
    if ($mime === '' && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? (string) finfo_file($finfo, $tmpName) : '';
        if ($finfo) finfo_close($finfo);
    }
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime]) || $imageInfo === false) {
        throw new RuntimeException('仅支持 JPEG、PNG、GIF 和 WebP 图片');
    }

    $relativeDirectory = 'user/life/' . date('Y/m');
    $directory = dirname(__DIR__) . '/' . $relativeDirectory;
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new RuntimeException('无法创建图片目录');
    }

    $safePrefix = preg_replace('/[^a-z0-9_-]/i', '', $prefix) ?: 'image';
    $filename = $safePrefix . '-' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    $target = $directory . '/' . $filename;
    $moved = is_uploaded_file($tmpName) ? @move_uploaded_file($tmpName, $target) : @rename($tmpName, $target);
    if (!$moved) $moved = @copy($tmpName, $target);
    if (!$moved || !is_file($target)) throw new RuntimeException('无法保存图片');

    @chmod($target, 0644);
    return './' . $relativeDirectory . '/' . $filename;
}

function life_image_from_request(string $source, string $url, ?array $file, string $prefix): array
{
    if ($source === 'local') {
        $path = life_store_uploaded_image($file ?? [], $prefix);
        if ($path === '') throw new RuntimeException('请选择要上传的图片');
        return ['url' => $path, 'source' => 'local'];
    }

    $validatedUrl = life_validate_external_image_url($url);
    if ($validatedUrl === null) throw new RuntimeException('请输入有效的 HTTP/HTTPS 图片地址');
    return ['url' => $validatedUrl, 'source' => 'external'];
}
