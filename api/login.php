<?php

require_once __DIR__ . '/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('非法请求');
}

$account = trim((string) ($_POST['zh'] ?? ''));
$plainPassword = (string) ($_POST['mm'] ?? '');
if ($account === '' || $plainPassword === '') {
    http_response_code(422);
    exit('账号和密码不能为空');
}
if (strlen($account) < 5 || strlen($account) > 32 || strlen($plainPassword) < 3 || strlen($plainPassword) > 72) {
    http_response_code(422);
    exit('账号或密码格式不正确');
}

require __DIR__ . '/../config.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(503);
    exit('暂时无法连接数据库');
}
$conn->set_charset('utf8mb4');

$stmt = $conn->prepare('SELECT * FROM user WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $account);
$stmt->execute();
$user = $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
$stmt->close();

$attempt = life_check_password('login', static fn() => $user && yxos_verify_password($plainPassword, (string) $user['password']));
if ($attempt['retry'] > 0) {
    header('Retry-After: ' . $attempt['retry']);
    exit('连续错误6次，已锁定，请' . ceil($attempt['retry'] / 60) . '分钟后重试');
}
if (!$attempt['success']) {
    http_response_code(401);
    exit('账号或密码错误');
}
if ((string) $user['ban'] === '-1') {
    http_response_code(403);
    exit('该账号当前不可用');
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
exit('登录成功!');
