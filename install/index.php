<?php
session_start();
$_SESSION['install_csrf'] ??= bin2hex(random_bytes(32));

if (is_file(__DIR__ . '/ins.bak')) {
    exit('系统已经安装。如需重新安装，请先备份数据并删除 install/ins.bak。');
}
?><!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>安装 Life</title>
  <link rel="icon" href="../assets/img/favicon.png" type="image/png">
  <link href="../admin/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../admin/assets/css/style.min.css" rel="stylesheet">
  <style>
    body{background:#f4f7f9}.install-shell{max-width:720px;margin:48px auto;padding:0 18px}.card{border:0;border-radius:8px;box-shadow:0 14px 40px rgba(40,60,80,.08)}.card-body{padding:32px}.install-title{margin:0 0 8px;font-size:28px}.install-note{color:#7b8794;margin-bottom:28px}.form-control{height:44px}.btn{height:44px}
  </style>
</head>
<body>
<main class="install-shell">
  <section class="card">
    <div class="card-body">
      <h1 class="install-title">安装 Life</h1>
      <p class="install-note">基于 yxos 的个人生活记录系统。请准备一个空的 MySQL 8 数据库。</p>
      <form action="install.php" method="post" autocomplete="off"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['install_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group"><label for="db-host">数据库地址</label><input class="form-control" id="db-host" name="servername" value="localhost" required></div>
        <div class="form-group"><label for="db-name">数据库名称</label><input class="form-control" id="db-name" name="dbname" required></div>
        <div class="form-group"><label for="db-user">数据库账号</label><input class="form-control" id="db-user" name="username" required></div>
        <div class="form-group"><label for="db-password">数据库密码</label><input class="form-control" type="password" id="db-password" name="password" autocomplete="new-password"></div>
        <hr>
        <div class="form-group"><label for="admin-account">管理员账号</label><input class="form-control" id="admin-account" name="admin" required minlength="5" maxlength="32" pattern="[A-Za-z0-9_]+" autocomplete="username"></div>
        <div class="form-group"><label for="admin-password">管理员密码</label><input class="form-control" type="password" id="admin-password" name="admin_password" required minlength="8" maxlength="72" autocomplete="new-password"></div>
        <button class="btn btn-primary btn-block" type="submit">开始安装</button>
      </form>
    </div>
  </section>
</main>
</body>
</html>
