<?php
require_once __DIR__ . '/require-login.php';

$iteace = '1';
require_once __DIR__ . '/../config.php';
require __DIR__ . '/../api/wz.php';
require_once __DIR__ . '/../includes/life-admin.php';
require_once __DIR__ . '/../includes/life-schema.php';
life_require_admin();
life_ensure_personal_tools_schema($conn);

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT) ?: 0;
$site = null;
if ($editId > 0) {
    $stmt = $conn->prepare('SELECT id, urls, url, description, category FROM `link` WHERE id=? LIMIT 1');
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $site = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$categories = [];
$categoryResult = $conn->query("SELECT DISTINCT category FROM `link` WHERE category<>'' ORDER BY category");
while ($categoryResult && $row = $categoryResult->fetch_assoc()) {
    $categories[] = (string) $row['category'];
}

$csrf = life_csrf_token();
$value = static function (string $key, string $default = '') use ($site): string {
    return htmlspecialchars((string) ($site[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
life_admin_start('网站管理', 'sites');
if (isset($_GET['saved'])) echo '<div class="alert alert-success">网站已保存。</div>';
if (isset($_GET['deleted'])) echo '<div class="alert alert-success">网站已删除。</div>';
?>
<div class="card"><header class="card-header"><div class="card-title"><?php echo $site ? '编辑网站' : '添加网站'; ?></div></header><div class="card-body">
<form method="post" action="./api/life-sites.php">
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="<?php echo $site ? 'update' : 'create'; ?>"><input type="hidden" name="id" value="<?php echo (int) ($site['id'] ?? 0); ?>">
<div class="form-row"><div class="form-group col-md-6"><label>网站名称</label><input class="form-control" name="name" maxlength="160" value="<?php echo $value('urls'); ?>" required></div><div class="form-group col-md-6"><label>链接</label><input class="form-control" type="url" name="url" maxlength="1000" value="<?php echo $value('url'); ?>" placeholder="https://" required></div></div>
<div class="form-group"><label>简介</label><textarea class="form-control" name="description" maxlength="500" rows="3"><?php echo $value('description'); ?></textarea></div>
<div class="form-group"><label>分类</label><input class="form-control" name="category" maxlength="80" value="<?php echo $value('category', '常用'); ?>" list="site-category-list"><datalist id="site-category-list"><?php foreach ($categories as $category): ?><option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><?php endforeach; ?></datalist></div>
<button class="btn btn-primary" type="submit">保存网站</button><?php if ($site): ?> <a class="btn btn-secondary" href="./life-sites.php">取消编辑</a><?php endif; ?>
</form></div></div>

<div class="card"><header class="card-header"><div class="card-title">网站列表</div></header><div class="card-body table-responsive"><table class="table table-hover"><thead><tr><th>名称</th><th>分类</th><th>简介</th><th>链接</th><th>操作</th></tr></thead><tbody>
<?php $result = $conn->query('SELECT id,urls,url,description,category FROM `link` ORDER BY category,id DESC'); while ($result && $row = $result->fetch_assoc()): ?>
<tr><td><?php echo htmlspecialchars($row['urls'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></td><td><a href="<?php echo htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">打开</a></td><td><a class="btn btn-sm btn-primary" href="?edit=<?php echo (int) $row['id']; ?>">编辑</a> <form style="display:inline" method="post" action="./api/life-sites.php" onsubmit="return confirm('确定删除这个网站吗？')"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>"><button class="btn btn-sm btn-danger" type="submit">删除</button></form></td></tr>
<?php endwhile; ?></tbody></table></div></div>
<?php life_admin_end(); ?>
