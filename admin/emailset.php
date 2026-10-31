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
<title>邮箱设置 - Seb朋友圈系统</title>
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
              <header class="card-header"><div class="card-title">邮箱配置</div></header>
              <div class="card-body">
                
                <form class="form-horizontal" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="exampleFormControlInput1">邮箱服务器地址</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $emydz;?>" minlength="1" placeholder="邮箱服务器地址(例:smtp.qq.com)" name="emdz">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">SSL加密方式</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $emssl;?>" minlength="1" placeholder="SSL加密方式(例:ssl)" name="emssl">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">SMTP端口</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $emduk;?>" minlength="1" placeholder="SMTP端口(例:465)" name="emdk">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">邮箱授权码</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $emkey;?>" minlength="1" placeholder="输入邮箱授权码" name="emkey">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">SMTP账号</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $emzh;?>" minlength="1" placeholder="输入smtp账号(邮箱号)" name="emfzh">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">发送邮箱</label>
                    <input type="email" class="form-control" id="exampleFormControlInput1" value="<?php echo $emfs;?>" minlength="1" placeholder="发送邮箱的账号" name="emfsy">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">发送者名字</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $emfszm;?>" minlength="1" placeholder="发送者名字" name="emfsm">
                  </div>
                  <input type="hidden" value="emailset" name="lx">
                  
                  <button type="submit" class="btn btn-primary">保存</button>
                  <a class="btn btn-success btn-primary" href="./api/emailtest.php">测试</a>
                  
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
