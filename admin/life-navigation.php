<?php
require_once __DIR__ . '/require-login.php';

header('Location: ./index.php'); exit;

$iteace = '1';
require_once __DIR__ . '/../config.php';
require __DIR__ . '/../api/wz.php';
require_once __DIR__ . '/../includes/life-security.php';
require_once __DIR__ . '/../includes/life-admin.php';

if ($userdlzt !== '1') {
    header('Location: ./login.php');
    exit;
}
if ($user_zh !== $glyadmin || !hash_equals((string) $passid, (string) $user_passid)) {
    http_response_code(403);
    exit('没有权限');
}

$hasTableResult = $conn->query("SHOW TABLES LIKE 'life_navigation'");
$hasTable = $hasTableResult && $hasTableResult->num_rows > 0;
$items = [];
if ($hasTable) {
    $result = $conn->query('SELECT * FROM life_navigation ORDER BY sort_order ASC, id ASC');
    while ($result && $row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
$csrf = life_csrf_token();
life_admin_start('导航管理', 'navigation');
?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">导航已保存。</div><?php endif; ?>
<?php if (isset($_GET['installed'])): ?><div class="alert alert-success">个人生活系统数据表已初始化。</div><?php endif; ?>
<?php if (!$hasTable): ?>
  <div class="card"><header class="card-header"><div class="card-title">初始化个人生活系统</div></header><div class="card-body">
    <p>将创建导航、记录和画廊数据表，不会删除朋友圈原有表。</p>
    <form method="post" action="./api/life-install.php"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><button class="btn btn-primary" type="submit">初始化数据表</button></form>
  </div></div>
<?php else: ?>
  <div class="card"><header class="card-header"><div class="card-title">添加导航</div></header><div class="card-body">
    <form class="form-row" method="post" action="./api/life-navigation.php">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="create">
      <div class="form-group col-md-2"><label>名称</label><input class="form-control" name="name" maxlength="50" required></div>
      <div class="form-group col-md-2"><label>图标</label><input class="form-control" name="icon" maxlength="100" placeholder="如 🎵"></div>
      <div class="form-group col-md-3"><label>URL</label><input class="form-control" name="url" maxlength="500" placeholder="./music.php" required></div>
      <div class="form-group col-md-2"><label>排序</label><input class="form-control" type="number" name="sort_order" value="60"></div>
      <div class="form-group col-md-2"><label>打开方式</label><select class="form-control" name="target"><option value="_self">当前窗口</option><option value="_blank">新窗口</option></select></div>
      <div class="form-group col-md-1"><label>启用</label><div><input type="checkbox" name="is_enabled" value="1" checked></div></div>
      <div class="col-12"><button class="btn btn-primary" type="submit">添加</button></div>
    </form>
  </div></div>

  <div class="card"><header class="card-header"><div class="card-title">现有导航</div></header><div class="card-body">
  <?php foreach ($items as $item): ?>
    <form class="form-row align-items-end border-bottom pb-3 mb-3" method="post" action="./api/life-navigation.php">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
      <div class="form-group col-md-2"><label>名称</label><input class="form-control" name="name" maxlength="50" value="<?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
      <div class="form-group col-md-2"><label>图标</label><input class="form-control" name="icon" maxlength="100" value="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="form-group col-md-3"><label>URL</label><input class="form-control" name="url" maxlength="500" value="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" required></div>
      <div class="form-group col-md-1"><label>排序</label><input class="form-control" type="number" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>"></div>
      <div class="form-group col-md-2"><label>打开方式</label><select class="form-control" name="target"><option value="_self"<?php echo $item['target'] === '_self' ? ' selected' : ''; ?>>当前窗口</option><option value="_blank"<?php echo $item['target'] === '_blank' ? ' selected' : ''; ?>>新窗口</option></select></div>
      <div class="form-group col-md-1"><label>启用</label><div><input type="checkbox" name="is_enabled" value="1"<?php echo (int) $item['is_enabled'] === 1 ? ' checked' : ''; ?>></div></div>
      <div class="form-group col-md-1"><button class="btn btn-primary btn-sm" type="submit">保存</button></div>
    </form>
    <form method="post" action="./api/life-navigation.php" onsubmit="return confirm('确定删除这项导航吗？')" style="margin-top:-62px;margin-bottom:28px;text-align:right">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>"><button class="btn btn-danger btn-sm" type="submit">删除</button>
    </form>
  <?php endforeach; ?>
  </div></div>
<?php endif; ?>
<?php life_admin_end(); ?>
