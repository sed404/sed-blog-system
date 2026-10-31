<?php
require_once __DIR__ . "/../includes/login-throttle.php";

function yxos_client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

function yxos_set_auth_cookie(string $name, string $value, int $expires): void
{
    setcookie($name, $value, [
        'expires' => $expires,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function yxos_verify_password(string $plainText, string $storedHash): bool
{
    $info = password_get_info($storedHash);
    if (($info['algoName'] ?? 'unknown') !== 'unknown') {
        return password_verify($plainText, $storedHash);
    }

    return hash_equals($storedHash, md5($plainText));
}

function yxos_upgrade_password(mysqli $conn, int $userId, string $plainText, string $storedHash): void
{
    $info = password_get_info($storedHash);
    if (($info['algoName'] ?? 'unknown') !== 'unknown') {
        return;
    }

    $newHash = password_hash($plainText, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE user SET password = ? WHERE id = ?');
    $stmt->bind_param('si', $newHash, $userId);
    $stmt->execute();
    $stmt->close();
}
