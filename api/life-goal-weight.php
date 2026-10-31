<?php

$iteace = '0';
require __DIR__ . '/../config.php';
require __DIR__ . '/wz.php';
require_once __DIR__ . '/../includes/life-security.php';
require_once __DIR__ . '/../includes/life-schema.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('非法请求'); }
$isAdmin = (string)($userdlzt ?? '') === '1'
    && (string)($user_zh ?? '') === (string)($glyadmin ?? '')
    && hash_equals((string)($passid ?? ''), (string)($user_passid ?? ''));
if (!$isAdmin) { http_response_code(403); exit('请使用管理员账号登录后记录体重。'); }
life_verify_csrf();
if (!life_ensure_personal_tools_schema($conn)) { http_response_code(503); exit('数据表升级失败，请检查数据库权限。'); }

$goalId = filter_input(INPUT_POST, 'goal_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
$weightText = trim((string)($_POST['weight_jin'] ?? ''));
if ($goalId < 1) { http_response_code(422); exit('目标不正确。'); }
if (!preg_match('/^[1-9][0-9]{0,3}(?:\.[0-9])?$/', $weightText) || (float)$weightText > 2000) {
    header('Location: ../about.php?weight_error=' . $goalId . '#goal-' . $goalId);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM life_planner_items WHERE id=? AND item_type='goal' AND status='in_progress' AND template_key='weight' LIMIT 1");
$stmt->bind_param('i', $goalId); $stmt->execute(); $goal = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$goal) { http_response_code(404); exit('该体重管理目标不存在或已结束。'); }

$weight = (float)$weightText;
$stmt = $conn->prepare('INSERT INTO life_goal_weight_records (goal_id,weight_jin) VALUES (?,?)');
$stmt->bind_param('id', $goalId, $weight); $stmt->execute(); $stmt->close();
header('Location: ../about.php?weight_saved=' . $goalId . '#goal-' . $goalId);
exit;
