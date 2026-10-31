<?php
require_once __DIR__ . '/require-login.php';
$iteace='1';
require_once __DIR__.'/../config.php';
require __DIR__.'/../api/wz.php';
require_once __DIR__.'/../includes/life-admin.php';
require_once __DIR__.'/../includes/life-profile.php';
life_require_admin();
$profile=life_profile_load($conn); $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    life_verify_csrf();
    foreach (life_profile_fields() as $key=>$label) {
        $profile[$key]=trim((string)($_POST[$key]??''));
        if (strlen($profile[$key])>($key==='bio'?12000:1500)) $error='内容过长，请精简后保存。';
    }
    foreach (['avatar','cover','website'] as $key) if (!life_profile_url_valid($profile[$key],$key!=='website')) $error='图片请填写 http(s) 或本站相对地址，个人网站请填写完整 http(s) 地址。';
    if ($profile['email']!=='' && !filter_var($profile['email'],FILTER_VALIDATE_EMAIL)) $error='请填写有效的联系邮箱。';
    if ($error==='') {
        $payload=json_encode($profile,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);
        $exists=$conn->query("SELECT title FROM configx WHERE title='life_profile' LIMIT 1");
        $stmt=$conn->prepare($exists && $exists->num_rows ? "UPDATE configx SET text=? WHERE title='life_profile'" : "INSERT INTO configx (title,text) VALUES ('life_profile',?)");
        $stmt->bind_param('s',$payload); $stmt->execute(); $stmt->close();
        header('Location: ./life-profile.php?saved=1'); exit;
    }
}
$csrf = life_csrf_token();
life_admin_start('关于设置','profile');
if (isset($_GET['saved'])) echo '<div class="alert alert-success">关于设置已保存。</div>';
if ($error!=='') echo '<div class="alert alert-danger">'.admin_ui_escape($error).'</div>';
?>
<div class="card"><header class="card-header"><div class="card-title">关于我 · 内容设置</div></header><div class="card-body">
<p class="text-muted">这些资料会公开展示。留空的联系方式不会展示。目标和进度在「备忘管理」维护，默认主题沿用「基本设置」。</p>
<form method="post"><input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
<?php foreach (life_profile_fields() as $key=>$label): ?>
<div class="form-group"><label for="profile-<?php echo $key; ?>"><?php echo $label; ?></label>
<?php if ($key==='bio'): ?><textarea id="profile-bio" class="form-control" name="bio" rows="5" maxlength="4000"><?php echo admin_ui_escape($profile[$key]); ?></textarea>
<?php else: ?><input id="profile-<?php echo $key; ?>" class="form-control" name="<?php echo $key; ?>" value="<?php echo admin_ui_escape($profile[$key]); ?>" maxlength="500" type="<?php echo $key==='email'?'email':'text'; ?>"><?php endif; ?></div>
<?php endforeach; ?>
<button class="btn btn-primary" type="submit">保存设置</button> <a href="../about.php" class="btn btn-secondary" target="_blank" rel="noopener">查看关于页</a> <a href="./life-planner.php" class="btn btn-secondary">管理目标和进度</a>
</form></div></div>
<?php life_admin_end(); ?>
