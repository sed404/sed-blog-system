<?php
header('Cache-Control: no-store');
require_once __DIR__ . '/../includes/admin-shell.php';
?><!doctype html>
<html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>管理登录 - Seb朋友圈系统</title>
<link rel="stylesheet" href="./assets/css/bootstrap-4.6.2.min.css">
<?php admin_ui_assets(); ?></head>
<body class="admin-ui"><div class="admin-login-tools"><?php admin_ui_theme_button(); ?></div><?php admin_ui_theme_panel(); ?><main class="admin-login">
<div class="admin-brand"><span class="admin-brand-mark"><?php echo admin_ui_icon('grid'); ?></span><span class="admin-brand-text"><?php echo admin_ui_escape(admin_ui_brand()); ?></span></div>
<div class="card"><div class="card-header"><h1 class="card-title">登录管理后台</h1></div><div class="card-body">
<form action="./api/login.php" method="post">
<div class="form-group"><label for="admin-account">账号</label><input class="form-control" id="admin-account" name="username" placeholder="请输入管理员账号" autocomplete="username" minlength="5" maxlength="32" required autofocus></div>
<div class="form-group"><label for="admin-password">密码</label><input class="form-control" id="admin-password" name="password" type="password" placeholder="请输入密码" autocomplete="current-password" minlength="3" maxlength="72" required></div>
<button class="btn btn-primary btn-block" type="submit">登录</button>
</form></div></div><p class="hint">请勿在公共设备保存密码</p><p class="hint"><a href="../index.php">返回前台</a></p>
</main></body></html>
