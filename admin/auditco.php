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
<title>审核评论 - Seb朋友圈系统</title>
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
<style>
    td{
        max-width: 180px;
        word-break:keep-all;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }
</style>
<body class="admin-ui">
<!--页面loading-->
<div id="lyear-preloader" class="loading">
  <div class="ctn-preloader">
    <div class="round_spinner">
      <div class="spinner"></div>
      <img src="<?php 
if (strpos($logo, "http") !== false) {
	echo $logo;
} else {
	echo "." . $logo;
}
?>" alt="">
    </div>
  </div>
</div>
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
              <header class="card-header"><div class="card-title" onclick="window.location = './auditco.php'" style="cursor:pointer;"><?php 
if (addslashes(htmlspecialchars($_GET["type"] ?? "")) == "" || addslashes(htmlspecialchars($_GET["type"] ?? "")) == "0") {
	echo "待审核评论";
} else {
	if (addslashes(htmlspecialchars($_GET["type"] ?? "")) == "all") {
		echo "已审核评论";
	}
}
?></div>
              <form class="search-bar ml-md-auto" method="get" action="./auditco.php" role="form">
                  <!--input type="hidden" name="search_field" id="search-field" value="title"-->
                  <div class="input-group ml-md-auto">
                    <div class="input-group-prepend">
                        <?php 
if (addslashes(htmlspecialchars($_GET["type"] ?? "")) == "" || addslashes(htmlspecialchars($_GET["type"] ?? "")) == "0") {
	?><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">待审核评论</button>
                      <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="?type=all" data-field="title">已审核评论</a>
                      </div><?php 
} else {
	if (addslashes(htmlspecialchars($_GET["type"] ?? "")) == "all") {
		?><button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="search-btn">已审核评论</button>
                      <div class="dropdown-menu" style="">
                        <a class="dropdown-item" href="?type=0" data-field="title">待审核评论</a>
                      </div><?php 
	}
}
?>                      
                    </div>
                    <input type="text" class="form-control" name="keyword" placeholder="关键字搜索">
                  </div>
                </form>
              </header>
              <div class="card-body">
                
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>昵称</th>
                        <th>邮箱</th>
                        <th>评论内容</th>
                        <th>来自文章</th>
                        <th>IP</th>
                        <th>状态</th>
                        <th>时间</th>
                        <th>操作</th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php 
if (addslashes(htmlspecialchars($_GET["page"] ?? "")) != "") {
	$page = $_GET["page"];
} else {
	$page = 1;
}
$perPage = 20;
$offset = ($page - 1) * $perPage;
if (addslashes(htmlspecialchars($_GET["type"] ?? "")) == "" || addslashes(htmlspecialchars($_GET["type"] ?? "")) == "0") {
	$sql3 = "SELECT * FROM comm where comaud= '0' order by id desc LIMIT {$perPage} OFFSET {$offset}";
	$totalRecords = $conn->query("SELECT COUNT(*) AS total FROM comm where comaud= '0'")->fetch_assoc();
} else {
	if (addslashes(htmlspecialchars($_GET["type"] ?? "")) == "all") {
		$sql3 = "SELECT * FROM comm where comaud= '1' order by id desc LIMIT {$perPage} OFFSET {$offset}";
		$totalRecords = $conn->query("SELECT COUNT(*) AS total FROM comm where comaud= '1'")->fetch_assoc();
	}
}
$keyword = addslashes(htmlspecialchars($_GET["keyword"] ?? ""));
if ($keyword != "") {
	$sql3 = "SELECT * FROM comm WHERE cotext LIKE '%{$keyword}%' ORDER BY id DESC";
}
$result3 = $conn->query($sql3);
if ($result3->num_rows > 0) {
	while ($row3 = $result3->fetch_assoc()) {
		$couser = $row3["couser"];
		$coimg = $row3["coimg"];
		$coname = $row3["coname"];
		$courl = $row3["courl"];
		$cotext = $row3["cotext"];
		$bcouser = $row3["bcouser"];
		$bconame = $row3["bconame"];
		$comaud = $row3["comaud"];
		$cotime = $row3["cotime"];
		$coip = $row3["ip"];
		$wzcid = $row3["wzcid"];
		$ecid = $row3["ecid"];
		$pldid = $row3["id"];
		$sql33 = "SELECT * FROM essay WHERE cid = '{$wzcid}'";
		$result33 = $conn->query($sql33);
		if ($result33->num_rows > 0) {
			while ($row33 = $result33->fetch_assoc()) {
				$wzdnr = $row33["ptptext"];
				$wzdnrlx = $row33["ptplx"];
				if ($wzdnr == "") {
					if ($wzdnrlx == "video") {
						$wzdnr = "视频文章不支持预览";
					} elseif ($wzdnrlx == "music") {
						$wzdnr = "音乐文章不支持预览";
					}
				}
				$pattern = "/<a\\s+[^>]*>(.*?)<\\/a>/i";
				$replacement = "\$1";
				$cotext = preg_replace($pattern, $replacement, $cotext);
				$wzdnr = str_replace("./assets/owo/paopao/", "../../assets/owo/paopao/", $wzdnr, $count);
				$wzdnr = $wzdnr . PHP_EOL;
				$wzdnr = str_replace("src=\"./assets/img/thumbnail.svg\"", "", $wzdnr, $count);
				$wzdnr = $wzdnr . PHP_EOL;
				$wzdnr = str_replace("data-src=\"", "src=\"", $wzdnr, $count);
				$wzdnr = $wzdnr . PHP_EOL;
				$wzdnr = str_replace("<img ", "<img style=\"width: 24px;height: 24px;\" ", $wzdnr, $count);
				$wzdnr = $wzdnr . PHP_EOL;
				$pattern = "/<a\\s+[^>]*>(.*?)<\\/a>/i";
				$replacement = "\$1";
				$wzdnr = preg_replace($pattern, $replacement, $wzdnr);
			}
		}
		$sql33 = "SELECT * FROM user WHERE username = '{$couser}'";
		$result33 = $conn->query($sql33);
		if ($result33->num_rows > 0) {
			while ($row33 = $result33->fetch_assoc()) {
				$couseremail = $row33["email"];
			}
		}
		if (strpos($couser, "vis#-[") !== false || strpos($couser, "-#vis") !== false) {
			$couseremail = str_replace(["vis#-[", "]-#vis"], "", $couser);
		}
		$data_result = mysqli_query($conn, "select * from essay where cid='{$wzcid}'");
		$data_row = mysqli_fetch_array($data_result);
		$wzdlx = $data_row["ptplx"];
		$wzdtp = $data_row["ptpimag"];
		if ($wzdlx == "img") {
			if ($wzdtp != "") {
				$imgar = explode("(+@+)", $wzdtp);
				$coun = count($imgar);
				$wzdtp = $imgar[0];
				if (strpos($wzdtp, "./upload/") !== false) {
					$wzdtp = "../../" . $wzdtp;
				}
			} else {
				$wzdtp = "./../assets/img/thumbnailbg.svg";
			}
		} else {
			$wzdtp = "../../assets/img/thumbnailbg.svg";
		}
		if ($coimg == "./assets/img/tx.png") {
			$coimg = "../../assets/img/tx.png";
		} else {
			if (strpos($coimg, "./user/headimg/") !== false) {
				$coimg = "../." . $coimg;
			}
		}
		$cotext = str_replace("./assets/owo/paopao/", "../../assets/owo/paopao/", $cotext, $count);
		$cotext = $cotext . PHP_EOL;
		$cotext = str_replace("src=\"./assets/img/thumbnail.svg\"", "", $cotext, $count);
		$cotext = $cotext . PHP_EOL;
		$cotext = str_replace("data-src=\"", "src=\"", $cotext, $count);
		$cotext = $cotext . PHP_EOL;
		$cotext = str_replace("<img ", "<img style=\"width: 24px;height: 24px;\" ", $cotext, $count);
		$cotext = $cotext . PHP_EOL;
		if ($comaud == 0) {
			$plshzt = "<span class=\"badge badge-success\" style=\"margin-left: 5px;font-size: 12px;vertical-align: middle;background-color: var(--admin-danger)\">待审核</span>";
		} elseif ($comaud == 1) {
			$plshzt = "<span class=\"badge badge-success\" style=\"margin-left: 5px;font-size: 12px;vertical-align: middle;background-color: var(--admin-success)\">已审核</span>";
		}
		echo "<tr id=\"sh-plid-" . $ecid . "\">
                            <td><img style=\"border-radius: 4px;object-fit: cover;\" class=\"img-avatar img-avatar-36 m-r-10\" src=\"" . $coimg . "\" alt=\"头像\">" . $coname . "</td>
                            <td>" . $couseremail . "</td>
                            <td style=\"color: var(--admin-primary);\">" . $cotext . "</td>
                            <td><a href=\"../view.php?cid=" . $wzcid . "\" target=\"_blank\" title=\"\" data-toggle=\"tooltip\" data-original-title=\"\" style=\"color: var(--dark);\">" . $wzdnr . "</a></td>
                            <td>" . $coip . "</td>
                            <td>" . $plshzt . "</td>
                            <td>" . $cotime . "</td>
                            <td>
                                <div class=\"btn-group\" style=\"margin-right: 5px;\">
                                    <a class=\"btn btn-xs btn-default\" href=\"JavaScript:;\" title=\"\" data-toggle=\"tooltip\" data-original-title=\"\" onclick=\"scwtgpl()\" lang=\"" . $ecid . "\"><i class=\"mdi mdi-close\" style=\"pointer-events: none;font-size: 16px;\"></i></a>
                                </div>";
		if (addslashes(htmlspecialchars($_GET["type"] ?? "")) != "all") {
			echo "<div class=\"btn-group\">
                                    <a class=\"btn btn-xs btn-default\" href=\"JavaScript:;\" title=\"\" data-toggle=\"tooltip\" data-original-title=\"\" onclick=\"shwtgpl()\" lang=\"" . $ecid . "\"><i class=\"mdi mdi-checkbox-marked-circle-outline\" style=\"pointer-events: none;font-size: 16px;\"></i></a>
                                </div>";
		}
		?></td>
                        </tr><?php 
	}
}
$totalRecords = $totalRecords["total"];
$maxPages = ceil($totalRecords / $perPage);
?>

                                          
                    </tbody>
                  </table>
                </div>
                <?php 
echo "<div class=\"fixed-table-pagination callout\">
                  <div class=\"float-left pagination-detail\" style=\"margin-bottom: 10px;\">
                      <span class=\"pagination-info\">
                          每页显示" . $perPage . "条数据，总共" . $totalRecords . "条数据
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
		echo "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"首页\" href=\"./auditco.php?" . $typez . "page=1\">首页</a></li>";
		echo "<li class=\"page-item page-pre\"><a class=\"page-link\" aria-label=\"上一页\" href=\"./auditco.php?" . $typez . "page=" . ($page - 1) . "\">‹</a></li>";
	}
	$start = max(1, $page - 1);
	$end = min($page + 1, $maxPages);
	if ($start > 1) {
	}
	for ($i = $start; $i <= $end; $i++) {
		if ($i == $page) {
			echo "<li class=\"page-item active\"><a class=\"page-link\" aria-label=\"第" . $i . "页\" href=\"./auditco.php?" . $typez . "page=" . $i . "\">" . $i . "</a></li>";
		} else {
			echo "<li class=\"page-item \"><a class=\"page-link\" aria-label=\"第" . $i . "页\" href=\"./auditco.php?" . $typez . "page=" . $i . "\">" . $i . "</a></li>";
		}
	}
	if ($end < $maxPages) {
		?><li class="page-item disabled"><span class="page-link">...</span></li><?php 
	}
	if ($page < $maxPages) {
		echo "<li class=\"page-item page-pre\"><a class=\"page-link\" aria-label=\"下一页\" href=\"./auditco.php?" . $typez . "page=" . ($page + 1) . "\">›</a></li>";
		echo "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"尾页\" href=\"./auditco.php?" . $typez . "page=" . $maxPages . "\">尾页</a></li>";
	}
}
?>

                      </ul>
                  </div>
            </div>              </div>
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
//审核评论事件
function shwtgpl(){
    var ele = window.event.srcElement.lang;

    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/escoaud.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('ztm=shpl&cs='+ele);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                alert("未获取到数据!");
                return;
            }
            //alert(xhr.responseText);
            if (xhr.responseText == "200") {
                var x = document.getElementById("sh-plid-"+ele);
                //如果对象x不为空
                if (x != null){
                    x.remove();
                }
            }else{
                alert(xhr.responseText);
            }
        }
    };
}


//驳回删除评论事件
function scwtgpl(){
    if (confirm("确定要删除此评论吗?")) {
        // 用户点击了确认按钮
      } else {
        // 用户点击了取消按钮或关闭了弹窗
        return;
      }
      
    var ele = window.event.srcElement.lang;
    // 异步对象
    var xhr = new XMLHttpRequest();
    // 设置属性
    xhr.open('post', './api/escoaud.php');
    // 如果想要使用post提交数据,必须添加此行
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // 将数据通过send方法传递
    xhr.send('ztm=bhpl&cs='+ele);
    // 发送并接受返回值
    xhr.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == "") {
                alert("未获取到数据!");
                return;
            }
            //alert(xhr.responseText);
            if (xhr.responseText == "200") {
                var x = document.getElementById("sh-plid-"+ele);
                //如果对象x不为空
                if (x != null){
                    x.remove();
                }
            }else{
                alert(xhr.responseText);
            }
        }
    };
}
</script>
</body>
</html>
