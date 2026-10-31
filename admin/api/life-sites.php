<?php

$iteace = '1';
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../api/wz.php';
require_once __DIR__ . '/../../includes/life-admin.php';
require_once __DIR__ . '/../../includes/life-schema.php';
life_require_admin();
life_ensure_personal_tools_schema($conn);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('非法请求');
}
life_verify_csrf();

$action = (string) ($_POST['action'] ?? '');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($action === 'delete' && $id > 0) {
    $stmt = $conn->prepare('DELETE FROM `link` WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: ../life-sites.php?deleted=1');
    exit;
}

$shorten = static function (string $value, int $length): string {
    $value = trim($value);
    return function_exists('mb_substr') ? mb_substr($value, 0, $length, 'UTF-8') : substr($value, 0, $length);
};
$name = $shorten((string) ($_POST['name'] ?? ''), 160);
$url = trim((string) ($_POST['url'] ?? ''));
$description = $shorten((string) ($_POST['description'] ?? ''), 500);
$category = $shorten((string) ($_POST['category'] ?? ''), 80);
$category = $category !== '' ? $category : '常用';
$parts = parse_url($url);
$validUrl = filter_var($url, FILTER_VALIDATE_URL) !== false && is_array($parts)
    && in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true);
if ($name === '' || !$validUrl) {
    http_response_code(422);
    exit('请填写网站名称和有效链接。');
}

if ($action === 'create') {
    $host = (string) parse_url($url, PHP_URL_HOST);
    $emptyImage = $host !== '' ? 'https://' . $host . '/favicon.ico' : '';
    $stmt = $conn->prepare('INSERT INTO `link` (urls,url,urlimg,description,category) VALUES (?,?,?,?,?)');
    $stmt->bind_param('sssss', $name, $url, $emptyImage, $description, $category);
} elseif ($action === 'update' && $id > 0) {
    $host = (string) parse_url($url, PHP_URL_HOST);
    $favicon = $host !== '' ? 'https://' . $host . '/favicon.ico' : '';
    $stmt = $conn->prepare('UPDATE `link` SET urls=?,url=?,urlimg=?,description=?,category=? WHERE id=?');
    $stmt->bind_param('sssssi', $name, $url, $favicon, $description, $category, $id);
} else {
    http_response_code(422);
    exit('操作参数不正确。');
}
$stmt->execute();
$stmt->close();
header('Location: ../life-sites.php?saved=1');
