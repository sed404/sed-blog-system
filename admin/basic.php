<?php
require_once __DIR__ . '/require-login.php';

//decode by nige112
$iteace = "1";
if (is_file("../config.php")) {
	include_once "../config.php";
}
include "../api/wz.php";
$siteDomain = '';
$defaultTheme = 'light';
$snowEnabled = '0';
$snowResult = $conn->query("SELECT text FROM configx WHERE title='snow_enabled' LIMIT 1");
if ($snowResult && ($snowRow = $snowResult->fetch_assoc())) $snowEnabled = (string) $snowRow['text'];
$siteDomainResult = @$conn->query("SELECT text FROM configx WHERE title='site_domain' LIMIT 1");
if ($siteDomainResult && ($siteDomainRow = $siteDomainResult->fetch_assoc())) $siteDomain = (string) $siteDomainRow['text'];
$themeResult = @$conn->query("SELECT text FROM configx WHERE title='default_theme' LIMIT 1");
if ($themeResult && ($themeRow = $themeResult->fetch_assoc())) $defaultTheme = (string) $themeRow['text'];
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
<title>基础设置 - Seb朋友圈系统</title>
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
              <header class="card-header"><div class="card-title">网站配置</div></header>
              <div class="card-body">
                
                <form class="form-group" action="./api/adminupdata.php" method="post" enctype="multipart/form-data">
                  <?php require_once __DIR__ . '/../includes/life-security.php'; ?><input type="hidden" name="csrf_token" value="<?php echo life_csrf_token(); ?>">
                  <div class="form-group"><label for="snow-enabled">前台飘雪</label><select id="snow-enabled" class="form-control" name="snow_enabled"><option value="0" <?php echo $snowEnabled==='0'?'selected':''; ?>>关闭</option><option value="1" <?php echo $snowEnabled==='1'?'selected':''; ?>>开启</option></select><small class="form-text text-muted">启用后前台显示飘雪；系统设置减少动态效果时自动关闭。</small></div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站标题</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $name;?>" minlength="1" placeholder="" name="wzbt">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站描述</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $subtitle;?>" minlength="1" placeholder="可定义多个描述词以逗号分开" name="wzms">
                  </div>
                  <div class="form-group">
                    <label>前台默认日夜模式</label>
                    <select class="form-control" name="default_theme"><option value="light" <?php echo $defaultTheme==='light'?'selected':''; ?>>日间模式</option><option value="dark" <?php echo $defaultTheme==='dark'?'selected':''; ?>>夜间模式</option></select>
                    <small class="text-muted">用户未手动切换时使用此默认值。</small>
                  </div>
                  <div class="form-group"><label>本站域名</label><input type="url" class="form-control" name="site_domain" value="<?php echo htmlspecialchars($siteDomain,ENT_QUOTES,'UTF-8'); ?>" placeholder="https://example.com"><small class="text-muted">用于迁移和修复站内资源链接。</small></div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">管理员账号</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $glyadmin;?>" minlength="5" maxlength="32" placeholder="" name="wzglyzh" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站首页签名</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $sign;?>" minlength="1" placeholder="" name="wzqm">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">默认文章显示数量</label>
                    <input type="number" class="form-control" id="exampleFormControlInput1" value="<?php echo $essgs;?>" minlength="1" maxlength="2"placeholder="" name="wzmrxw">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">默认评论显示数量</label>
                    <input type="number" class="form-control" id="exampleFormControlInput1" value="<?php echo $commgs;?>" minlength="1" maxlength="2"placeholder="" name="wzmrxp">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">备案号</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $beian;?>" maxlength="500" placeholder='示例:<a href="http://beian.miit.gov.cn/">蜀ICP备20220202号</a>' name="wzba">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站字体(TTF字体链接)</label>
                    <input type="url" class="form-control" id="exampleFormControlInput1" value="<?php echo $scfont;?>" maxlength="500" placeholder="" name="wzzit">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站版权</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $copyright;?>" maxlength="50" placeholder="" name="wzcopy">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">网站访问密码(不设置请留空)</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php echo $pagepass;?>" maxlength="100" placeholder="" name="pagepass">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlInput1">敏感词过滤(用 || 符号分开)</label>
                    <input type="text" class="form-control" id="exampleFormControlInput1" value="<?php 
if ($filtertext != "") {
	$data = json_decode($filtertext, true);
	$result = implode("||", $data);
	echo $result;
}
?>" placeholder="" name="mask">
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlSelect1">悬浮音乐播放器样式</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="wzmusplay">
                        <?php 
if ($musplay == 0) {
	?><option value="0">经典</option>
                            <option value="1">新颖</option><?php 
} elseif ($musplay == 1) {
	?><option value="1">新颖</option>
                            <option value="0">经典</option><?php 
} else {
	?><option value="">设置错误,请重新设置</option><option value="0">经典</option>
                            <option value="1">新颖</option><?php 
}
?>                      
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlSelect1">注册验证邮箱(需先配置邮箱)</label>
                    <select class="form-control" id="exampleFormControlSelect1" name="regverify">
                        <?php 
if ($regverify == 0) {
	?><option value="0">不验证</option>
                            <option value="1">验证</option><?php 
} elseif ($regverify == 1) {
	?><option value="1">验证</option>
                            <option value="0">不验证</option><?php 
} else {
	?><option value="">设置错误,请重新设置</option><option value="0">不验证</option>
                            <option value="1">验证</option><?php 
}
?>                    </select>
                  </div>
                  <div class="form-group">
                    <label for="exampleFormControlTextarea1">网站音乐(一行一个,不设置则留空)</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="" name="wzmusic" spellcheck="false"><?php echo $wzmusic;?></textarea>
                    <small class="help-block">直链音乐格式：<code>歌名|链接</code>，建议每首歌单独一行；MP3、FLAC 等链接均可填写。</small><br>
                    <small class="help-block">网易云音乐格式(请填写前缀:网易音乐+音乐id)：<code>网易音乐|25906124</code></small>
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleFormControlTextarea1">自定义CSS</label>
                    <textarea class="form-control"  style="min-height:150px" id="exampleFormControlTextarea1" placeholder="" name="cucss" spellcheck="false"><?php echo $filtercucss;?></textarea>
                    <small class="help-block">请填写自定义CSS内容，不要填写style标签，请直接写代码</small>
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleFormControlTextarea1">自定义JS</label>
                    <textarea class="form-control"  style="min-height:150px" id="exampleFormControlTextarea1" placeholder="" name="cujs" spellcheck="false"><?php echo $filtercujs;?></textarea>
                    <small class="help-block">请填写自定义JS内容，例如网站统计等，不要填写script标签，请直接写代码</small>
                  </div>
                  
                  <input type="hidden" value="wzpz" name="lx">
                  
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
    function kgkz(){
        var element = event.target;
        var isChecked = element.checked;
        if(isChecked){
            console.log("选择")
            element.checked = true;
            var ele = window.event.srcElement.lang;
            var ele2 = window.event.srcElement.value;
            element.value = ele;
            element.lang = ele2;
        }else{
            console.log("未选择")
            element.checked = false;
            var ele = window.event.srcElement.lang;
            var ele2 = window.event.srcElement.value;
            element.value = ele;
            element.lang = ele2;
        }
        
    }
</script>
</body>
</html>
