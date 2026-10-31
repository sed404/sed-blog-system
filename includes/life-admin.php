<?php

require_once __DIR__ . '/life-security.php';

function life_require_admin(): void
{
    global $userdlzt, $user_zh, $glyadmin, $passid, $user_passid;
    if ((string) $userdlzt !== '1') {
        header('Location: ./login.php');
        exit;
    }
    if ((string) $user_zh !== (string) $glyadmin || !hash_equals((string) $passid, (string) $user_passid)) {
        http_response_code(403);
        exit('没有权限');
    }
}

function life_admin_start(string $title, string $active): void
{
    require_once __DIR__ . '/admin-shell.php';
    ?><!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo admin_ui_escape($title . ' - ' . admin_ui_brand()); ?></title><link rel="stylesheet" href="./assets/css/admin-icons.css"><link rel="stylesheet" href="./assets/css/bootstrap-4.6.2.min.css"><link rel="stylesheet" href="./assets/css/style.min.css"><?php admin_ui_assets(); ?></head><body class="admin-ui"><div class="lyear-layout-web"><div class="lyear-layout-container"><?php admin_ui_shell(); ?><main class="lyear-layout-content"><div class="container-fluid p-t-15"><?php
}
function life_admin_end(): void
{
    ?></div></main></div></div><script src="./assets/js/jquery-3.7.1.min.js"></script><script src="./assets/js/bootstrap-4.6.2.bundle.min.js"></script><script src="./assets/js/perfect-scrollbar.min.js"></script><script src="./assets/js/main.min.js?v=<?php echo filemtime(__DIR__ . '/../admin/assets/js/main.min.js'); ?>"></script></body></html><?php
}

function life_table_exists(mysqli $conn, string $table): bool
{
    if (!preg_match('/^[a-z0-9_]+$/i', $table)) {
        return false;
    }
    $result = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'");
    return $result && $result->num_rows > 0;
}
