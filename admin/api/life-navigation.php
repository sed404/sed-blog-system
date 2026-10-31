<?php

$iteace = '1';
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../api/wz.php';
require_once __DIR__ . '/../../includes/life-security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('非法请求');
}
if ($userdlzt !== '1' || $user_zh !== $glyadmin || !hash_equals((string) $passid, (string) $user_passid)) {
    http_response_code(403);
    exit('没有权限');
}
life_verify_csrf();

$action = (string) ($_POST['action'] ?? '');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($action === 'delete') {
    $stmt = $conn->prepare('DELETE FROM life_navigation WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: ../life-navigation.php?saved=1');
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$icon = trim((string) ($_POST['icon'] ?? ''));
$url = trim((string) ($_POST['url'] ?? ''));
$sortOrder = filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT);
$sortOrder = $sortOrder === false || $sortOrder === null ? 0 : $sortOrder;
$isEnabled = isset($_POST['is_enabled']) ? 1 : 0;
$target = ($_POST['target'] ?? '_self') === '_blank' ? '_blank' : '_self';

$nameLength = preg_match_all('/./u', $name, $unusedNameMatches);
$iconLength = preg_match_all('/./u', $icon, $unusedIconMatches);
if ($name === '' || $nameLength === false || $nameLength > 50 || $iconLength === false || $iconLength > 100 || !life_is_safe_navigation_url($url)) {
    http_response_code(422);
    exit('导航名称、图标或 URL 格式不正确。');
}

if ($action === 'create') {
    $stmt = $conn->prepare('INSERT INTO life_navigation (name, icon, url, sort_order, is_enabled, target) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssiis', $name, $icon, $url, $sortOrder, $isEnabled, $target);
} elseif ($action === 'update' && $id > 0) {
    $stmt = $conn->prepare('UPDATE life_navigation SET name = ?, icon = ?, url = ?, sort_order = ?, is_enabled = ?, target = ? WHERE id = ?');
    $stmt->bind_param('sssiisi', $name, $icon, $url, $sortOrder, $isEnabled, $target, $id);
} else {
    http_response_code(422);
    exit('操作参数不正确。');
}
$stmt->execute();
$stmt->close();
header('Location: ../life-navigation.php?saved=1');
