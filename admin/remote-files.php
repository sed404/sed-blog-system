<?php
require_once __DIR__ . '/require-login.php';
$iteace = '1';
require_once __DIR__ . '/../config.php';
require __DIR__ . '/../api/wz.php';
require_once __DIR__ . '/../includes/life-admin.php';
require_once __DIR__ . '/../includes/remote-files.php';
life_require_admin();
$csrf = life_csrf_token();
$error = ''; $success = ''; $items = []; $editing = false;
$form = ['filename' => '', 'stem' => '', 'extension' => 'txt', 'project' => '', 'description' => '', 'content' => '', 'revision' => ''];
$text = static fn(array $input, string $key, string $default = ''): string => isset($input[$key]) && is_string($input[$key]) ? $input[$key] : $default;
try {
    $store = new RemoteFiles(dirname(__DIR__));
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        life_verify_csrf();
        $action = $text($_POST, 'action');
        $editing = $action === 'update';
        foreach (array_keys($form) as $key) $form[$key] = $text($_POST, $key);
        $file = $editing ? $form['filename'] : RemoteFiles::filename($form['stem'], $form['extension']);
        $store->save($action, $file, $form['project'], $form['description'], $form['content'], $form['revision']);
        header('Location: ./remote-files.php?edit=' . rawurlencode($file) . '&saved=1', true, 303);
        exit;
    }
    $edit = $text($_GET, 'edit');
    if ($edit !== '') {
        $form = array_merge($form, $store->get($edit));
        [$form['stem'], $form['extension']] = explode('.', $form['filename']);
        $editing = true;
    }
} catch (RuntimeException | InvalidArgumentException $exception) {
    $error = $exception->getMessage();
}
try { if (isset($store)) $items = $store->all(); }
catch (RuntimeException | InvalidArgumentException $exception) { $error = $exception->getMessage(); }
if ($text($_GET, 'saved') === '1') $success = '已保存。App 可通过原读取地址获取文件内容。';
$search = trim($text($_GET, 'q'));
$filtered = array_values(array_filter($items, static fn($item) => $search === '' || stripos($item['filename'] . ' ' . $item['project'] . ' ' . $item['description'], $search) !== false));
$total = count($filtered); $pages = max(1, (int) ceil($total / 10));
$page = min($pages, max(1, (int) $text($_GET, 'page', '1')));
$rows = array_slice($filtered, ($page - 1) * 10, 10);
$basePath = rtrim(str_replace('\\', '/', dirname(dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/remote-files.php')))), '/.');
$publicPath = static fn(string $file): string => $basePath . '/' . rawurlencode($file);
life_admin_start('后端管理', 'remote-files');
?>
<div class="admin-welcome"><h1>后端管理</h1><p>统一管理 App 远程读取的文本文件。保存后可通过文件地址直接读取，无需登录。</p></div>
<?php if ($error !== ''): ?><div class="alert alert-danger" role="alert"><?php echo admin_ui_escape($error); ?></div><?php endif; ?>
<?php if ($success !== ''): ?><div class="alert alert-success" role="status"><?php echo admin_ui_escape($success); ?></div><?php endif; ?>
<div class="card">
  <header class="card-header"><div class="card-title"><?php echo $editing ? '编辑接口文件' : '添加接口文件'; ?></div></header>
  <div class="card-body">
    <form method="post" action="./remote-files.php" id="remote-file-form">
      <input type="hidden" name="csrf_token" value="<?php echo admin_ui_escape($csrf); ?>">
      <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">
      <input type="hidden" name="filename" value="<?php echo admin_ui_escape($form['filename']); ?>">
      <input type="hidden" name="revision" value="<?php echo admin_ui_escape($form['revision']); ?>">
      <div class="form-row">
        <div class="form-group col-md-4"><label for="remote-project">所属项目</label><input class="form-control" id="remote-project" name="project" value="<?php echo admin_ui_escape($form['project']); ?>" placeholder="例如：我的 App" maxlength="80" required></div>
        <div class="form-group col-md-5"><label for="remote-stem">文件名</label><input class="form-control" id="remote-stem" name="stem" value="<?php echo admin_ui_escape($form['stem']); ?>" placeholder="例如：wangzhan" pattern="[a-zA-Z0-9][a-zA-Z0-9_\-]{0,79}" maxlength="80" required <?php echo $editing ? 'readonly' : ''; ?>></div>
        <div class="form-group col-md-3"><label for="remote-extension">后缀</label><input class="form-control" id="remote-extension" name="extension" value="<?php echo admin_ui_escape($form['extension']); ?>" list="remote-extensions" placeholder="txt" pattern="[a-zA-Z][a-zA-Z0-9]{0,15}" maxlength="16" required <?php echo $editing ? 'readonly' : ''; ?>><datalist id="remote-extensions"><option value="txt"><option value="json"><option value="xml"><option value="csv"><option value="yaml"><option value="cfg"><option value="dat"></datalist></div>
      </div>
      <div class="form-group"><label for="remote-description">说明</label><input class="form-control" id="remote-description" name="description" maxlength="300" value="<?php echo admin_ui_escape($form['description']); ?>" placeholder="例如：App 首页公告 / 版权配置 / 更新信息"></div>
      <div class="form-group"><label for="remote-content">文件内容</label><textarea class="form-control remote-content" id="remote-content" name="content" rows="12" spellcheck="false" aria-describedby="remote-content-help"><?php echo admin_ui_escape($form['content']); ?></textarea><div class="text-muted mt-2" id="remote-content-help">UTF-8 文本，最多 1 MB。按填写内容保存，不添加 HTML 或 JSON 包装；请勿填写密码或私密密钥。</div></div>
      <?php if ($editing): ?><div class="remote-address mb-3"><span>读取地址</span><a href="<?php echo admin_ui_escape($publicPath($form['filename'])); ?>" data-remote-url target="_blank" rel="noopener noreferrer"><?php echo admin_ui_escape($publicPath($form['filename'])); ?></a></div><p class="text-muted">编辑时保留文件名和后缀，避免现有 App 地址失效。需要新地址时，请添加新文件。</p><?php endif; ?>
      <button class="btn btn-primary" type="submit"><?php echo $editing ? '保存内容' : '创建文件'; ?></button>
      <?php if ($editing): ?><a class="btn btn-secondary ml-2" href="./remote-files.php">添加新文件</a><?php endif; ?>
      <span class="text-muted ml-2" id="remote-byte-count"></span>
    </form>
  </div>
</div>
<div class="card">
  <header class="card-header"><div class="card-title">接口文件</div></header>
  <div class="card-body">
    <form class="form-inline mb-3" method="get" action="./remote-files.php"><label for="remote-search" class="mr-2">关键词</label><input class="form-control mr-2" id="remote-search" name="q" value="<?php echo admin_ui_escape($search); ?>" placeholder="项目 / 文件名 / 说明"><button class="btn btn-primary mr-2" type="submit">搜索</button><a class="btn btn-secondary" href="./remote-files.php">重置</a></form>
    <div class="table-responsive"><table class="table table-hover remote-table"><thead><tr><th>文件名</th><th>所属项目</th><th>说明</th><th>大小</th><th>更新时间</th><th>读取地址</th><th>操作</th></tr></thead><tbody>
    <?php if (!$rows): ?><tr><td colspan="7" class="text-center text-muted py-4"><?php echo $search === '' ? '暂无接口文件。根目录已有的 wangzhan.txt、banquan.txt 会自动出现在这里；没有时可通过上方表单创建。' : '未找到匹配的文件。'; ?></td></tr><?php endif; ?>
    <?php foreach ($rows as $item): ?><tr>
      <td><?php echo admin_ui_escape($item['filename']); ?></td><td><?php echo admin_ui_escape($item['project']); ?></td><td><?php echo admin_ui_escape($item['description']); ?></td><td><?php echo number_format($item['bytes']); ?> B</td><td><?php echo $item['modified'] ? date('Y-m-d H:i', $item['modified']) : '—'; ?></td>
      <td><?php if ($item['error'] === ''): ?><a href="<?php echo admin_ui_escape($publicPath($item['filename'])); ?>" data-remote-url target="_blank" rel="noopener noreferrer"><?php echo admin_ui_escape($publicPath($item['filename'])); ?></a><?php else: ?><span class="text-danger"><?php echo admin_ui_escape($item['error']); ?></span><?php endif; ?></td>
      <td><?php if ($item['error'] === ''): ?><a class="btn btn-sm btn-primary" href="?edit=<?php echo rawurlencode($item['filename']); ?>">编辑</a> <button class="btn btn-sm btn-secondary" type="button" data-copy-url="<?php echo admin_ui_escape($publicPath($item['filename'])); ?>">复制地址</button><?php else: ?>—<?php endif; ?></td>
    </tr><?php endforeach; ?>
    </tbody></table></div>
    <div class="remote-pagination"><span class="text-muted">共 <?php echo $total; ?> 条 · 10 条/页</span><nav aria-label="文件分页"><ul class="pagination">
      <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?<?php echo admin_ui_escape(http_build_query(['q' => $search, 'page' => $page - 1])); ?>" aria-label="上一页">上一页</a></li><?php endif; ?>
      <?php for ($p = max(1, $page - 2); $p <= min($pages, $page + 2); $p++): ?><li class="page-item<?php echo $p === $page ? ' active' : ''; ?>"><a class="page-link" <?php echo $p === $page ? 'aria-current="page"' : ''; ?> href="?<?php echo admin_ui_escape(http_build_query(['q' => $search, 'page' => $p])); ?>"><?php echo $p; ?></a></li><?php endfor; ?>
      <?php if ($page < $pages): ?><li class="page-item"><a class="page-link" href="?<?php echo admin_ui_escape(http_build_query(['q' => $search, 'page' => $page + 1])); ?>" aria-label="下一页">下一页</a></li><?php endif; ?>
    </ul></nav></div>
    <div id="remote-copy-status" class="text-muted mt-2" role="status"></div>
  </div>
</div>
<script src="./assets/js/remote-files.js?v=<?php echo filemtime(__DIR__ . '/assets/js/remote-files.js'); ?>" defer></script>
<?php life_admin_end(); ?>
