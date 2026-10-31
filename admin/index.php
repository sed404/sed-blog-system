<?php
require_once __DIR__ . '/require-login.php';

//decode by 敏感又自卑
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
		exit("<script language=\"JavaScript\">;alert(\"您账号登陆令牌已经失效请重新登陆！\");location.href=\"./login.php\";</script>;");
	}
} else {
	exit("<script language=\"JavaScript\">;alert(\"您的账号未获取后台权限哦！\");location.href=\"../index.php\";</script>;");
}
$Query = "Select count(*) as AllNum from user";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$usercount = $b["AllNum"];
$Query = "Select count(*) as AllNum from essay";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$essaycount = $b["AllNum"];
$Query = "Select count(*) as AllNum from comm";
$a = mysqli_query($conn, $Query);
$b = mysqli_fetch_assoc($a);
$commcount = $b["AllNum"];
?><!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="<?php echo $name;?>">
<meta name="description" content="<?php echo $name . " ," . $subtitle;?>">
<meta name="author" content="<?php echo $name;?>">
<title>后台首页 - Seb朋友圈系统</title>
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
      <div class="container-fluid p-t-15"><div class="admin-welcome"><h1>后台首页</h1><p>欢迎回来，<?php echo admin_ui_escape($user_name ?: '管理员'); ?>。在这里管理内容、用户与系统设置。</p></div>
        <div class="row">
          <div class="col-md-6 col-xl-3">
            <div class="card bg-primary text-white">
              <div class="card-body clearfix">
                <div class="flex-box">
                  <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account fs-22"></i></span>
                  <span class="fs-22 lh-22"><?php echo $usercount;?></span>
                </div>
                <div class="text-right">用户总数</div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 col-xl-3">
            <div class="card bg-danger text-white">
              <div class="card-body clearfix">
                <div class="flex-box">
                  <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-script-text fs-22"></i></span>
                  <span class="fs-22 lh-22"><?php echo $essaycount;?></span>
                </div>
                <div class="text-right">文章总数</div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 col-xl-3">
            <div class="card bg-success text-white">
              <div class="card-body clearfix">
                <div class="flex-box">
                  <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-comment-multiple fs-22"></i></span>
                  <span class="fs-22 lh-22"><?php echo $commcount;?></span>
                </div>
                <div class="text-right">评论总数</div>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 col-xl-3">
            <div class="card bg-purple text-white">
              <div class="card-body clearfix">
                <div class="flex-box">
                  <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-cloud-check fs-22"></i></span>
                  <span class="fs-22 lh-22">
                  正常                  </span>
                </div>
                <div class="text-right">服务器状态</div>
              </div>
            </div>
          </div>
        </div>

        
        
        
              <?php 
if (defined("auth_bdyz")) {
	$valuelixauth = constant("auth_bdyz");
	if ($valuelixauth == "lixianauth") {
		?><div class="row">
                            <div class="col-md-12 col-lg-12">
                            <div class="card">
                            <div class="callout callout-danger"><?php 
		$formattedDate = substr($auth_keytm, 0, 4) . "-" . substr($auth_keytm, 4, 2) . "-" . substr($auth_keytm, 6, 2);
		echo "授权验证失败，由于检测到您在此之前已经授权，现已启动离线授权，您的域名[<span style=\"color: var(--admin-success);\">" . $_SERVER["HTTP_HOST"] . "</span>]离线授权将会在<span style=\"color: var(--admin-primary);\">" . $formattedDate . "</span>过期";
		?></div>
                            </div>
                          </div>
                        </div><?php 
	}
}
?>              
        
        
        <div class="row">
          <div class="col-md-12 col-lg-12">
              
              
              <div class="card">
              <header class="card-header">
                <div class="card-title">Seb朋友圈系统</div>
                <ul class="card-actions">
                  <li><a href="#!" class="card-btn-close"><i class="mdi mdi-close"></i></a></li>
                  <li><a href="#!" class="card-btn-slide rotate-180"><i class="mdi mdi-chevron-up"></i></a></li>
                </ul>
              </header>
              <div class="card-body">
                <h5>Seb朋友圈系统</h5>
                <p>Seb朋友圈系统基于 <a href="https://github.com/yunxi0723/yxos" target="_blank" rel="noopener noreferrer">yunxi0723/yxos</a> 重构。原项目是一套仿微信朋友圈程序，提供动态发布、图片与视频、评论点赞、用户管理和夜间模式等基础能力。</p>
                <p class="mb-1">原版源码及原始实现版权归 <a href="https://github.com/yunxi0723" target="_blank" rel="noopener noreferrer">yunxi0723</a> 与原项目贡献者所有，并继续遵循原项目 GPL-3.0 许可证。</p>
                <p class="mb-1">Sed 重构、功能扩展与维护：<a href="https://github.com/sed404" target="_blank" rel="noopener noreferrer">Sed</a>。重构版本新增作品记录、画廊、网站管理、备忘、对象存储、AI 文案与迁移修复工具。</p>
                <p class="mb-0">本项目保留原作者署名与许可证声明；新增及重构代码版权归对应开发者所有。个人主页：<a href="https://one.mcxk.cc/" target="_blank" rel="noopener noreferrer">https://one.mcxk.cc/</a>。</p>
              </div>
              
            </div>
            
            
            
          </div>
        </div>
        
        
        
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <header class="card-header"><div class="card-title">后台通告</div></header>
              <div class="card-body" style="padding: 0px;min-height: 248px;">
                  <div class="card-body">         
          <p>1：欢迎您使用后台管理系统</p>
        <p>2：这里是您管理朋友圈的内容和用户的重要平台，请确保您的操作符合网站政策和安全规范！</p>
                </div>
              </div>
            </div>
          </div>
          
          
            <div class="col-md-6"> 
            <div class="card">
              <div class="card-header">
                <div class="card-title">运行环境</div>
              </div>
              <div class="card-body">
<dl class="admin-system-info">
<dt>PHP 版本</dt><dd><?php echo admin_ui_escape(PHP_VERSION); ?></dd>
<dt>最大上传文件</dt><dd><?php echo admin_ui_escape(ini_get('upload_max_filesize')); ?></dd>
<dt>POST 请求上限</dt><dd><?php echo admin_ui_escape(ini_get('post_max_size')); ?></dd>
<dt>服务器系统</dt><dd><?php echo admin_ui_escape(PHP_OS); ?></dd>
</dl></div>
            </div>
          </div>
        </div>
        

        
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
</body>
</html>
