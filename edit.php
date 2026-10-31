<?php

//decode by nige112
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$arr = [["code" => "201", "msg" => "非法请求"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$cid = trim((string) ($_GET["cid"] ?? ''));
$cid = addslashes(htmlspecialchars($cid, ENT_QUOTES, 'UTF-8'));
$referer = (string) ($_SERVER["HTTP_REFERER"] ?? '');
if (strpos($referer, "view.php") !== false) {
	if ($cid != "") {
		$tiaourl = "./view.php?cid=" . $cid;
	} else {
		$tiaourl = "./view.php";
	}
} else {
	$tiaourl = "./index.php";
}
$iteace = "0";
if (is_file("./config.php")) {
	require "./config.php";
} else {
	exit("未检测到配置文件：<span style=\"color: red;\">config.php</span>，若您是首次使用请先进行安装,删除文件夹 <span style=\"color: red;\">[install/]</span> 下的 <span style=\"color: red;\">[ins.bak]</span> 文件后进行安装。");
}
require "./api/wz.php";
if ($userdlzt == 0) {
	header("Location:./index.php");
	exit;
}
$ptpuser = $ptpimg = $ptpname = $ptptext = $ptpimag = $ptpvideo = $ptpmusic = '';
$ptplx = $ptpdw = $ptptime = $ptpggurl = $ptpys = $ptpaud = $ptpip = '';
$ptpgg = '0';
$commauth = '1';
$wid = 0;
$year = $month = $day = $hour = $minute = $second = '';
$externalImageText = '';
$useExternalImages = false;
$useQiniuImages = false;
$storedImages = [];
if ($cid != "") {
	$sql = "select * from essay where cid = '{$cid}'";
	$result = $conn->query($sql);
	if (!mysqli_num_rows($result) > 0) {
		if (strpos($referer, "view.php") !== false) {
			$tiaourl = "./home.php";
		} else {
			$tiaourl = "./index.php";
		}
		exit("<script language=\"JavaScript\">;alert(\"朋友圈内容不存在或已被删除!\");location.href=\"" . $tiaourl . "\";</script>;");
	}
	if ($zjzhq == $glyadmin) {
	} else {
		$sql = "select * from essay where cid= '{$cid}'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$ptpuser = $row["ptpuser"];
		} else {
			if (strpos($referer, "view.php") !== false) {
				$tiaourl = "./home.php";
			} else {
				$tiaourl = "./index.php";
			}
			exit("<script language=\"JavaScript\">;alert(\"未获取到朋友圈数据\");location.href=\"" . $tiaourl . "\";</script>;");
		}
		if ($zjzhq == $ptpuser) {
		} else {
			if (strpos($referer, "view.php") !== false) {
				$tiaourl = "./home.php";
			} else {
				$tiaourl = "./index.php";
			}
			exit("<script language=\"JavaScript\">;alert(\"您无权操作其他用户的朋友圈内容\");location.href=\"" . $tiaourl . "\";</script>;");
		}
	}
	$data_result = mysqli_query($conn, "select * from essay where cid='{$cid}'");
	$row = mysqli_fetch_array($data_result);
	$ptpuser = $row["ptpuser"];
	$ptpimg = $row["ptpimg"];
	$ptpname = $row["ptpname"];
	$ptptext = $row["ptptext"];
	$ptpimag = $row["ptpimag"];
	$ptpvideo = $row["ptpvideo"];
	$ptpmusic = $row["ptpmusic"];
	$ptplx = $row["ptplx"];
	$ptpdw = $row["ptpdw"];
	$ptptime = $row["ptptime"];
	$ptpgg = $row["ptpgg"];
	$ptpggurl = $row["ptpggurl"];
	$ptpys = $row["ptpys"];
	$commauth = $row["commauth"];
	$ptpaud = $row["ptpaud"];
	$ptpip = $row["ip"];
	$cid = $row["cid"];
	$wid = $row["id"];
	if ($ptpimag !== '') {
		$storedImages = array_values(array_filter(array_map('trim', explode('(+@+)', $ptpimag))));
		$useExternalImages = count($storedImages) > 0;
		foreach ($storedImages as $storedImage) {
			$scheme = strtolower((string) parse_url($storedImage, PHP_URL_SCHEME));
			if (!in_array($scheme, ['http', 'https'], true)) {
				$useExternalImages = false;
				break;
			}
		}
		if ($useExternalImages) {
			$externalImageText = implode("\n", $storedImages);
			$qiniuDomain = '';
			if (isset($filterqiniu) && $filterqiniu !== '') {
				$qiniuConfig = json_decode($filterqiniu, true);
				$qiniuDomain = is_array($qiniuConfig) ? rtrim((string) ($qiniuConfig['domain'] ?? ''), '/') : '';
			}
			$useQiniuImages = $qiniuDomain !== '';
			if ($useQiniuImages) {
				foreach ($storedImages as $storedImage) {
					if (strncmp(rtrim($storedImage, '/'), $qiniuDomain . '/', strlen($qiniuDomain) + 1) !== 0) {
						$useQiniuImages = false;
						break;
					}
				}
				if ($useQiniuImages) {
					$useExternalImages = false;
					$externalImageText = '';
				}
			}
		}
	}
	$year = date("Y", strtotime($ptptime));
	$month = date("m", strtotime($ptptime));
	$day = date("d", strtotime($ptptime));
	$hour = date("H", strtotime($ptptime));
	$minute = date("i", strtotime($ptptime));
	$second = date("s", strtotime($ptptime));
	$arr = [["title" => "::(呵呵)", "img" => "./assets/owo/paopao/E591B5E591B5_2x.png"], ["title" => "::(哈哈)", "img" => "./assets/owo/paopao/E59388E59388_2x.png"], ["title" => "::(吐舌)", "img" => "./assets/owo/paopao/E59090E8888C_2x.png"], ["title" => "::(太开心)", "img" => "./assets/owo/paopao/E5A4AAE5BC80E5BF83_2x.png"], ["title" => "::(笑眼)", "img" => "./assets/owo/paopao/E7AC91E79CBC_2x.png"], ["title" => "::(花心)", "img" => "./assets/owo/paopao/E88AB1E5BF83_2x.png"], ["title" => "::(小乖)", "img" => "./assets/owo/paopao/E5B08FE4B996_2x.png"], ["title" => "::(乖)", "img" => "./assets/owo/paopao/E4B996_2x.png"], ["title" => "::(捂嘴笑)", "img" => "./assets/owo/paopao/E68D82E598B4E7AC91_2x.png"], ["title" => "::(滑稽)", "img" => "./assets/owo/paopao/E6BB91E7A8BD_2x.png"], ["title" => "::(你懂的)", "img" => "./assets/owo/paopao/E4BDA0E68782E79A84_2x.png"], ["title" => "::(不高兴)", "img" => "./assets/owo/paopao/E4B88DE9AB98E585B4_2x.png"], ["title" => "::(怒)", "img" => "./assets/owo/paopao/E68092_2x.png"], ["title" => "::(汗)", "img" => "./assets/owo/paopao/E6B197_2x.png"], ["title" => "::(黑线)", "img" => "./assets/owo/paopao/E9BB91E7BABF_2x.png"], ["title" => "::(泪)", "img" => "./assets/owo/paopao/E6B3AA_2x.png"], ["title" => "::(真棒)", "img" => "./assets/owo/paopao/E79C9FE6A392_2x.png"], ["title" => "::(喷)", "img" => "./assets/owo/paopao/E596B7_2x.png"], ["title" => "::(惊哭)", "img" => "./assets/owo/paopao/E6838AE593AD_2x.png"], ["title" => "::(阴险)", "img" => "./assets/owo/paopao/E998B4E999A9_2x.png"], ["title" => "::(鄙视)", "img" => "./assets/owo/paopao/E98499E8A786_2x.png"], ["title" => "::(酷)", "img" => "./assets/owo/paopao/E985B7_2x.png"], ["title" => "::(啊)", "img" => "./assets/owo/paopao/E5958A_2x.png"], ["title" => "::(狂汗)", "img" => "./assets/owo/paopao/E78B82E6B197_2x.png"], ["title" => "::(what)", "img" => "./assets/owo/paopao/what_2x.png"], ["title" => "::(疑问)", "img" => "./assets/owo/paopao/E79691E997AE_2x.png"], ["title" => "::(酸爽)", "img" => "./assets/owo/paopao/E985B8E788BD_2x.png"], ["title" => "::(呀咩蹀)", "img" => "./assets/owo/paopao/E59180E592A9E788B9_2x.png"], ["title" => "::(委屈)", "img" => "./assets/owo/paopao/E5A794E5B188_2x.png"], ["title" => "::(惊讶)", "img" => "./assets/owo/paopao/E6838AE8AEB6_2x.png"], ["title" => "::(睡觉)", "img" => "./assets/owo/paopao/E79DA1E8A789_2x.png"], ["title" => "::(笑尿)", "img" => "./assets/owo/paopao/E7AC91E5B0BF_2x.png"], ["title" => "::(挖鼻)", "img" => "./assets/owo/paopao/E68C96E9BCBB_2x.png"], ["title" => "::(吐)", "img" => "./assets/owo/paopao/E59090_2x.png"], ["title" => "::(犀利)", "img" => "./assets/owo/paopao/E78A80E588A9_2x.png"], ["title" => "::(小红脸)", "img" => "./assets/owo/paopao/E5B08FE7BAA2E884B8_2x.png"], ["title" => "::(懒得理)", "img" => "./assets/owo/paopao/E68792E5BE97E79086_2x.png"], ["title" => "::(勉强)", "img" => "./assets/owo/paopao/E58B89E5BCBA_2x.png"], ["title" => "::(爱心)", "img" => "./assets/owo/paopao/E788B1E5BF83_2x.png"], ["title" => "::(心碎)", "img" => "./assets/owo/paopao/E5BF83E7A28E_2x.png"], ["title" => "::(玫瑰)", "img" => "./assets/owo/paopao/E78EABE791B0_2x.png"], ["title" => "::(礼物)", "img" => "./assets/owo/paopao/E7A4BCE789A9_2x.png"], ["title" => "::(彩虹)", "img" => "./assets/owo/paopao/E5BDA9E899B9_2x.png"], ["title" => "::(太阳)", "img" => "./assets/owo/paopao/E5A4AAE998B3_2x.png"], ["title" => "::(星星月亮)", "img" => "./assets/owo/paopao/E6989FE6989FE69C88E4BAAE_2x.png"], ["title" => "::(钱币)", "img" => "./assets/owo/paopao/E992B1E5B881_2x.png"], ["title" => "::(茶杯)", "img" => "./assets/owo/paopao/E88CB6E69DAF_2x.png"], ["title" => "::(蛋糕)", "img" => "./assets/owo/paopao/E89B8BE7B395_2x.png"], ["title" => "::(大拇指)", "img" => "./assets/owo/paopao/E5A4A7E68B87E68C87_2x.png"], ["title" => "::(胜利)", "img" => "./assets/owo/paopao/E8839CE588A9_2x.png"], ["title" => "::(haha)", "img" => "./assets/owo/paopao/haha_2x.png"], ["title" => "::(OK)", "img" => "./assets/owo/paopao/OK_2x.png"], ["title" => "::(沙发)", "img" => "./assets/owo/paopao/E6B299E58F91_2x.png"], ["title" => "::手纸", "img" => "./assets/owo/paopao/E6898BE7BAB8_2x.png"], ["title" => "::(香蕉)", "img" => "./assets/owo/paopao/E9A699E89589_2x.png"], ["title" => "::(便便)", "img" => "./assets/owo/paopao/E4BEBFE4BEBF_2x.png"], ["title" => "::(药丸)", "img" => "./assets/owo/paopao/E88DAFE4B8B8_2x.png"], ["title" => "::(红领巾)", "img" => "./assets/owo/paopao/E7BAA2E9A286E5B7BE_2x.png"], ["title" => "::(蜡烛)", "img" => "./assets/owo/paopao/E89CA1E7839B_2x.png"], ["title" => "::(音乐)", "img" => "./assets/owo/paopao/E99FB3E4B990_2x.png"], ["title" => "::(灯泡)", "img" => "./assets/owo/paopao/E781AFE6B3A1_2x.png"]];
	for ($i = 0; $i < count($arr); $i++) {
		$danm = $arr[$i]["title"];
		$dang = $arr[$i]["img"];
		$aeimg = "<span class=\"sh-nr-bq-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $dang . "\" class=\"sh-nr-bq-img\" alt=\"" . $danm . "\"></span>";
		$ptptext = str_ireplace($aeimg, $danm, $ptptext);
		$aeimg = "<img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $dang . "\" class=\"sh-nr-bq-img\">";
		$ptptext = str_ireplace($aeimg, $danm, $ptptext);
		$pattern = "/<a\\s+.*?\\bhref=[\"'](.*?)[\"'].*?>.*?<\\/a>/i";
		$replacement = " \$1 ";
		$ptptext = preg_replace($pattern, $replacement, $ptptext);
		$ptptext = str_replace("<br>", "", $ptptext);
	}
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>发布朋友圈</title>
    <!--为搜索引擎定义关键词-->
    <meta name="keywords" content="<?php echo $name;?>">
    <!--为网页定义描述内容 用于告诉搜索引擎，你网站的主要内容-->
    <meta name="description" content="<?php echo $name . "," . $subtitle;?>">
    <meta name="author" content="<?php echo $name;?>">
    <meta name="copyright" content="<?php echo $name;?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta http-equiv="cache-control" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,minimum-scale=1.0, user-scalable=no minimal-ui">
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $icon;?>" /><!-- Standard iPhone -->
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $icon;?>" /><!-- Retina iPhone -->
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $icon;?>" /><!-- Standard iPad -->
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $icon;?>" /><!-- Retina iPad -->
    <link rel="icon" href="<?php echo $icon;?>" type="image/x-icon" />
    <meta name="robots" content="index,follow">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="x-rim-auto-match" content="none">
    <?php 
if ($rosdomain == 1) {
	?><meta name="referrer" content="no-referrer"><?php 
}
?>    <link rel="stylesheet" href="<?php echo $iconurl_url;?>">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
    <link rel="stylesheet" type="text/css" href="./assets/mesg/dist/css/style.css?v=<?php echo $resversion;?>"><!--引入弹窗对话框-->
    <?php echo $scfontzt;?>    <?php 
if ($siteBackground !== '') {
	?><style>body{background-image:url('<?php echo htmlspecialchars($siteBackground, ENT_QUOTES, 'UTF-8'); ?>');}</style><?php
}
?>    <?php echo "<style>" . $filtercucss . "</style>";?></head>
<body class="<?php 
if (isset($_COOKIE["dark_theme"])) {
	if ($_COOKIE["dark_theme"] == "dark-theme") {
		echo "dark-theme";
	}
}
echo "\">
    ";
if ($pagepass != "") {
	if (isset($_COOKIE["pagepass"])) {
		if ($_COOKIE["pagepass"] != md5(md5($pagepass))) {
			include "./site/pagepass.php";
		}
	} else {
		include "./site/pagepass.php";
	}
}
?>    <div class="centent">
        <div class="sh-main setup-main edit-main" >
            <form method="post" action="./api/form.php" enctype="multipart/form-data" onsubmit="return dosubmit()" autocomplete="off">
            <?php 
if ($cid != "") {
	echo "<input type=\"text\" name=\"edlx\" value=\"edites\" checked id=\"editeslx\" style=\"display: none;\"/>
                <input type=\"text\" name=\"edwzcid\" value=\"" . $cid . "\" checked id=\"edwzicd\" style=\"display: none;\"/>";
} else {
	?><input type="text" name="edlx" value="prees" checked id="editeslx" style="display: none;"/><?php 
}
?>            <div class="sh-main-head setup-main-head">
                <!-- 顶部菜单 -->
                <div class="sh-main-head-top setup-main-top" id="sh-main-head-top">
                    <!-- 左边 -->
                    <div class="sh-main-head-top-left">
                        <div class="sh-main-head-top-left-s" onclick="location.href='<?php echo $tiaourl;?>'">
                            <i class="iconfont icon-weibiaoti al-sxbh" id="top-left-1"></i>
                        </div>
                        <div class="sh-main-head-top-left-s setup-main-head-top-left-s">
                            <span class="setup-main-title">发布</span>
                        </div>
                    </div>
                    <!-- 右边 -->
                    <div class="sh-main-head-top-right">
                        <div class="sh-main-head-top-right-s-fas">
                            <button type="submit" id="submit">发布</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sh-cont-nr">
                <div class="sh-cont-nr-tzh">
                    <textarea name="text" id="bletext" class="form-controll" placeholder="说点什么.." maxlength="50000" spellcheck="false"><?php 
if ($ptptext != "") {
	echo $ptptext;
}
?></textarea><div class="ai-copywrite-bar"><input type="text" id="ai-prompt" placeholder="输入提示词，例如：写一段春日郊游的朋友圈文案"><button type="button" id="ai-copywrite">AI 写文案</button></div>
                </div>
                
                <!--各种开关控制-->
                <div class="sh-cont-nr-kg">
                    
                    <!--表情开关按钮-->
                    <div class="sh-cont-nr-kg-bqkg" onclick="shkgbqkg()" title="表情" id="bqkg"><i class="iconfont icon-biaoqing ri-sxfbbq" id="bqkgimg"></i></div>
                    
                    <!--广告开关按钮-->
                    <?php 
if ($ptpgg == "1") {
	?><div class="sh-cont-nr-kg-ggkg" onclick="ggkg()" title="取消广告" id="ggkg" lang="1" style="background: var(--themetm);"><i class="iconfont icon-ljgg ri-sxfbbqls" id="ggkgimg"></i>
                    <input type="radio" name="radiogg" value="1" checked="" id="ggmk" style="display: none;">
                    </div><?php 
} else {
	?><div class="sh-cont-nr-kg-ggkg" onclick="ggkg()" title="设为广告" id="ggkg" lang="0"><i class="iconfont icon-ljgg ri-sxfbbq" id="ggkgimg"></i>
                    <input type="radio" name="radiogg" value="0" checked id="ggmk" style="display: none;"/>
                    </div><?php 
}
?>                    
                    
                    <?php 
if ($notname == "1") {
	if ($cid == "") {
		?><!--匿名开关按钮-->
                    <div class="sh-cont-nr-kg-yxplkg" onclick="nmkgz()" title="匿名发布" id="nmkgz" lang="0"><i class="iconfont icon-anonymous ri-sxfbbq" id="nmkgimg"></i>
                    <input type="radio" name="nmkg" value="0" checked id="nmmkz" style="display: none;"/>
                    </div><?php 
	}
}
?>                    
                    <!--评论开关按钮-->
                    <?php 
if ($commauth != "0") {
	?><div class="sh-cont-nr-kg-yxplkg" onclick="yxplkgz()" title="允许评论" id="yxplkgz" lang="1" style="background: var(--themetm);"><i class="iconfont icon-pinglun-yx ri-sxfbbqls" id="yxplkgimg"></i>
                    <input type="radio" name="yxplkg" value="1" checked id="yxplmkz" style="display: none;"/>
                    </div><?php 
} else {
	?><div class="sh-cont-nr-kg-yxplkg" onclick="yxplkgz()" title="禁止评论" id="yxplkgz" lang="0" style="background: var(--fgxys);"><i class="iconfont icon-pinglun-yx ri-sxfbbq" id="yxplkgimg"></i>
                    <input type="radio" name="yxplkg" value="0" checked="" id="yxplmkz" style="display: none;">
                    </div><?php 
}
?>                    
                    
                    <!--文章类型切换按钮-->
                    <?php 
if ($cid == "") {
	?><div class="sh-cont-nr-kg-lxqh" onclick="lxqhkg()" title="文章类型:图文" id="lxqh"><i class="iconfont icon-tupian ri-sxfbbqls" id="lxqhimg"></i>
                    <input type="radio" name="radio" value="1" checked id="lxmk" style="display: none;"/>
                    </div><?php 
}
?>                    

                    <!--图片/视频来源切换按钮-->
                    <div class="sh-cont-nr-kg-yxplkg media-source-toggle" onclick="toggleMediaSource()" title="切换到图片外链" id="tpscfs" data-mode="<?php echo $useExternalImages ? 'external' : 'upload'; ?>" style="background: <?php echo $useExternalImages ? 'var(--themetm)' : 'var(--fgxys)'; ?>;">
                        <i class="iconfont icon-lianjie1 <?php echo $useExternalImages ? 'ri-sxfbbqls' : 'ri-sxfbbq'; ?>" id="twlimg"></i>
                    </div>
                    <div class="sh-cont-nr-kg-yxplkg qiniu-upload-toggle" onclick="toggleQiniuUpload()" title="七牛云直传（<?php echo $useQiniuImages ? '开启' : '关闭'; ?>）" id="qiniu-upload-toggle" data-enabled="<?php echo $useQiniuImages ? '1' : '0'; ?>" style="background: <?php echo $useQiniuImages ? 'var(--themetm)' : 'var(--fgxys)'; ?>;">
                        <i class="iconfont icon-tianjiatupian <?php echo $useQiniuImages ? 'ri-sxfbbqls' : 'ri-sxfbbq'; ?>" id="qiniu-upload-img"></i>
                    </div>
                    
                </div>
                <!--各种开关控制 end-->
                
                <div class="sh-pinglun-biao" id="biaoqing" style="display: none;">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E591B5E591B5_2x.png" alt="::(呵呵)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E59388E59388_2x.png" alt="::(哈哈)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E59090E8888C_2x.png" alt="::(吐舌)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5A4AAE5BC80E5BF83_2x.png" alt="::(太开心)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E7AC91E79CBC_2x.png" alt="::(笑眼)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E88AB1E5BF83_2x.png" alt="::(花心)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5B08FE4B996_2x.png" alt="::(小乖)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E4B996_2x.png" alt="::(乖)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E68D82E598B4E7AC91_2x.png" alt="::(捂嘴笑)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6BB91E7A8BD_2x.png" alt="::(滑稽)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E4BDA0E68782E79A84_2x.png" alt="::(你懂的)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E4B88DE9AB98E585B4_2x.png" alt="::(不高兴)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E68092_2x.png" alt="::(怒)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6B197_2x.png" alt="::(汗)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E9BB91E7BABF_2x.png" alt="::(黑线)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6B3AA_2x.png" alt="::(泪)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E79C9FE6A392_2x.png" alt="::(真棒)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E596B7_2x.png" alt="::(喷)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6838AE593AD_2x.png" alt="::(惊哭)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E998B4E999A9_2x.png" alt="::(阴险)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E98499E8A786_2x.png" alt="::(鄙视)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E985B7_2x.png" alt="::(酷)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5958A_2x.png" alt="::(啊)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E78B82E6B197_2x.png" alt="::(狂汗)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/what_2x.png" alt="::(what)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E79691E997AE_2x.png" alt="::(疑问)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E985B8E788BD_2x.png" alt="::(酸爽)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E59180E592A9E788B9_2x.png" alt="::(呀咩蹀)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5A794E5B188_2x.png" alt="::(委屈)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6838AE8AEB6_2x.png" alt="::(惊讶)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E79DA1E8A789_2x.png" alt="::(睡觉)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E7AC91E5B0BF_2x.png" alt="::(笑尿)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E68C96E9BCBB_2x.png" alt="::(挖鼻)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E59090_2x.png" alt="::(吐)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E78A80E588A9_2x.png" alt="::(犀利)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5B08FE7BAA2E884B8_2x.png" alt="::(小红脸)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E68792E5BE97E79086_2x.png" alt="::(懒得理)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E58B89E5BCBA_2x.png" alt="::(勉强)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E788B1E5BF83_2x.png" alt="::(爱心)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5BF83E7A28E_2x.png" alt="::(心碎)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E78EABE791B0_2x.png" alt="::(玫瑰)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E7A4BCE789A9_2x.png" alt="::(礼物)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5BDA9E899B9_2x.png" alt="::(彩虹)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5A4AAE998B3_2x.png" alt="::(太阳)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6989FE6989FE69C88E4BAAE_2x.png" alt="::(星星月亮)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E992B1E5B881_2x.png" alt="::(钱币)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E88CB6E69DAF_2x.png" alt="::(茶杯)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E89B8BE7B395_2x.png" alt="::(蛋糕)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E5A4A7E68B87E68C87_2x.png" alt="::(大拇指)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E8839CE588A9_2x.png" alt="::(胜利)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/haha_2x.png" alt="::(haha)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/OK_2x.png" alt="::(OK)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6B299E58F91_2x.png" alt="::(沙发)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E6898BE7BAB8_2x.png" alt="::手纸" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E9A699E89589_2x.png" alt="::(香蕉)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E4BEBFE4BEBF_2x.png" alt="::(便便)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E88DAFE4B8B8_2x.png" alt="::(药丸)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E7BAA2E9A286E5B7BE_2x.png" alt="::(红领巾)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E89CA1E7839B_2x.png" alt="::(蜡烛)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E99FB3E4B990_2x.png" alt="::(音乐)" onclick="biaoqzj()">
                            <img src="./assets/img/thumbnail.svg" data-src="./assets/owo/paopao/E781AFE6B3A1_2x.png" alt="::(灯泡)" onclick="biaoqzj()">
                        </div>
            </div>

            <!--定位-->
            <div class="sh-cont-dw">
                <div class="sh-cont-dw-tu" onclick="hqdqip()" title="获取当前位置">
                <i class="iconfont icon-31dingwei ri-sxgps" id="sh-cont-dw"></i></div>
                <input type="text" name="dw" id="dwxx" value="<?php 
if ($ptpdw != "") {
	echo $ptpdw;
}
?>" placeholder="当前位置" minlength="1" maxlength="100" spellcheck="false">
            </div>
            
            <!--发布时间-->
            <?php 
if ($cid != "") {
	echo "<div class=\"sh-cont-dw\">
                <div class=\"sh-cont-dw-tu\" onclick=\"hqdqip()\" title=\"发布时间\">
                <i class=\"iconfont icon-shijian31 ri-sxgps\" id=\"sh-cont-dw\"></i></div>
                <input type=\"text\" name=\"fbtime\" id=\"fbtimexx\" value=\"" . $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute . ":" . $second . "\" placeholder=\"发布时间\" minlength=\"1\" maxlength=\"20\" spellcheck=\"false\" required>
            </div>";
}
echo "
            <!--广告跳转链接-->
            ";
if ($ptpgg == "1") {
	echo "<div class=\"sh-cont-gg\" id=\"sh-cont-gg\" style=\"display: flex;\">
                <input type=\"url\" name=\"gg\" id=\"gglj\" value=\"" . $ptpggurl . "\" placeholder=\"广告跳转链接\" minlength=\"1\" maxlength=\"2000\" spellcheck=\"false\">
            </div>";
} else {
	?><div class="sh-cont-gg" id="sh-cont-gg">
                <input type="url" name="gg" id="gglj" value="" placeholder="广告跳转链接" minlength="1" maxlength="2000" spellcheck="false">
            </div><?php 
}
?>            
            
            <!--音乐-->
            <div class="sh-cont-yy" id="sh-cont-yy" <?php 
if ($cid != "") {
	echo "style=\"display:none\"";
}
?>>
                <span>
                    <input type="text" name="music" id="music" value="" placeholder="ID/音乐链接*" minlength="1" maxlength="2000" spellcheck="false" style="border-radius: 4px 0 0 0;">
                    <p></p>
                <input type="text" name="musicm" id="musicm" value="" placeholder="歌名*" minlength="1" maxlength="2000" spellcheck="false" style="border-radius: 0 4px 0 0;">
                </span>
                <div class="sh-cont-yy-fg"></div>
                <span>
                    <input type="text" name="musics" id="musics" value="" placeholder="歌手*" minlength="1" maxlength="2000" spellcheck="false" style="border-radius: 0 0 0 4px;">
                    <p></p>
                <input type="text" name="musict" id="musict" value="" placeholder="封面链接" minlength="1" maxlength="2000" pattern="^(http|https)://[\w\-]+(\.[\w\-]+)+([\w\-\.,@?^=%&:/~\+#]*[\w\-@?^=%&/~\+#])?$" spellcheck="false" style="border-radius: 0 0 4px 0;">
                </span>
                <!--p>注:网易云音乐可直接填写音乐id</p-->
            </div>
            
            <!--视频-->
            <input type="hidden" name="video_mode" id="video_mode" value="upload">
            <div class="video-external-fields" id="video-external-fields" style="display:none;">
            <div class="sh-cont-sp" id="sh-cont-sp" <?php 
if ($cid != "") {
	echo "style=\"display:none\"";
}
?>>
                <input type="url" name="spp" id="spp" value="" placeholder="视频外链（http/https）" minlength="1" maxlength="2000" spellcheck="false">
            </div>
            <div class="sh-cont-sp" id="sh-cont-spfm" style="display:flex;margin-top: 2px;">
                <input type="url" name="sppfm" id="sppfm" value="" placeholder="视频封面外链（可选）" minlength="1" maxlength="2000" spellcheck="false">
            </div>
            </div>
            <video src="" controls="controls" controlslist="nodownload noplaybackrate noremoteplayback" poster="" oncontextmenu="return false" class="sh-content-video sh-cont-video" id="sh-content-video" muted="true" playsinline webkit-playsinline preload="metadata" autoplay muted loop <?php 
if ($cid == "") {
	echo "style=\"display:none\"";
}
?>></video>
            <div class="filsp" id="filsp" <?php 
if ($cid != "") {
	echo "style=\"display:none\"";
}
?>>
                <span class="cupload-upload-btn">+</span>
            <input type="file" accept="video/*" name="file" id="files"><br>
            </div>

            <!--图片-->
            <div class="sh-cont-img" id="sh-cont-img">
                <input type="hidden" name="image_mode" id="image_mode" value="<?php echo $useQiniuImages ? 'qiniu' : ($useExternalImages ? 'external' : 'upload'); ?>">
                <input type="hidden" name="qiniu_upload" id="qiniu_upload" value="<?php echo $useQiniuImages ? '1' : '0'; ?>">
                <div class="sh-cont-imgul" id="sh-cont-imgul"<?php echo $useExternalImages ? ' style="display:flex"' : ''; ?>>
                    <textarea name="imgul" id="imgul" class="form-controllul" placeholder="每行粘贴一个对象存储图片 URL，最多 15 条"><?php echo htmlspecialchars($externalImageText, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="cupload-image-list-wk" id="local-image-upload"<?php echo $useExternalImages ? ' style="display:none"' : ''; ?>><div id="cupload-7"></div></div>
                <?php if ($cid != '' && $ptpimag != ''): ?>
                <label class="remove-images-option"><input type="checkbox" name="remove_images" value="1"> 移除文章现有图片</label>
                <?php endif; ?>
            </div>
            <input type="hidden" name="allkey" id="allkey" value="<?php echo htmlspecialchars((string) $allkey, ENT_QUOTES, 'UTF-8'); ?>">
            </form>
            <!--div class="edit-detu" id="edit-detu">
                <div class="edit-detu-wk">
                    <div class="edit-detu-wk-xx"><i class="iconfont icon-shanchu3"></i><span>删除</span></div>
                </div>
            </div-->
        </div>
        





<div class="sh-copyright">
    <span class="sh-copyright-banquan" id="sh-copyright-banquan"><?php echo $copyright;?></span>&nbsp;
    <?php 
if ($beian != "") {
	echo "<span class=\"sh-copyright-banquan\">" . html_entity_decode($beian) . "</span>";
}
?></div>
</div>










<script type="text/javascript" id="script-id1">
var myallkeyVar ="<?php echo $allkey;?>";//获取发文章用的key
// 获取要删除的 script 标签的引用  
var scriptTag = document.getElementById("script-id1");  
// 删除 script 标签  
scriptTag.parentNode.removeChild(scriptTag);
</script>



    <script type="text/javascript" src="./assets/js/edit.js?v=<?php echo filemtime(__DIR__ . '/assets/js/edit.js'); ?>"></script>
    <script src="./assets/js/cupload.js?v=<?php echo $resversion;?>-qiniu2"></script><!-- 引用多图上传js文件-->
    <script type="text/javascript" src="./assets/mesg/dist/js/sh-noytf.js?v=<?php echo $resversion;?>"></script>
	<script type="text/javascript">
		var cupload7 = new Cupload ({
			ele	: '#cupload-7',
			name: 'image',
			num	: 15,//限制图片上传最多15张
			data: <?php echo json_encode($useQiniuImages ? $storedImages : [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
		});
	</script>
	<?php echo "<script type=\"text/javascript\">" . $filtercujs . "</script>";?><script>(function(){var b=document.getElementById('ai-copywrite'),p=document.getElementById('ai-prompt'),t=document.getElementById('bletext');if(!b)return;b.onclick=function(){var q=p.value.trim();if(!q){alert('请先输入提示词');return;}b.disabled=true;b.textContent='生成中…';var f=new FormData();f.append('prompt',q);fetch('./api/ai-generate.php',{method:'POST',body:f,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(d){if(!d.success)throw new Error(d.message||'生成失败');t.value=d.content;}).catch(function(e){alert(e.message);}).finally(function(){b.disabled=false;b.textContent='AI 写文案';});};}());</script></body>
</html>
