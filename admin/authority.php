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
?><!DOCTYPE html>
<html lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="keywords" content="<?php echo $name;?>">
<meta name="description" content="<?php echo $name . " ," . $subtitle;?>">
<meta name="author" content="<?php echo $name;?>">
<title>权限设置 - Seb朋友圈系统</title>
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
      
      <div class="container-fluid p-t-15">
        
        
        
        <div class="row">
            <div class="col-lg-12">
            <div class="card">
              <div class="card-header"><div class="card-title">权限配置</div></div>
              <div class="card-body">
                <form action="./api/adminupdata.php" method="post" enctype="multipart/form-data" class="form-horizontal" autocomplete="off">
                    <?php require_once __DIR__ . '/../includes/life-security.php'; ?><input type="hidden" name="csrf_token" value="<?php echo life_csrf_token(); ?>">
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-access-point-network" style="margin-right: 5px;"></i>站点开关</label>
                        <select class="form-control" name="develop_zt" <?php 
if ($zt == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($zt == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($zt == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-account-plus" style="margin-right: 5px;"></i>用户注册</label>
                        <select class="form-control" name="develop_reg" <?php 
if ($regqx == -1) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($regqx == -1) {
	?><option value="-1">关闭</option>
                               <option value="0">开启</option><?php 
} elseif ($regqx == 0) {
	?><option value="0">开启</option>
                                <option value="-1">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-account-arrow-left" style="margin-right: 5px;"></i>前台登录</label>
                        <select class="form-control" name="develop_qlogin" <?php 
if ($loginkg == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($loginkg == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($loginkg == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-link-variant" style="margin-right: 5px;"></i>显示友链</label>
                        <select class="form-control" name="develop_lnk" <?php 
if ($lnkzt == 1) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($lnkzt == 0) {
	?><option value="0">开启</option>
                                <option value="1">关闭</option><?php 
} elseif ($lnkzt == 1) {
	?><option value="1">关闭</option>
                               <option value="0">开启</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-telegram" style="margin-right: 5px;"></i>用户发布</label>
                        <select class="form-control" name="develop_fowz" <?php 
if ($kqsy == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($kqsy == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($kqsy == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-feather" style="margin-right: 5px;"></i>前台发布</label>
                        <select class="form-control" name="develop_qfowz" <?php 
if ($ptpfan == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($ptpfan == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($ptpfan == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-reddit" style="margin-right: 5px;"></i>匿名发布</label>
                        <select class="form-control" name="develop_nmfowz" <?php 
if ($notname == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($notname == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($notname == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-rotate-orbit" style="margin-right: 5px;"></i>图片压缩</label>
                        <select class="form-control" name="develop_imgys" <?php 
if ($imgpres == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($imgpres == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($imgpres == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-comment-processing" style="margin-right: 5px;"></i>游客评论</label>
                        <select class="form-control" name="develop_vispl" <?php 
if ($viscomm == -1) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($viscomm == -1) {
	?><option value="-1">关闭</option>
                               <option value="0">开启</option><?php 
} elseif ($viscomm == 0) {
	?><option value="0">开启</option>
                                <option value="-1">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-comment-question" style="margin-right: 5px;"></i>评论审核</label>
                        <select class="form-control" name="develop_plsh" <?php 
if ($comaud == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($comaud == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($comaud == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-script-text" style="margin-right: 5px;"></i>文章审核</label>
                        <select class="form-control" name="develop_wzsh" <?php 
if ($ptpaud == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($ptpaud == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($ptpaud == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-dlna" style="margin-right: 5px;"></i>跨域资源</label>
                        <select class="form-control" name="develop_kyurl" <?php 
if ($rosdomain == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($rosdomain == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($rosdomain == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-islam" style="margin-right: 5px;"></i>日夜模式</label>
                        <select class="form-control" name="develop_ryms" <?php 
if ($daymode == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($daymode == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($daymode == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-arrow-up-bold-circle" style="margin-right: 5px;"></i>回到顶部</label>
                        <select class="form-control" name="develop_gotop" <?php 
if ($gotop == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($gotop == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($gotop == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="home-notification-enabled"><i class="mdi mdi-bell-outline" style="margin-right:5px;"></i>首页通知铃铛</label>
                        <select class="form-control" id="home-notification-enabled" name="home_notification_enabled">
                            <option value="1" <?php echo ($lifeConfig['home_notification_enabled'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                            <option value="0" <?php echo ($lifeConfig['home_notification_enabled'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                        </select>
                        <small class="text-muted">隐藏后，已启用的发布相机会占用通知铃铛的位置。</small>
                    </div>
                    <div class="form-group">
                        <label><i class="mdi mdi-magnify" style="margin-right: 5px;"></i>搜索功能</label>
                        <select class="form-control" name="develop_so" <?php 
if ($search == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($search == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($search == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-adchoices" style="margin-right: 5px;"></i>视频自动播放</label>
                        <select class="form-control" name="develop_videoplay" <?php 
if ($videoauplay == 0) {
	echo "style=\"color:red\"";
}
?>>
                            <?php 
if ($videoauplay == 0) {
	?><option value="0">关闭</option>
                               <option value="1">开启</option><?php 
} elseif ($videoauplay == 1) {
	?><option value="1">开启</option>
                                <option value="0">关闭</option><?php 
}
?>                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="mdi mdi-image" style="margin-right: 5px;"></i>大图/视频圆角</label>
                        <select class="form-control" name="develop_piccir"<?php echo (string) $filterpiccir === '0' ? ' style="color:red"' : ''; ?>>
                            <option value="0"<?php echo (string) $filterpiccir === '0' ? ' selected' : ''; ?>>关闭</option>
                            <option value="1"<?php echo (string) $filterpiccir === '1' ? ' selected' : ''; ?>>开启</option>
                        </select>
                    </div>
                  
                   <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode" style="color: var(--admin-text);">站点开关</label>
                      <div class="controls-box clearfix">
                          <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_1" name="develop_mode" class="custom-control-input" value="0" <--?php
                          if ($zt == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_1">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_2" name="develop_mode" class="custom-control-input" value="1" <-?php
                          if ($zt == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_2">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后网站将不能访问,后台管理不受影响</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode2" style="color: var(--admin-text);">用户注册</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_03" name="develop_mode2" class="custom-control-input" value="-1" <-?php
                          if ($regqx == -1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_03">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_04" name="develop_mode2" class="custom-control-input" value="0" <-?php
                          if ($regqx == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_04">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后新用户不可再注册账号</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode8" style="color: var(--admin-text);">前台登录</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_015" name="develop_mode8" class="custom-control-input" value="0" <-?php
                          if ($loginkg == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_015">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_016" name="develop_mode8" class="custom-control-input" value="1" <-?php
                          if ($loginkg == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_016">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后前台将不会显示登录按钮</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode1" style="color: var(--admin-text);">显示友链</label>
                      <div class="controls-box clearfix">
                          <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_01" name="develop_mode1" class="custom-control-input" value="1" <-?php
                          if ($lnkzt == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_01">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_02" name="develop_mode1" class="custom-control-input" value="0" <-?php
                          if ($lnkzt == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_02">开启</label>
      	              </div>
                        
                      </div>
                      <code style="color: var(--admin-muted)">关闭后前台将不会显示友链列表</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode3" style="color: var(--admin-text);">用户发布</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_05" name="develop_mode3" class="custom-control-input" value="0" <-?php
                          if ($kqsy == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_05">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_06" name="develop_mode3" class="custom-control-input" value="1" <-?php
                          if ($kqsy == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_06">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后只有管理员才能发布文章</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode7" style="color: var(--admin-text);">前台发布</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_013" name="develop_mode7" class="custom-control-input" value="0" <-?php
                          if ($ptpfan == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_013">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_014" name="develop_mode7" class="custom-control-input" value="1" <-?php
                          if ($ptpfan == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_014">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后前台将不会显示发布文章按钮</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode9" style="color: var(--admin-text);">匿名发布</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_017" name="develop_mode9" class="custom-control-input" value="0" <-?php
                          if ($notname == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_017">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_018" name="develop_mode9" class="custom-control-input" value="1" <-?php
                          if ($notname == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_018">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后将不允许匿名发布文章</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode10" style="color: var(--admin-text);">图片压缩</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_021" name="develop_mode10" class="custom-control-input" value="0" <-?php
                          if ($imgpres == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_021">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_022" name="develop_mode10" class="custom-control-input" value="1" <-?php
                          if ($imgpres == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_022">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后发布文章时上传的图片将不会被压缩</code>
                    </div-->
                    
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode4" style="color: var(--admin-text);">游客评论</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_07" name="develop_mode4" class="custom-control-input" value="-1" <-?php
                          if ($viscomm == -1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_07">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_08" name="develop_mode4" class="custom-control-input" value="0" <-?php
                          if ($viscomm == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_08">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后只能登录账号进行发布评论</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode5" style="color: var(--admin-text);">评论审核</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_09" name="develop_mode5" class="custom-control-input" value="0" <-?php
                          if ($comaud == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_09">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_010" name="develop_mode5" class="custom-control-input" value="1" <-?php
                          if ($comaud == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_010">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后所有人都可随意发布评论</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 20px;">
                      <label for="develop_mode6" style="color: var(--admin-text);">文章审核</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_011" name="develop_mode6" class="custom-control-input" value="0" <-?php
                          if ($ptpaud == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_011">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_012" name="develop_mode6" class="custom-control-input" value="1" <-?php
                          if ($ptpaud == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_012">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后所有人都可随意发布文章</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode11" style="color: var(--admin-text);">跨域资源</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_023" name="develop_mode11" class="custom-control-input" value="0" <-?php
                          if ($rosdomain == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_023">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_024" name="develop_mode11" class="custom-control-input" value="1" <-?php
                          if ($rosdomain == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_024">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">用于加载一些有跨域限制、防盗链的远程资源链接,注意:当打开此功能时会出现 百度统计、cnzz等统计网站可能会导致失效，并且当七牛云设置了Referer防盗链且不为空时，将不可访问等,请按需求开启</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode12" style="color: var(--admin-text);">日夜模式</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_025" name="develop_mode12" class="custom-control-input" value="0" <-?php
                          if ($daymode == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_025">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_026" name="develop_mode12" class="custom-control-input" value="1" <-?php
                          if ($daymode == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_026">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后将不会在前台右下角悬浮显示日夜模式切换按钮</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode13" style="color: var(--admin-text);">回到顶部</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_027" name="develop_mode13" class="custom-control-input" value="0" <-?php
                          if ($gotop == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_027">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_028" name="develop_mode13" class="custom-control-input" value="1" <-?php
                          if ($gotop == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_028">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后将不会在前台右下角悬浮显示回到顶部按钮</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;margin-bottom: 0;border-bottom: 1px solid rgba(77, 82, 89, 0.07);">
                      <label for="develop_mode14" style="color: var(--admin-text);">搜索功能</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_029" name="develop_mode14" class="custom-control-input" value="0" <-?php
                          if ($search == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_029">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_030" name="develop_mode14" class="custom-control-input" value="1" <-?php
                          if ($search == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_030">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后将不会显示搜索按钮</code>
                    </div-->
                    
                    <!--div class="bg-light" style="padding: 10px 10px;margin-bottom: 20px;">
                      <label for="develop_mode15" style="color: var(--admin-text);">视频自动播放</label>
                      <div class="controls-box clearfix">
                        <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_031" name="develop_mode15" class="custom-control-input" value="0" <-?php
                          if ($videoauplay == 0){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_031">关闭</label>
      	              </div>
      	              <div class="custom-control custom-radio custom-control-inline">
      	                <input type="radio" id="develop_mode_032" name="develop_mode15" class="custom-control-input" value="1" <-?php
                          if ($videoauplay == 1){echo 'checked=""';}?>>
      	                <label class="custom-control-label" for="develop_mode_032">开启</label>
      	              </div>
                      </div>
                      <code style="color: var(--admin-muted)">关闭后将不会自动播放文章中的视频</code>
                    </div-->
                    
                    <input type="hidden" value="wzqxp" name="lx">
                    
                    <button type="submit" class="btn btn-primary">保存</button>
                </form>
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
