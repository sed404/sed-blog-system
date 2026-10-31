<?php

// Issue a short-lived, single-object upload token for the authenticated editor.
header('Content-Type: application/json; charset=utf-8');

function qiniu_token_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    qiniu_token_response(['code' => 405, 'msg' => 'Method Not Allowed'], 405);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$requestKey = (string) ($_POST['allkey'] ?? '');
$sessionKey = (string) ($_SESSION['allkey'] ?? '');
if ($requestKey === '' || $sessionKey === '' || !hash_equals($sessionKey, $requestKey)) {
    qiniu_token_response(['code' => 403, 'msg' => '请求已过期，请刷新页面'], 403);
}

$usernameCookie = (string) ($_COOKIE['username'] ?? '');
$passIdCookie = (string) ($_COOKIE['passid'] ?? '');
if ($usernameCookie === '' || $passIdCookie === '') {
    qiniu_token_response(['code' => 401, 'msg' => '请先登录'], 401);
}

require '../config.php';
require '../includes/qiniu.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    qiniu_token_response(['code' => 500, 'msg' => '数据库连接失败'], 500);
}
$conn->set_charset('utf8mb4');

$stmt = $conn->prepare('SELECT username, passid, ban, bantime FROM user WHERE username = ? LIMIT 1');
if (!$stmt) {
    qiniu_token_response(['code' => 500, 'msg' => '用户校验失败'], 500);
}
$stmt->bind_param('s', $usernameCookie);
$stmt->execute();
$stmt->bind_result($username, $passId, $ban, $banTime);
$found = $stmt->fetch();
$stmt->close();
if (!$found || !hash_equals((string) $passId, $passIdCookie)) {
    qiniu_token_response(['code' => 401, 'msg' => '登录状态已失效'], 401);
}
if ((string) $ban !== '0' && (string) $banTime !== 'false') {
    qiniu_token_response(['code' => 403, 'msg' => '当前账号不可上传'], 403);
}

$settings = qiniu_settings($conn);
if (!qiniu_valid_settings($settings)) {
    qiniu_token_response(['code' => 503, 'msg' => '请先在后台完成七牛云配置'], 503);
}

$extension = strtolower((string) ($_POST['ext'] ?? 'bin'));
if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
    qiniu_token_response(['code' => 422, 'msg' => '不支持的图片格式'], 422);
}

$key = qiniu_object_key((string) $username, $extension);
qiniu_token_response([
    'code' => 0,
    'token' => qiniu_upload_token($settings, $key),
    'key' => $key,
    'uploadUrl' => $settings['uploadUrl'],
    'url' => qiniu_object_url($settings, $key),
]);
