<?php
require_once __DIR__ . '/require-login.php';

$iteace = '1';
require_once __DIR__ . '/../config.php';
require __DIR__ . '/../api/wz.php';
require_once __DIR__ . '/../includes/life-admin.php';
require_once __DIR__ . '/../includes/life-schema.php';
life_require_admin();
life_ensure_personal_tools_schema($conn);

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT) ?: 0;
$item = null;
if ($editId > 0) {
    $stmt = $conn->prepare('SELECT id,item_type,title,details,status,progress_current,progress_total,template_key FROM life_planner_items WHERE id=? LIMIT 1');
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$csrf = life_csrf_token();
$value = static function (string $key, string $default = '') use ($item): string {
    return htmlspecialchars((string) ($item[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
$typeLabels = ['goal' => '目标', 'memo' => '备忘录'];
$statusLabels = ['in_progress' => '进行中', 'pending' => '待完成', 'completed' => '已完成'];
life_admin_start('备忘管理', 'planner');
if (isset($_GET['saved'])) echo '<div class="alert alert-success">内容已保存。</div>';
if (isset($_GET['deleted'])) echo '<div class="alert alert-success">内容已删除。</div>';
echo '<p class="text-muted">关于页的显示名称、简介、兴趣和联系方式可在 <a href="./life-profile.php">关于设置</a> 中编辑。</p>';
?>
<div class="card"><header class="card-header"><div class="card-title"><?php echo $item ? '编辑内容' : '添加目标或备忘'; ?></div></header><div class="card-body">
<form method="post" action="./api/life-planner.php" id="planner-admin-form">
<input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="<?php echo $item ? 'update' : 'create'; ?>"><input type="hidden" name="id" value="<?php echo (int) ($item['id'] ?? 0); ?>">
<div class="form-row"><div class="form-group col-md-4"><label>类型</label><select class="form-control" name="item_type" id="planner-item-type"><option value="goal"<?php echo ($item['item_type'] ?? 'goal') === 'goal' ? ' selected' : ''; ?>>目标</option><option value="memo"<?php echo ($item['item_type'] ?? '') === 'memo' ? ' selected' : ''; ?>>备忘录</option></select></div><div class="form-group col-md-4"><label>状态</label><select class="form-control" name="status" id="planner-status" data-current="<?php echo $value('status', 'in_progress'); ?>"></select></div></div>
<div class="form-group" id="planner-template-field"><label>目标模板</label><select class="form-control" name="template_key"><option value="standard"<?php echo $value('template_key', 'standard') === 'standard' ? ' selected' : ''; ?>>普通目标</option><option value="weight"<?php echo $value('template_key', 'standard') === 'weight' ? ' selected' : ''; ?>>体重管理</option></select><small class="form-text text-muted">体重管理模板会在进行中的目标下显示最近五次体重记录，单位为斤。</small></div>
<div class="form-group"><label>标题</label><input class="form-control" name="title" maxlength="200" value="<?php echo $value('title'); ?>" required></div>
<div class="form-group"><label>补充说明</label><textarea class="form-control" name="details" maxlength="2000" rows="4"><?php echo $value('details'); ?></textarea></div>
<div class="form-row"><div class="form-group col-md-4"><label>目标当前进度</label><input class="form-control" name="progress_current" type="number" min="0" max="1000000000" step="1" value="<?php echo $value('progress_current','0'); ?>"></div><div class="form-group col-md-4"><label>目标总量（0 表示不展示进度条）</label><input class="form-control" name="progress_total" type="number" min="0" max="1000000000" step="1" value="<?php echo $value('progress_total','0'); ?>"></div></div>
<p class="text-muted">进行中的目标显示在关于页的兴趣上方。备忘录不使用进度数值。</p>
<button class="btn btn-primary" type="submit">保存内容</button><?php if ($item): ?> <a class="btn btn-secondary" href="./life-planner.php">取消编辑</a><?php endif; ?>
</form></div></div>

<div class="card"><header class="card-header"><div class="card-title">目标与备忘列表</div></header><div class="card-body table-responsive"><table class="table table-hover"><thead><tr><th>类型</th><th>模板</th><th>标题</th><th>说明</th><th>状态</th><th>操作</th></tr></thead><tbody>
<?php $result = $conn->query("SELECT id,item_type,title,details,status,template_key FROM life_planner_items ORDER BY FIELD(status,'in_progress','pending','completed'),updated_at DESC,id DESC"); while ($result && $row = $result->fetch_assoc()): ?>
<tr><td><?php echo $typeLabels[$row['item_type']] ?? htmlspecialchars($row['item_type'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo $row['item_type'] === 'goal' ? (($row['template_key'] ?? 'standard') === 'weight' ? '体重管理' : '普通目标') : '—'; ?></td><td><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo nl2br(htmlspecialchars($row['details'], ENT_QUOTES, 'UTF-8')); ?></td><td><form method="post" action="./api/life-planner.php"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>"><input type="hidden" name="item_type" value="<?php echo htmlspecialchars($row['item_type'], ENT_QUOTES, 'UTF-8'); ?>"><select class="form-control form-control-sm" name="status" onchange="this.form.submit()"><?php $allowed = $row['item_type'] === 'goal' ? ['in_progress','completed'] : ['pending','completed']; foreach ($allowed as $status): ?><option value="<?php echo $status; ?>"<?php echo $row['status'] === $status ? ' selected' : ''; ?>><?php echo $statusLabels[$status]; ?></option><?php endforeach; ?></select></form></td><td><a class="btn btn-sm btn-primary" href="?edit=<?php echo (int) $row['id']; ?>">编辑</a> <form style="display:inline" method="post" action="./api/life-planner.php" onsubmit="return confirm('确定删除这条内容吗？')"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>"><button class="btn btn-sm btn-danger" type="submit">删除</button></form></td></tr>
<?php endwhile; ?></tbody></table></div></div>
<script>(function(){var type=document.getElementById('planner-item-type');var status=document.getElementById('planner-status');var template=document.getElementById('planner-template-field');function updateStatus(){var current=status.dataset.current;var values=type.value==='goal'?[['in_progress','进行中'],['completed','已完成']]:[['pending','待完成'],['completed','已完成']];status.innerHTML='';values.forEach(function(value){var option=document.createElement('option');option.value=value[0];option.textContent=value[1];option.selected=value[0]===current;status.appendChild(option)});status.dataset.current=status.value;template.hidden=type.value!=='goal'}type.addEventListener('change',function(){status.dataset.current=type.value==='goal'?'in_progress':'pending';updateStatus()});updateStatus()})();</script>
<?php life_admin_end(); ?>
