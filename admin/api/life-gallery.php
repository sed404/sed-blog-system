<?php
$iteace = '1';
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../api/wz.php';
require_once __DIR__ . '/../../includes/life-admin.php';
require_once __DIR__ . '/../../includes/life-image.php';
life_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('请求方式不正确'); }
life_verify_csrf();
if (!life_table_exists($conn, 'life_gallery_items')) { http_response_code(503); exit('请先初始化数据表'); }

$action = (string) ($_POST['action'] ?? '');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($action === 'album_create') {
    $name = trim((string) ($_POST['name'] ?? ''));
    if ($name === '') { http_response_code(422); exit('相册名称不能为空'); }
    $description = trim((string) ($_POST['description'] ?? ''));
    $sort = filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT) ?: 0;
    $stmt = $conn->prepare('INSERT INTO life_gallery_albums (name,description,sort_order) VALUES (?,?,?)');
    $stmt->bind_param('ssi', $name, $description, $sort); $stmt->execute(); $stmt->close();
    header('Location: ../life-gallery.php?saved=1'); exit;
}
if ($action === 'album_delete' && $id > 0) {
    $stmt = $conn->prepare('DELETE FROM life_gallery_albums WHERE id = ?');
    $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();
    header('Location: ../life-gallery.php?saved=1'); exit;
}
if ($action === 'item_delete' && $id > 0) {
    $stmt = $conn->prepare('UPDATE life_gallery_items SET deleted_at = NOW() WHERE id = ?');
    $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();
    header('Location: ../life-gallery.php?saved=1'); exit;
}
if ($action !== 'item_create') { http_response_code(422); exit('操作参数不正确'); }

require_once __DIR__ . '/../../includes/life-gallery-upload.php';
require_once __DIR__ . '/../../includes/life-schema.php';
if (!life_ensure_personal_tools_schema($conn)) { http_response_code(503); exit('媒体数据表升级失败，请检查数据库权限'); }
$albumId = filter_input(INPUT_POST, 'album_id', FILTER_VALIDATE_INT) ?: null;
if ($albumId !== null) {
    $albumCheck = $conn->prepare('SELECT id FROM life_gallery_albums WHERE id=?');
    $albumCheck->bind_param('i', $albumId); $albumCheck->execute();
    $albumExists = $albumCheck->get_result()->fetch_assoc(); $albumCheck->close();
    if (!$albumExists) { http_response_code(422); exit('相册不存在，请重新选择'); }
}
$description = trim((string) ($_POST['description'] ?? ''));
try {
    $takenInput = trim((string) ($_POST['taken_at'] ?? ''));
    $takenAt = life_gallery_taken_at($takenInput);
} catch (RuntimeException $exception) {
    http_response_code(422); exit(htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
}
$files = [];
$uploaded = $_FILES['image_files'] ?? null;
if (is_array($uploaded) && is_array($uploaded['name'] ?? null)) {
    foreach ($uploaded['name'] as $index => $name) {
        $files[] = [
            'name' => (string)$name,
            'type' => (string)($uploaded['type'][$index] ?? ''),
            'tmp_name' => (string)($uploaded['tmp_name'][$index] ?? ''),
            'error' => (int)($uploaded['error'][$index] ?? UPLOAD_ERR_NO_FILE),
            'size' => (int)($uploaded['size'][$index] ?? 0),
        ];
    }
} elseif (isset($_FILES['image_file'])) {
    $files[] = $_FILES['image_file']; // Compatibility with the previous single-file form.
}
if (!$files || count($files) > 20) { http_response_code(422); exit('请选择 1–20 个文件'); }
$customTitle = trim((string) ($_POST['title'] ?? ''));
$source = (string)($_POST['image_source'] ?? 'qiniu');
$tags = '';
$sort = 0;
$visible = 1;
$ratio = 'auto';
$stmt = $conn->prepare('INSERT INTO life_gallery_items (album_id,title,description,tags,image_url,image_source,media_type,display_ratio,taken_at,sort_order,is_visible) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
$saved = 0;
$errors = [];
foreach ($files as $file) {
    $filename = basename(str_replace('\\', '/', (string)($file['name'] ?? '')));
    try {
        $image = life_gallery_upload($conn, $file, $source);
        $title = count($files) === 1 && $customTitle !== '' ? $customTitle : pathinfo($filename, PATHINFO_FILENAME);
        $title = mb_substr(trim($title), 0, 255, 'UTF-8');
        $imageUrl = $image['url'];
        $imageSource = $image['source'];
        $mediaType = $image['type'];
        $stmt->bind_param('issssssssii', $albumId, $title, $description, $tags, $imageUrl, $imageSource, $mediaType, $ratio, $takenAt, $sort, $visible);
        $stmt->execute();
        $saved++;
    } catch (RuntimeException $exception) {
        $errors[] = $filename . '：' . $exception->getMessage();
    } catch (mysqli_sql_exception $exception) {
        error_log('gallery item save failed: ' . $exception->getMessage());
        $errors[] = $filename . '：保存记录失败';
    }
}
$stmt->close();
$_SESSION['gallery_upload_result'] = ['uploaded'=>$saved, 'failed'=>count($errors), 'errors'=>$errors];
header('Location: ../life-gallery.php?saved=1');
exit;
