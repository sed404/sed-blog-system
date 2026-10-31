<?php

$iteace = '1';
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../api/wz.php';
require_once __DIR__ . '/../../includes/life-admin.php';
require_once __DIR__ . '/../../includes/life-schema.php';
life_require_admin();
if (!life_ensure_personal_tools_schema($conn)) { http_response_code(503); exit('数据表升级失败，请检查数据库权限后重试。'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('非法请求');
}
life_verify_csrf();

$action = (string) ($_POST['action'] ?? '');
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($action === 'delete' && $id > 0) {
    $stmt = $conn->prepare('DELETE FROM life_planner_items WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: ../life-planner.php?deleted=1');
    exit;
}

$type = (string) ($_POST['item_type'] ?? '');
$status = (string) ($_POST['status'] ?? '');
$templateKey = (string) ($_POST['template_key'] ?? 'standard');
$allowedStatuses = $type === 'goal' ? ['in_progress', 'completed'] : ['pending', 'completed'];
if ($action === 'status' && $id > 0 && in_array($type, ['goal', 'memo'], true) && in_array($status, $allowedStatuses, true)) {
    $stmt = $conn->prepare('UPDATE life_planner_items SET status=? WHERE id=? AND item_type=?');
    $stmt->bind_param('sis', $status, $id, $type);
    $stmt->execute();
    $stmt->close();
    header('Location: ../life-planner.php?saved=1');
    exit;
}

$shorten = static function (string $value, int $length): string {
    $value = trim($value);
    return function_exists('mb_substr') ? mb_substr($value, 0, $length, 'UTF-8') : substr($value, 0, $length);
};
$title = $shorten((string) ($_POST['title'] ?? ''), 200);
$details = $shorten((string) ($_POST['details'] ?? ''), 2000);
$progressCurrent = filter_input(INPUT_POST, 'progress_current', FILTER_VALIDATE_INT, ['options'=>['min_range'=>0,'max_range'=>1000000000]]);
$progressTotal = filter_input(INPUT_POST, 'progress_total', FILTER_VALIDATE_INT, ['options'=>['min_range'=>0,'max_range'=>1000000000]]);
if ($progressCurrent === false || $progressTotal === false) { http_response_code(422); exit('目标进度必须是有效的非负整数。'); }
$progressCurrent = $progressCurrent ?? 0;
$progressTotal = $progressTotal ?? 0;
if ($type !== 'goal') { $progressCurrent = 0; $progressTotal = 0; $templateKey = 'standard'; }
if (!in_array($templateKey, ['standard', 'weight'], true)) { http_response_code(422); exit('目标模板不正确。'); }
if (!in_array($type, ['goal', 'memo'], true) || $title === '' || !in_array($status, $allowedStatuses, true)) {
    http_response_code(422);
    exit('请填写完整内容。');
}

if ($action === 'create') {
    $stmt = $conn->prepare('INSERT INTO life_planner_items (item_type,title,details,status,progress_current,progress_total,template_key) VALUES (?,?,?,?,?,?,?)');
    $stmt->bind_param('ssssiis', $type, $title, $details, $status, $progressCurrent, $progressTotal, $templateKey);
} elseif ($action === 'update' && $id > 0) {
    $stmt = $conn->prepare('UPDATE life_planner_items SET item_type=?,title=?,details=?,status=?,progress_current=?,progress_total=?,template_key=? WHERE id=?');
    $stmt->bind_param('ssssiisi', $type, $title, $details, $status, $progressCurrent, $progressTotal, $templateKey, $id);
} else {
    http_response_code(422);
    exit('操作参数不正确。');
}
$stmt->execute();
$stmt->close();
header('Location: ../life-planner.php?saved=1');
