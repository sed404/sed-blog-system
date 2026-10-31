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
$filew = addslashes(htmlspecialchars($_GET["filew"] ?? ""));
if ($filew != "") {
	if (is_file($filew)) {
		$status = unlink($filew);
		if ($status) {
			$ym = addslashes(htmlspecialchars($_GET["page"] ?? ""));
			if ($ym != "") {
				header("Location: ./rm.php?page=" . addslashes(htmlspecialchars($_GET["page"] ?? "")) . "");
			} else {
				header("Location: ./rm.php");
			}
		} else {
			exit("<script language=\"JavaScript\">;alert(\"文件删除失败,请重试!\");location.href=\"./rm.php\";</script>");
		}
	} else {
		header("Location: ./rm.php");
	}
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
<title>资源列表 - Seb朋友圈系统</title>
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
<style>.admin-ui .admin-resource-grid {display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:12px;align-items:start;justify-items:center;}</style>
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
            
            <div style="width: 100%;height: 100%;display: none;justify-content: center;align-items: center;z-index: 99;position: fixed;top: 0;left: 0;background: rgba(0,0,0,.45);" id="xxk" onclick="gbxxk()">
          <div class="alert btn-default alert-dismissible" role="alert" style="max-width: 90%;min-width: 40%;" onclick="fzmp()">
                  <button type="button" class="close" aria-label="Close"><span aria-hidden="true" onclick="gbxxk()">×</span></button>
                  <h6 class="alert-heading" style="word-wrap:break-word;word-break:break-all;overflow: hidden;color: var(--admin-secondary);" id="wgm">-</h6>
                  <span style="word-wrap:break-word;word-break:break-all;overflow: hidden;color: var(--admin-secondary);" id="time">-</span><br>
                  <span style="word-wrap:break-word;word-break:break-all;overflow: hidden;color: var(--admin-secondary);" id="wgwz">-</span><br>
                  <span style="word-wrap:break-word;word-break:break-all;overflow: hidden;color: var(--admin-secondary);" id="size">-</span><br>
                  <span style="word-wrap:break-word;word-break:break-all;overflow: hidden;color: var(--admin-secondary);" id="wlx">-</span><br>
                  <img class="card-img-top" src="" alt="文件预览" style="margin-top: 10px;max-width: 200px;max-height: 200px;min-width: 100px;min-height: 100px;object-fit: cover;border-radius: 4px;" id="dwgyl">
                  
                  <iframe src="" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen" frameBorder="" style="width: 100%;height:280px;border-radius: 4px;margin-top: 10px;" id="sh-video"></iframe>

                  <p>
                      
                    <button type="button" class="btn btn-danger" style="margin-top: 15px;" onclick="delew()" id="delew" lang="">删除文件</button>
                    <button type="button" class="btn btn-outline-secondary" style="margin-top: 15px;" onclick="copyurl()" id="copyurl" lang="123">复制链接</button>
                  </p>
                </div>
                </div>
                
                
          <div class="col-lg-12">
            <div class="card">
                
              <header class="card-header"><div class="card-title" onclick="window.location = './rm.php'" style="cursor:pointer;">资源列表</div>
              <form class="search-bar ml-md-auto" method="get" action="./rm.php" role="form">
                  <!--input type="hidden" name="search_field" id="search-field" value="title"-->
                  <div class="input-group ml-md-auto">
                    <div class="input-group-prepend">
                      <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">所有文件</button>
                      <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="?type=img" data-field="title">图片</a>
                        <a class="dropdown-item" href="?type=video" data-field="cat_name">视频</a>
                      </div>
                    </div>
                    <input type="text" class="form-control" name="keyword" placeholder="请输入名称">
                  </div>
                </form>
              </header>
              
              <div class="card-body admin-resource-grid">

<?php 
$type = addslashes(htmlspecialchars($_GET["type"] ?? ""));
$keyword = addslashes(htmlspecialchars($_GET["keyword"] ?? ""));
$exclude = ["php", "html", "htm"];
$perPage = 50;
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$keyword = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
$folderPath = "../upload/";
if ($type == "video") {
	$files = glob($folderPath . "*.{mp4}", GLOB_BRACE);
} elseif ($type == "img") {
	$files = glob($folderPath . "*.{jpg,png,jpeg,gif,svg,webp,bmp}", GLOB_BRACE);
} else {
	$files = glob($folderPath . "*.{jpg,png,jpeg,gif,svg,webp,bmp,mp4}", GLOB_BRACE);
}
foreach ($exclude as $ext) {
	$files = array_diff($files, glob($folderPath . "*." . $ext, GLOB_BRACE));
}
if ($keyword != "") {
	$files = array_filter($files, function ($file) use($keyword) {
		return strpos(basename($file), $keyword) !== false;
	});
}
usort($files, function ($a, $b) {
	return filemtime($b) - filemtime($a);
});
$totalFiles = count($files);
$totalPages = ceil($totalFiles / $perPage);
$maxPages = $totalPages;
$start = ($page - 1) * $perPage;
$end = min($start + $perPage, $totalFiles);
$pageFiles = array_slice($files, $start, $perPage);
$pagination = "";
for ($i = 1; $i <= $totalPages; $i++) {
	if ($i == $page) {
		$pagination .= "<span style=\"color: red;\">" . $i . "</span> ";
	} else {
		$pagination .= "<a href=\"?page=" . $i . "&keyword=" . urlencode($keyword) . "\">" . $i . "</a> ";
	}
}
if ($pagination != "") {
	$pagination = substr($pagination, 0, strpos($pagination, " ", 4)) . "... " . substr($pagination, strrpos($pagination, " ", -4));
}
echo "  

    ";
$wgsl = 0;
foreach ($pageFiles as $file) {
	$wgsl++;
	$yfilna = basename($file);
	$filna = "../upload/" . basename($file);
	$size = transf_byte(filesize($filna));
	$time = date("Y-m-d H:i:s", filemtime($filna));
	$wlx = end(explode(".", $filna));
	$wjurl = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . str_replace("../", "/", $filna);
	$query_essay = "SELECT ptpimag, ptpvideo FROM essay";
	$result_essay = mysqli_query($conn, $query_essay);
	$query_user = "SELECT img, homeimg FROM user";
	$result_user = mysqli_query($conn, $query_user);
	$targetImage = $yfilna;
	$found = false;
	while ($row_essay = mysqli_fetch_assoc($result_essay)) {
		if (strpos($row_essay["ptpimag"], $targetImage) !== false || strpos($row_essay["ptpvideo"], $targetImage) !== false) {
			$found = true;
			break;
		}
	}
	while ($row_user = mysqli_fetch_assoc($result_user)) {
		if (strpos($row_user["img"], $targetImage) !== false || strpos($row_user["homeimg"], $targetImage) !== false) {
			$found = true;
			break;
		}
	}
	if ($found) {
		$shiytitle = "";
	} else {
		$shiytitle = "<span style=\"width: fit-content;height: fit-content;grid-column: 1;grid-row: 1;z-index: 1;position: absolute;top: 8px;left: 8px;font-size: 12px;color: rgb(255 255 255);pointer-events: none;background: rgb(0,0,0,0.28);padding: 1px 3px;border-radius: 4px;\">未使用</span>";
	}
	if ($wlx != "mp4") {
		echo "<div style=\"position:relative;\">
             <img class=\"mr-3 rounded\" alt=\"" . $yfilna . "\" src=\"" . $filna . "\" data-holder-rendered=\"true\" style=\"width: 100vw;height: 150px;object-fit: cover;padding: 0px;cursor: zoom-in;\" data-wgm=\"" . $yfilna . "\" data-size=\"" . $size . "\" data-time=\"" . $time . "\" data-wlx=\"" . $wlx . "\" data-wgwz=\"/upload/\" data-wjurl=\"" . $wjurl . "\" data-delewg=\"../upload/" . $yfilna . "\" onclick=\"wgxx()\" id=\"wgx-" . $wgsl . "\" >
             " . $shiytitle . "
             </div>";
	} else {
		echo "<div style=\"position:relative;\">
             <video class=\"img-thumbnail\" src=\"" . $filna . "\" playsinline=\"\" webkit-playsinline=\"\" preload=\"metadata\" autoplay=\"\" muted=\"\" loop=\"\" style=\"width: 150px;max-height: 150px;padding: 0;border: 0px solid #ffffff00;max-height: 150px;background: black;\" data-wgm=\"" . $yfilna . "\" data-size=\"" . $size . "\" data-time=\"" . $time . "\" data-wlx=\"" . $wlx . "\" data-wgwz=\"/upload/\" data-wjurl=\"" . $wjurl . "\" data-delewg=\"../upload/" . $yfilna . "\" onclick=\"wgxx()\" id=\"wgx-" . $wgsl . "\"></video>
             " . $shiytitle . "
             </div>";
	}
}
?> 

              </div>
               
              <?php 
echo "<div class=\"fixed-table-pagination callout\">
                  <div class=\"float-left pagination-detail\" style=\"margin-bottom: 10px;\">
                      <span class=\"pagination-info\">
                          每页显示" . $perPage . "条数据，总共" . $totalFiles . "条数据
                      </span>
                  </div>
                                    <div class=\"float-right pagination\">
                      <ul class=\"pagination\">";
if (addslashes(htmlspecialchars($_GET["type"] ?? "")) != "") {
	$typez = "type=" . addslashes(htmlspecialchars($_GET["type"] ?? "")) . "&";
} else {
	$typez = "";
}
if ($maxPages > 1) {
	if ($page > 1) {
		echo "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"首页\" href=\"./rm.php?" . $typez . "page=1\">首页</a></li>";
		echo "<li class=\"page-item page-pre\"><a class=\"page-link\" aria-label=\"上一页\" href=\"./rm.php?" . $typez . "page=" . ($page - 1) . "\">‹</a></li>";
	}
	$start = max(1, $page - 1);
	$end = min($page + 1, $maxPages);
	if ($start > 1) {
	}
	for ($i = $start; $i <= $end; $i++) {
		if ($i == $page) {
			echo "<li class=\"page-item active\"><a class=\"page-link\" aria-label=\"第" . $i . "页\" href=\"./rm.php?" . $typez . "page=" . $i . "\">" . $i . "</a></li>";
		} else {
			echo "<li class=\"page-item \"><a class=\"page-link\" aria-label=\"第" . $i . "页\" href=\"./rm.php?" . $typez . "page=" . $i . "\">" . $i . "</a></li>";
		}
	}
	if ($end < $maxPages) {
		?><li class="page-item disabled"><span class="page-link">...</span></li><?php 
	}
	if ($page < $maxPages) {
		echo "<li class=\"page-item page-pre\"><a class=\"page-link\" aria-label=\"下一页\" href=\"./rm.php?" . $typez . "page=" . ($page + 1) . "\">›</a></li>";
		echo "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"尾页\" href=\"./rm.php?" . $typez . "page=" . $maxPages . "\">尾页</a></li>";
	}
}
?>

                      </ul>
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
<script type="text/javascript">
    function gbxxk(){
        document.getElementById("xxk").style.display="none";
        document.getElementById("sh-video").src="";
        document.getElementById("dwgyl").src="";
    }
    function fzmp(){
        event.stopPropagation();
    }
    
    function wgxx(){
        var ele = window.event.srcElement.id;
        let wg = document.querySelector("#"+ele);
        
        document.getElementById("xxk").style.display="flex";//显示文件信息预览框
 
        document.getElementById("wgm").innerText="文件名："+wg.getAttribute("data-wgm");//文件名
        document.getElementById("size").innerText="文件大小："+wg.getAttribute("data-size");//文件大小
        document.getElementById("time").innerText="文件时间："+wg.getAttribute("data-time");//文件时间
        document.getElementById("wlx").innerText="文件类型："+wg.getAttribute("data-wlx");//文件类型
        document.getElementById("wgwz").innerText="文件位置："+wg.getAttribute("data-wgwz");//文件位置
        document.getElementById("copyurl").lang=wg.getAttribute("data-wjurl");//文件链接地址
        document.getElementById("delew").lang=wg.getAttribute("data-delewg");//文件删除地址
        
        //判断是否为视频
        if(wg.getAttribute("data-wlx") == "mp4"){
            document.getElementById("sh-video").style.display="block";//显示视频
            document.getElementById("dwgyl").style.display="none";//隐藏图片
            document.getElementById("sh-video").src='../site/library/player.php?url='+wg.getAttribute("data-delewg").replace("../","/");//文件预览
        }else{
            document.getElementById("sh-video").style.display="none";//隐藏视频
            document.getElementById("dwgyl").style.display="block";//显示图片
            document.getElementById("dwgyl").src=wg.getAttribute("data-delewg");//文件预览
        }
    }
    
    function copyurl(){
        var Url2=document.getElementById("copyurl").lang;
                var oInput = document.createElement('input');
                oInput.value = Url2;
                document.body.appendChild(oInput);
                oInput.select(); // 选择对象
                document.execCommand("Copy"); // 执行浏览器复制命令
                oInput.className = 'oInput';
                oInput.style.display='none';
                alert('复制成功');
                //禁止输入法弹出
                if (document.activeElement instanceof HTMLElement) {
                    document.activeElement.blur()
                }
            
            
    }
    
    function delew(){
        if (!confirm('确定永久删除此文件吗？此操作无法撤销。')) return;
        var button = document.getElementById('delew');
        if (button.disabled) return;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        window.location = "?page=<?php echo $page;?>" + "&filew=" + encodeURIComponent(button.lang);
    }
</script>
</body>
</html><?php 
function transf_byte($byte)
{
	$KB = 1024;
	$MB = $KB * 1024;
	$GB = $MB * 1024;
	$TB = $GB * 1024;
	if ($byte < $KB) {
		return $byte . "B";
	} elseif ($byte < $MB) {
		return round($byte / $KB, 2) . "KB";
	} elseif ($byte < $GB) {
		return round($byte / $MB, 2) . "MB";
	} elseif ($byte < $TB) {
		return round($byte / $GB, 2) . "GB";
	} else {
		return round($byte / $TB, 2) . "TB";
	}
}
