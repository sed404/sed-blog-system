<?php

$iteace = 'adminapi';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$userAccount = isset($_COOKIE['username']) ? (string) $_COOKIE['username'] : '';
$userPassId = isset($_COOKIE['passid']) ? (string) $_COOKIE['passid'] : '';
if ($userAccount === '' || $userPassId === '') {
    image_settings_redirect('请先登录');
}

require '../../config.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    image_settings_redirect('数据库连接失败');
}
$conn->set_charset('utf8mb4');

$stmt = $conn->prepare('SELECT username, passid FROM user WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $userAccount);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

$adminResult = $conn->query('SELECT username FROM admin WHERE id = 1 LIMIT 1');
$adminRow = $adminResult ? $adminResult->fetch_assoc() : null;
if (!$currentUser || !$adminRow || !hash_equals((string) $currentUser['passid'], $userPassId) || $currentUser['username'] !== $adminRow['username']) {
    image_settings_redirect('没有权限');
}

$action = isset($_POST['action']) ? (string) $_POST['action'] : '';
if ($action === 'save_qiniu') {
    $settings = array(
        'accessKey' => trim((string) ($_POST['access_key'] ?? '')),
        'secretKey' => trim((string) ($_POST['secret_key'] ?? '')),
        'bucket' => trim((string) ($_POST['bucket'] ?? '')),
        'domain' => rtrim(trim((string) ($_POST['domain'] ?? '')), '/'),
        'uploadUrl' => rtrim(trim((string) ($_POST['upload_url'] ?? 'https://upload.qiniup.com')), '/'),
    );
    if ($settings['accessKey'] === '' || $settings['secretKey'] === '' || $settings['bucket'] === '' || !image_settings_valid_url($settings['domain']) || !image_settings_valid_url($settings['uploadUrl'])) {
        image_settings_redirect('请完整填写七牛云配置，域名必须是有效的 http/https 地址');
    }
    $json = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    image_settings_save_config($conn, 'qiniu', $json);
    image_settings_redirect('七牛云设置已保存', true);
}

$definitions = array(
    'icon' => array('column' => 'icon', 'label' => 'Favicon', 'prefix' => 'favicon'),
    'logo' => array('column' => 'logo', 'label' => 'Logo', 'prefix' => 'logo'),
    'cover' => array('column' => 'homimg', 'label' => '相册封面', 'prefix' => 'cover'),
    'background' => array('column' => '', 'label' => '网页背景图', 'prefix' => 'background'),
);
$kind = isset($_POST['kind']) ? (string) $_POST['kind'] : '';
if (!isset($definitions[$kind])) {
    image_settings_redirect('未知的图像类型');
}
$definition = $definitions[$kind];

if ($action === 'clear_image') {
    if ($kind === 'background') {
        image_settings_delete_prefix('../../user/site-images', 'background');
        image_settings_delete_legacy_backgrounds();
        image_settings_save_config($conn, 'site_background', '');
    } else {
        $current = image_settings_admin_value($conn, $definition['column']);
        image_settings_delete_managed_file($current);
        image_settings_update_admin($conn, $definition['column'], '');
    }
    image_settings_redirect($definition['label'] . '已清除', true);
}

if ($action !== 'save_image') {
    image_settings_redirect('提交参数错误');
}

$imageUrl = trim((string) ($_POST['image_url'] ?? ''));
$sourceMode = isset($_POST['source_mode']) ? (string) $_POST['source_mode'] : 'url';
$hasUpload = isset($_FILES['image_file']) && (int) $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE;
if ($imageUrl === '' && !$hasUpload) {
    image_settings_redirect('请填写图片外链或选择本地图片');
}

if ($sourceMode === 'upload' && $hasUpload) {
    $savedValue = image_settings_store_upload($_FILES['image_file'], $definition['prefix']);
} elseif ($imageUrl !== '') {
    if (!image_settings_valid_url($imageUrl)) {
        image_settings_redirect('图片外链必须是有效的 http/https 地址');
    }
    $savedValue = $imageUrl;
} elseif ($hasUpload) {
    $savedValue = image_settings_store_upload($_FILES['image_file'], $definition['prefix']);
} else {
    image_settings_redirect('请填写图片外链或选择本地图片');
}

if ($kind === 'background') {
    image_settings_save_config($conn, 'site_background', $savedValue);
    image_settings_delete_legacy_backgrounds();
} else {
    $current = image_settings_admin_value($conn, $definition['column']);
    if ($current !== $savedValue) {
        image_settings_delete_managed_file($current);
    }
    image_settings_update_admin($conn, $definition['column'], $savedValue);
}

image_settings_redirect($definition['label'] . '已更新', true);

function image_settings_valid_url($url)
{
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    return in_array($scheme, array('http', 'https'), true);
}

function image_settings_store_upload($file, $prefix)
{
    if (!isset($file['error']) || (int) $file['error'] !== UPLOAD_ERR_OK) {
        image_settings_redirect('图片上传失败，请重新选择');
    }
    if ((int) $file['size'] <= 0 || (int) $file['size'] > 8 * 1024 * 1024) {
        image_settings_redirect('图片大小必须在 8MB 以内');
    }
    $imageInfo = @getimagesize($file['tmp_name']);
    $mime = is_array($imageInfo) && isset($imageInfo['mime']) ? $imageInfo['mime'] : '';
    $extensions = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    );
    if (!isset($extensions[$mime])) {
        image_settings_redirect('仅支持 JPG、PNG、GIF、WebP 图片');
    }
    $directory = '../../user/site-images';
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        image_settings_redirect('无法创建图片目录，请检查写入权限');
    }
    image_settings_delete_prefix($directory, $prefix);
    $filename = $prefix . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
        image_settings_redirect('保存图片失败，请检查目录权限');
    }
    return './user/site-images/' . $filename;
}

function image_settings_update_admin($conn, $column, $value)
{
    $allowed = array('icon', 'logo', 'homimg');
    if (!in_array($column, $allowed, true)) {
        image_settings_redirect('图像字段错误');
    }
    $stmt = $conn->prepare('UPDATE admin SET ' . $column . ' = ? WHERE id = 1');
    $stmt->bind_param('s', $value);
    if (!$stmt->execute()) {
        image_settings_redirect('保存图像设置失败');
    }
    $stmt->close();
}

function image_settings_admin_value($conn, $column)
{
    $allowed = array('icon', 'logo', 'homimg');
    if (!in_array($column, $allowed, true)) {
        return '';
    }
    $result = $conn->query('SELECT ' . $column . ' FROM admin WHERE id = 1 LIMIT 1');
    $row = $result ? $result->fetch_assoc() : null;
    return $row ? (string) $row[$column] : '';
}

function image_settings_save_config($conn, $title, $text)
{
    $stmt = $conn->prepare('SELECT id FROM configx WHERE title = ? LIMIT 1');
    $stmt->bind_param('s', $title);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($exists) {
        $stmt = $conn->prepare('UPDATE configx SET text = ? WHERE title = ?');
        $stmt->bind_param('ss', $text, $title);
    } else {
        $stmt = $conn->prepare('INSERT INTO configx (title, text) VALUES (?, ?)');
        $stmt->bind_param('ss', $title, $text);
    }
    if (!$stmt->execute()) {
        image_settings_redirect('保存设置失败');
    }
    $stmt->close();
}

function image_settings_delete_managed_file($value)
{
    $prefix = './user/site-images/';
    if (strpos($value, $prefix) !== 0) {
        return;
    }
    $path = '../../' . substr($value, 2);
    if (is_file($path)) {
        unlink($path);
    }
}

function image_settings_delete_prefix($directory, $prefix)
{
    foreach (glob($directory . '/' . $prefix . '-*') ?: array() as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
}

function image_settings_delete_legacy_backgrounds()
{
    foreach (array('jpg', 'jpeg', 'png', 'gif', 'webp') as $extension) {
        $path = '../../user/bobg/bobg.' . $extension;
        if (is_file($path)) {
            unlink($path);
        }
    }
}

function image_settings_redirect($message, $saved = false)
{
    $query = ($saved ? 'saved=1&message=' : 'error=1&message=') . rawurlencode($message);
    header('Location: ../imgset.php?' . $query);
    exit;
}
