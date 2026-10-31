<?php

$iteace = '1';
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../api/wz.php';
require_once __DIR__ . '/../../includes/life-security.php';
require_once __DIR__ . '/../../includes/life-schema.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('非法请求');
}
if ($userdlzt !== '1' || $user_zh !== $glyadmin || !hash_equals((string) $passid, (string) $user_passid)) {
    http_response_code(403);
    exit('没有权限');
}
life_verify_csrf();
$conn->set_charset('utf8mb4');

$files = glob(__DIR__ . '/../../migrations/[0-9][0-9][0-9]_*.sql') ?: [];
sort($files, SORT_STRING);
foreach ($files as $sqlFile) {
    $sql = file_get_contents($sqlFile);
    if ($sql === false || !$conn->multi_query($sql)) {
        http_response_code(500);
        exit('初始化失败，请检查数据库配置或迁移文件。');
    }
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    if ($conn->errno) {
        http_response_code(500);
        exit('初始化失败，请检查数据库配置或迁移文件。');
    }
}
header('Location: ../life-navigation.php?installed=1');
