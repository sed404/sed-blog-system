<?php
require_once __DIR__ . '/require-login.php';

header('Location: ./index.php'); exit;

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
<title>友链管理 - Seb朋友圈系统</title>
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
              <header class="card-header"><div class="card-title">友链管理</div></header>
              <div class="card-body">
                <form class="form-horizontal" action="./api/linkset.php" method="post" enctype="multipart/form-data">
                  <div class="form-group" id="ylglbk">
                    <label for="exampleFormControlSelect1">选择友链</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="linkid">
                        <option value="-1">请选择友链</option>
                        <?php 
$sqllink = "SELECT * FROM link";
$resultlink = $conn->query($sqllink);
while ($rowlink = $resultlink->fetch_assoc()) {
	$linkid = $rowlink["id"];
	$linkurl = $rowlink["url"];
	$linkurls = $rowlink["urls"];
	$linkurlimg = $rowlink["urlimg"];
	echo "<option value=\"" . $linkid . "\">" . $linkurls . "</option>";
}
?>                      
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">友链名字</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="" minlength="1" placeholder="输入友链名字" name="linmz">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput2">友链图标</label>
                    <input type="text" class="form-control" id="exampleFormControlInput2" value="" minlength="1" placeholder="输入友链图标地址" name="linimg">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput3">友链地址</label>
                    <input type="text" class="form-control" id="exampleFormControlInput3" value="" minlength="1" placeholder="输入友链地址" name="linurl">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlSelect1">删除友链</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="delyl">
                      <option value="0">不删除</option>
                      <option value="-1">删除</option>
                    </select>
                  </div>
                  <input type="hidden" value="ylgl" name="lx">
                  
                  <button type="submit" class="btn btn-primary">保存</button>
                  
                </form>
                
              </div>
            </div>
            
            
            
          </div>
        </div>
        
        
        
        
        
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <header class="card-header"><div class="card-title">添加友链</div></header>
              <div class="card-body">
                
                <form class="form-horizontal" action="./api/linkset.php" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="exampleFormControlInput11">友链名字</label>
                    <input type="text" class="form-control" id="exampleFormControlInput11" value="" minlength="1" placeholder="输入友链名字" name="linmz">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput12">友链图标</label>
                    <input type="text" class="form-control" id="exampleFormControlInput12" value="" minlength="1" placeholder="输入友链图标地址" name="linimg">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput13">友链地址</label>
                    <input type="text" class="form-control" id="exampleFormControlInput13" value="" minlength="1" placeholder="输入友链地址" name="linurl">
                  </div>
                  <input type="hidden" value="ylnew" name="lx">
                  
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
<script type="text/javascript">
    let oCon = document.querySelector("#ylglbk");
    oCon.onchange = function(e){
    if(e.target.name == "linkid"){
        if(e.target.value != -1){
            // 异步对象
            var xhr = new XMLHttpRequest();
            // 设置属性
            xhr.open('post', './api/linkset.php');
            // 如果想要使用post提交数据,必须添加此行
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            // 将数据通过send方法传递
            xhr.send('lx=poln&likid='+e.target.value);
            // 发送并接受返回值
            xhr.onreadystatechange = function () {
                // 这步为判断服务器是否正确响应
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText == "") {
                        alert("未获取到数据!");
                        return;
                    }
                    //alert(xhr.responseText);
                     var obj = JSON.parse(xhr.responseText); //由JSON字符串转换为JSON对象
                     document.getElementById("exampleFormControlInput1").value=obj[0].urlbt;
                     document.getElementById("exampleFormControlInput2").value=obj[0].urlimg;
                     document.getElementById("exampleFormControlInput3").value=obj[0].url;
                }
            };
        }
    }
}

</script>
</body>
</html>
