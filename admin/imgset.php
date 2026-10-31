<?php
require_once __DIR__ . '/require-login.php';

//decode by nige112
$iteace = "1";
if (is_file("../config.php")) {
	include_once "../config.php";
}
include "../api/wz.php";
if ($userdlzt == 0) {
	header("location: ./login.php");
	exit;
}
if ($user_zh == $glyadmin) {
	if ($user_passid != $passid) {
		exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"./login.php\";</script>;");
	}
} else {
	exit("<script language=\"JavaScript\">;alert(\"没有权限!\");location.href=\"../index.php\";</script>;");
}
$qiniuSettings = array('accessKey' => '', 'secretKey' => '', 'bucket' => '', 'domain' => '', 'uploadUrl' => 'https://upload.qiniup.com');
if (isset($filterqiniu) && $filterqiniu !== '') {
	$decodedQiniu = json_decode($filterqiniu, true);
	if (is_array($decodedQiniu)) {
		$qiniuSettings = array_merge($qiniuSettings, $decodedQiniu);
	}
}
$siteBackground = isset($siteBackground) ? trim((string) $siteBackground) : '';
function image_settings_preview_url($value)
{
	$value = trim((string) $value);
	if (strpos($value, './') === 0) {
		return '.' . $value;
	}
	return $value;
}
$imageSettings = array(
	'icon' => array('title' => 'Favicon 图标', 'description' => '浏览器标签页图标，建议使用正方形 PNG 或 WebP。', 'value' => $icon, 'accept' => 'image/png,image/jpeg,image/gif,image/webp'),
	'logo' => array('title' => '站点 Logo', 'description' => '用于网站品牌展示，推荐透明背景 PNG 或 WebP。', 'value' => $logo, 'accept' => 'image/png,image/jpeg,image/gif,image/webp'),
	'cover' => array('title' => '首页封面', 'description' => '用于首页、归档及用户页面的默认顶部封面。', 'value' => $homimg, 'accept' => 'image/png,image/jpeg,image/gif,image/webp'),
	'background' => array('title' => '网页背景图', 'description' => '全站页面背景，可使用对象存储外链或本地上传。', 'value' => $siteBackground, 'accept' => 'image/png,image/jpeg,image/gif,image/webp'),
);
?><!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="<?php echo $name;?>">
<meta name="description" content="<?php echo $name . " ," . $subtitle;?>">
<meta name="author" content="<?php echo $name;?>">
<title>图像设置 - Seb朋友圈系统</title>
<link rel="shortcut icon" type="image/x-icon" href="<?php 
if (strpos($icon, "http") !== false) {
	echo $icon;
} else {
	echo "." . $icon;
}
?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="x-rim-auto-match" content="none">
<link rel="stylesheet" type="text/css" href="./assets/css/admin-icons.css">
<link rel="stylesheet" type="text/css" href="./assets/css/bootstrap-4.6.2.min.css">
<link rel="stylesheet" type="text/css" href="./assets/css/style.min.css">
<style>
.image-settings-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.image-setting-card{height:100%;margin-bottom:0}.image-preview{height:180px;border:1px solid var(--admin-border-light);background:var(--admin-hover);display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:16px}.image-preview img{width:100%;height:100%;object-fit:contain}.image-preview-empty{color:var(--admin-muted);font-size:14px}.image-upload{position:relative;border:1px dashed var(--admin-border);padding:18px;text-align:center;cursor:pointer;margin-bottom:12px}.image-upload input{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer}.image-file-name{display:block;margin-top:5px;color:var(--admin-muted);font-size:12px;word-break:break-all}.image-actions{display:flex;gap:8px}.image-actions .btn-primary{flex:1}.settings-note{color:var(--admin-muted);font-size:13px;margin-bottom:14px}.status-message{margin-bottom:16px}@media(max-width:767px){.image-settings-grid{grid-template-columns:1fr}.image-preview{height:150px}}
</style>
<?php require_once __DIR__ . '/../includes/admin-shell.php'; admin_ui_assets(); ?>
</head>

<body class="admin-ui">
<!--页面loading-->
<div id="lyear-preloader" class="loading">
  <div class="ctn-preloader">
    <div class="round_spinner">
      <div class="spinner"></div>
      <img src="https://s1.locimg.com/2024/12/17/c87f60f549e70.png" alt="">
    </div>
  </div>
</div>
<!--页面loading end-->
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <!--左侧导航-->
    <?php require_once __DIR__ . '/../includes/admin-shell.php'; admin_ui_shell(); ?>
    <!--End 左侧导航-->
    
    <!--头部信息-->
    
    <!--End 头部信息-->
    
    <!--页面主要内容-->
    <main class="lyear-layout-content">
      
      <div class="container-fluid p-t-15">
        
        
        <?php if (isset($_GET['message'])): ?>
        <div class="alert <?php echo isset($_GET['saved']) ? 'alert-success' : 'alert-danger'; ?> status-message"><?php echo htmlspecialchars((string) $_GET['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="image-settings-grid">
          <?php foreach ($imageSettings as $kind => $setting): ?>
          <div class="card image-setting-card">
            <header class="card-header"><div class="card-title"><?php echo htmlspecialchars($setting['title'], ENT_QUOTES, 'UTF-8'); ?></div></header>
            <div class="card-body">
              <div class="image-preview" data-preview="<?php echo $kind; ?>">
                <?php if ($setting['value'] !== ''): ?>
                <img src="<?php echo htmlspecialchars(image_settings_preview_url($setting['value']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($setting['title'], ENT_QUOTES, 'UTF-8'); ?>预览">
                <?php else: ?>
                <span class="image-preview-empty">暂未设置</span>
                <?php endif; ?>
              </div>
              <p class="settings-note"><?php echo htmlspecialchars($setting['description'], ENT_QUOTES, 'UTF-8'); ?></p>
              <form action="./api/image-settings.php" method="post" enctype="multipart/form-data" class="image-setting-form">
                <input type="hidden" name="action" value="save_image">
                <input type="hidden" name="kind" value="<?php echo $kind; ?>">
                <input type="hidden" name="source_mode" value="url" class="source-mode-input">
                <div class="form-group">
                  <label>图片外链</label>
                  <input class="form-control image-url-input" type="url" name="image_url" value="<?php echo htmlspecialchars($setting['value'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://对象存储域名/path/image.jpg">
                </div>
                <label class="image-upload">
                  <i class="mdi mdi-cloud-upload" style="font-size:32px;color:var(--admin-success)"></i>
                  <span class="d-block">或选择本地图片</span>
                  <span class="image-file-name">JPG / PNG / GIF / WebP，最大 8MB</span>
                  <input type="file" name="image_file" accept="<?php echo $setting['accept']; ?>">
                </label>
                <div class="image-actions">
                  <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> 保存</button>
                  <?php if ($setting['value'] !== ''): ?>
                  <button type="submit" class="btn btn-outline-danger" name="action" value="clear_image" formnovalidate onclick="return confirm('确定清除这张图片吗？')"><i class="mdi mdi-delete-outline"></i></button>
                  <?php endif; ?>
                </div>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="card mt-3">
          <header class="card-header"><div class="card-title">七牛云对象存储</div></header>
          <div class="card-body">
            <p class="settings-note">开启文章发布页的七牛云开关后，图片会由浏览器直接上传到这里配置的存储空间。</p>
            <form action="./api/image-settings.php" method="post">
              <input type="hidden" name="action" value="save_qiniu">
              <div class="form-row">
                <div class="form-group col-md-6"><label>Access Key</label><input class="form-control" name="access_key" value="<?php echo htmlspecialchars($qiniuSettings['accessKey'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off"></div>
                <div class="form-group col-md-6"><label>Secret Key</label><input class="form-control" type="password" name="secret_key" value="<?php echo htmlspecialchars($qiniuSettings['secretKey'], ENT_QUOTES, 'UTF-8'); ?>" autocomplete="new-password"></div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6"><label>Bucket</label><input class="form-control" name="bucket" value="<?php echo htmlspecialchars($qiniuSettings['bucket'], ENT_QUOTES, 'UTF-8'); ?>"></div>
                <div class="form-group col-md-6"><label>绑定域名</label><input class="form-control" type="url" name="domain" value="<?php echo htmlspecialchars($qiniuSettings['domain'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://img.example.com"></div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6"><label>上传区域接口</label><input class="form-control" type="url" name="upload_url" value="<?php echo htmlspecialchars($qiniuSettings['uploadUrl'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://upload.qiniup.com"></div>
              </div>
              <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save"></i> 保存对象存储配置</button>
            </form>
          </div>
        </div>
          
        
        
        
        
      </div>
      
    </main>
    <!--End 页面主要内容-->
  </div>
</div>

<script type="text/javascript" src="./assets/js/jquery-3.7.1.min.js"></script>

<script type="text/javascript" src="./assets/js/bootstrap-4.6.2.bundle.min.js"></script>
<script type="text/javascript" src="./assets/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="./assets/js/main.min.js?v=<?php echo filemtime(__DIR__ . '/assets/js/main.min.js'); ?>"></script>
<script>
document.querySelectorAll('.image-setting-form').forEach(function(form){
    var fileInput = form.querySelector('input[type="file"]');
    var urlInput = form.querySelector('.image-url-input');
    var fileName = form.querySelector('.image-file-name');
    var sourceMode = form.querySelector('.source-mode-input');
    var preview = form.parentNode.querySelector('.image-preview');
    fileInput.addEventListener('change', function(){
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;
        urlInput.value = '';
        sourceMode.value = 'upload';
        fileName.textContent = file.name;
        var reader = new FileReader();
        reader.onload = function(event){ preview.innerHTML = '<img src="' + event.target.result + '" alt="上传预览">'; };
        reader.readAsDataURL(file);
    });
    urlInput.addEventListener('input', function(){
        if (urlInput.value.trim() !== '') {
            fileInput.value = '';
            sourceMode.value = 'url';
        }
    });
});
</script>
</body>
</html>
