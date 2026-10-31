<?php
/** Admin entry guard. Run before the public bootstrap can emit output or redirects. */
function admin_login_redirect(): never
{
    header('Cache-Control: no-store');
    $adminBase = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php'))), '/');
    header('Location: ' . $adminBase . '/login.php', true, 302);
    exit;
}

$adminAccount = $_COOKIE['username'] ?? '';
$adminToken = $_COOKIE['passid'] ?? '';
if (!is_string($adminAccount) || !is_string($adminToken) || $adminAccount === '' || $adminToken === '') {
    admin_login_redirect();
}
require_once __DIR__ . '/../config.php';
try {
    $adminConnection = new mysqli($servername, $username, $password, $dbname);
    $adminConnection->set_charset('utf8mb4');
    $adminStatement = $adminConnection->prepare('SELECT u.passid, u.ban, u.bantime FROM user u INNER JOIN admin a ON a.username = u.username WHERE u.username = ? LIMIT 1');
    $adminStatement->bind_param('s', $adminAccount);
    $adminStatement->execute();
    $adminIdentity = $adminStatement->get_result()->fetch_assoc();
    $adminStatement->close();
    $adminConnection->close();
} catch (Throwable $error) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: no-store');
    exit('后台暂时无法连接数据库，请稍后重试。');
}
if (!$adminIdentity || (string) $adminIdentity['passid'] === '' || !hash_equals((string) $adminIdentity['passid'], $adminToken)
    || ((string) ($adminIdentity['ban'] ?? '0') !== '0' && (string) ($adminIdentity['bantime'] ?? 'false') !== 'false')) {
    admin_login_redirect();
}
unset($adminAccount, $adminToken, $adminIdentity, $adminStatement, $adminConnection);
