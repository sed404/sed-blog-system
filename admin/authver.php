<?php
if (($_GET['condition'] ?? '') !== 'error') {
    header('Location: ../index.php');
    exit;
}
require_once __DIR__ . '/../includes/admin-shell.php';
?><!doctype html>
<html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>授权验证失败 - Seb朋友圈系统</title>
<link rel="stylesheet" href="./assets/css/bootstrap-4.6.2.min.css"><?php admin_ui_assets(); ?></head>
<body class="admin-ui"><div class="admin-login-tools"><?php admin_ui_theme_button(); ?></div><?php admin_ui_theme_panel(); ?>
<main class="admin-login"><div class="card"><header class="card-header"><h1 class="card-title">授权验证失败</h1></header><div class="card-body">
<div class="alert alert-danger" role="alert">系统未获取到当前域名的授权信息。</div>
<p>当前域名：<?php echo admin_ui_escape($_SERVER['SERVER_NAME'] ?? ''); ?></p>
<p class="text-muted">请检查网络连接、授权服务状态和域名授权配置，稍后重试。</p>
<a class="btn btn-primary" href="./index.php">返回后台</a> <a class="btn btn-secondary" href="./login.php">重新登录</a>
</div></div></main></body></html>
