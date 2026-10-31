<?php

require_once __DIR__ . '/../../api/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$account = trim((string) ($_POST['username'] ?? ''));
$plainPassword = (string) ($_POST['password'] ?? '');
require __DIR__ . '/../../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    exit('<script>alert("暂时无法连接数据库");location.href="../login.php";</script>');
}
$conn->set_charset('utf8mb4');

$adminResult = $conn->query('SELECT username FROM admin LIMIT 1');
$admin = $adminResult ? $adminResult->fetch_assoc() : null;

$stmt = $conn->prepare('SELECT * FROM user WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $account);
$stmt->execute();
$user = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
$stmt->close();
$attempt = life_check_password('login', static fn() => $admin && hash_equals((string) $admin['username'], $account) && $user && yxos_verify_password($plainPassword, (string) $user['password']));
if ($attempt['retry'] > 0) {
    http_response_code(429);
    header('Retry-After: ' . $attempt['retry']);
    exit('<script>alert("连续错误6次，已锁定，请' . ceil($attempt['retry'] / 60) . '分钟后重试");location.href="../login.php";</script>');
}
if (!$attempt['success']) {
    exit('<script>alert("管理员账号或密码错误");location.href="../login.php";</script>');
}

$userId = (int) $user['id'];
yxos_upgrade_password($conn, $userId, $plainPassword, (string) $user['password']);
$loginTime = date('Y-m-d H:i:s');
$loginIp = yxos_client_ip();
$stmt = $conn->prepare('UPDATE user SET logtime = ?, logip = ? WHERE id = ?');
$stmt->bind_param('ssi', $loginTime, $loginIp, $userId);
$stmt->execute();
$stmt->close();

yxos_set_auth_cookie('username', (string) $user['username'], time() + 604800);
yxos_set_auth_cookie('passid', (string) $user['passid'], time() + 604800);
$conn->close();
header('Location: ../index.php');
exit;
