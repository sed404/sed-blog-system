<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

//decode by nige112
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	$arr = [["code" => "201", "msg" => "非法请求"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];
if ($user_zh == "" || $user_passid == "") {
	exit("<script language=\"JavaScript\">;alert(\"请先登录!\");location.href=\"../index.php\";</script>");
}
include "../config.php";
require_once "../includes/qiniu.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("<script language=\"JavaScript\">;alert(\"连接数据库失败\");location.href=\"../edit.php\";</script>");
}
$user_zhsg = addslashes(htmlspecialchars($user_zh));
$sqls = "select * from user where username='{$user_zhsg}'";
$data_results = mysqli_query($conn, $sqls);
$data2s_row = mysqli_fetch_array($data_results);
$user_zh = $data2s_row["username"];
$user_name = $data2s_row["name"];
$user_img = $data2s_row["img"];
$user_url = $data2s_row["url"];
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$name = $row["name"];
	$subtitle = $row["subtitle"];
	$icon = $row["icon"];
	$logo = $row["logo"];
	$zt = $row["zt"];
	$glyusername = $row["username"];
	$glyadmin = $row["username"];
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$wzmusic = $row["music"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$lnkzt = $row["lnkzt"];
	$regqx = $row["regqx"];
	$kqsy = $row["kqsy"];
	$ptpaud = $row["ptpaud"];
	$notname = $row["notname"];
	$imgpres = $row["imgpres"];
	$wzname = $row["name"];
	$emydz = $row["emydz"];
	$emssl = $row["emssl"];
	$emduk = $row["emduk"];
	$emkey = $row["emkey"];
	$emzh = $row["emzh"];
	$emfs = $row["emfs"];
	$emfszm = $row["emfszm"];
}
$data_result = mysqli_query($conn, "select * from user where username='{$user_zh}'");
$data2_row = mysqli_fetch_array($data_result);
$zjzhq = $data2_row["username"];
$zjnc = $data2_row["name"];
$zjimg = $data2_row["img"];
$zjhomeimg = $data2_row["homeimg"];
$zjsign = $data2_row["sign"];
$zjemail = $data2_row["email"];
$zjurl = $data2_row["url"];
$zjfbqx = $data2_row["essqx"];
$zjemtz = $data2_row["esseam"];
$passid = $data2_row["passid"];
$ban = $data2_row["ban"];
$bantime = $data2_row["bantime"];
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"../index.php\";</script>;");
}
if ($ban != "0" && $bantime != "false") {
	if ($bantime === "true") {
		setcookie("username", "", time() + -1, "/");
		setcookie("passid", "", time() + -1, "/");
		exit("<script language=\"JavaScript\">;alert(\"该账号涉嫌违规,已被永久封禁!\");location.href=\"../index.php\";</script>");
	} else {
		setcookie("username", "", time() + -1, "/");
		setcookie("passid", "", time() + -1, "/");
		exit("<script language=\"JavaScript\">;alert(\"你的账号已被封禁！解封时间为:" . $bantime . "\");location.href=\"../index.php\";</script>");
	}
}
$filtertext = "";
$sql = "SELECT text FROM configx WHERE title = 'filtertext'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$filtertext = $row["text"];
}
$allkey = trim((string) ($_POST["allkey"] ?? ''));
if ($allkey === "") {
	exit("<script language=\"JavaScript\">;alert(\"请传入秘钥\");location.href=\"../edit.php\";</script>;");
}
$cloudallkey = (string) ($_SESSION["allkey"] ?? '');
$appintkey = md5(md5($_SERVER["HTTP_HOST"] . "fC4gT5uU2pW7kU8eL8dI4nK5xE9uT6iW"));
if ($allkey != $cloudallkey) {
	if ($allkey == $appintkey) {
	} else {
		exit("<script language=\"JavaScript\">;alert(\"秘钥错误\");location.href=\"../edit.php\";</script>;");
	}
}
if ($kqsy == 1) {
	if (!($zjfbqx == 1 || $zjfbqx == 2)) {
		exit("<script>alert(\"您没有发布权限!\");location.href=\"../edit.php\";</script>");
	}
} else {
	if ($zjzhq != $glyusername) {
		if (!($zjfbqx == 2)) {
			exit("<script>alert(\"管理员未开启所有用户发布权限!\");location.href=\"../edit.php\";</script>");
		}
	}
}
$Y = "Y";
$m = "m";
$d = "d";
$H = "H";
$i = "i";
$s = "s";
$sj = date($Y . $m . $d . $H . $i . $s);
if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) {
	$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
} elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]) {
	$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
} elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]) {
	$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
	$ip = getenv("HTTP_X_FORWARDED_FOR");
} elseif (getenv("HTTP_CLIENT_IP")) {
	$ip = getenv("HTTP_CLIENT_IP");
} elseif (getenv("REMOTE_ADDR")) {
	$ip = getenv("REMOTE_ADDR");
} else {
	$ip = "Unknown";
}
$sql = "SELECT ptptime FROM essay";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
	if ($row["ptptime"] == $sj) {
		exit("<script language=\"JavaScript\">;alert(\"发布频率过快,请稍后再试!\");location.href=\"../edit.php\";</script>;");
	}
}
$microtime = str_replace(".", "", microtime(true));
$randomDigits = "";
do {
	for ($i = 0; $i < 5; $i++) {
		$randomDigits .= rand(0, 9);
	}
} while (strlen($randomDigits) < 5);
$str = $microtime . $randomDigits;
$base64Arr = $_POST["image"] ?? [];
$base64Arr = is_array($base64Arr) ? array_values(array_filter($base64Arr, function ($value) {
	return is_string($value) && trim($value) !== '';
})) : [];
$imgul = trim((string) ($_POST["imgul"] ?? ''));
$imageMode = (string) ($_POST["image_mode"] ?? 'upload');
$imageMode = in_array($imageMode, ['upload', 'external', 'qiniu'], true) ? $imageMode : 'upload';
$videoMode = (string) ($_POST["video_mode"] ?? 'upload');
$removeImages = (string) ($_POST["remove_images"] ?? '') === '1';
$lx = addslashes(htmlspecialchars($_POST["radio"]));
$text = addslashes(htmlspecialchars(filterEmoji($_POST["text"])));
$dw = addslashes(htmlspecialchars($_POST["dw"]));
$gg = addslashes(htmlspecialchars($_POST["radiogg"]));
$yxplkg = addslashes(htmlspecialchars($_POST["yxplkg"]));
$nmkg = addslashes(htmlspecialchars($_POST["nmkg"]));
$gglj = addslashes(htmlspecialchars($_POST["gg"]));
$spp = addslashes(htmlspecialchars($_POST["spp"]));
$sppfm = addslashes(htmlspecialchars($_POST["sppfm"]));
$music = addslashes(htmlspecialchars($_POST["music"]));
$musicm = addslashes(htmlspecialchars($_POST["musicm"]));
$musics = addslashes(htmlspecialchars($_POST["musics"]));
$musict = addslashes(htmlspecialchars($_POST["musict"]));
$text = str_replace(PHP_EOL, "<br>", $text);
$edlx = addslashes(htmlspecialchars($_POST["edlx"]));
$fbtime = addslashes(htmlspecialchars($_POST["fbtime"]));
$edwzcid = addslashes(htmlspecialchars($_POST["edwzcid"]));
if (iconv_strlen($text, "UTF-8") > 50000) {
	exit("<script language=\"JavaScript\">;alert(\"字数超过最大限制!\");location.href=\"../edit.php\";</script>;");
}
if ($filtertext != "") {
	$arrayData = json_decode($filtertext, true);
	$text = str_replace($arrayData, "*", $text);
}
$emwz = $text;
if ($gg == 1) {
	if ($gglj == "") {
		exit("<script language=\"JavaScript\">;alert(\"请输入广告地址!\");location.href=\"../edit.php\";</script>;");
	}
	if (!preg_match("/^(http:\\/\\/|https:\\/\\/).*\$/", $gglj)) {
		exit("<script language=\"JavaScript\">;alert(\"请输入正确的广告地址链接!\");location.href=\"../edit.php\";</script>;");
	}
}
if ($notname == 1) {
	if ($nmkg == 1) {
		$zjnc = "匿名用户";
		$zjimg = "./assets/img/anonymous.png";
	}
}
$textes = parseUrls($text);
$arr = [["title" => "::(呵呵)", "img" => "./assets/owo/paopao/E591B5E591B5_2x.png"], ["title" => "::(哈哈)", "img" => "./assets/owo/paopao/E59388E59388_2x.png"], ["title" => "::(吐舌)", "img" => "./assets/owo/paopao/E59090E8888C_2x.png"], ["title" => "::(太开心)", "img" => "./assets/owo/paopao/E5A4AAE5BC80E5BF83_2x.png"], ["title" => "::(笑眼)", "img" => "./assets/owo/paopao/E7AC91E79CBC_2x.png"], ["title" => "::(花心)", "img" => "./assets/owo/paopao/E88AB1E5BF83_2x.png"], ["title" => "::(小乖)", "img" => "./assets/owo/paopao/E5B08FE4B996_2x.png"], ["title" => "::(乖)", "img" => "./assets/owo/paopao/E4B996_2x.png"], ["title" => "::(捂嘴笑)", "img" => "./assets/owo/paopao/E68D82E598B4E7AC91_2x.png"], ["title" => "::(滑稽)", "img" => "./assets/owo/paopao/E6BB91E7A8BD_2x.png"], ["title" => "::(你懂的)", "img" => "./assets/owo/paopao/E4BDA0E68782E79A84_2x.png"], ["title" => "::(不高兴)", "img" => "./assets/owo/paopao/E4B88DE9AB98E585B4_2x.png"], ["title" => "::(怒)", "img" => "./assets/owo/paopao/E68092_2x.png"], ["title" => "::(汗)", "img" => "./assets/owo/paopao/E6B197_2x.png"], ["title" => "::(黑线)", "img" => "./assets/owo/paopao/E9BB91E7BABF_2x.png"], ["title" => "::(泪)", "img" => "./assets/owo/paopao/E6B3AA_2x.png"], ["title" => "::(真棒)", "img" => "./assets/owo/paopao/E79C9FE6A392_2x.png"], ["title" => "::(喷)", "img" => "./assets/owo/paopao/E596B7_2x.png"], ["title" => "::(惊哭)", "img" => "./assets/owo/paopao/E6838AE593AD_2x.png"], ["title" => "::(阴险)", "img" => "./assets/owo/paopao/E998B4E999A9_2x.png"], ["title" => "::(鄙视)", "img" => "./assets/owo/paopao/E98499E8A786_2x.png"], ["title" => "::(酷)", "img" => "./assets/owo/paopao/E985B7_2x.png"], ["title" => "::(啊)", "img" => "./assets/owo/paopao/E5958A_2x.png"], ["title" => "::(狂汗)", "img" => "./assets/owo/paopao/E78B82E6B197_2x.png"], ["title" => "::(what)", "img" => "./assets/owo/paopao/what_2x.png"], ["title" => "::(疑问)", "img" => "./assets/owo/paopao/E79691E997AE_2x.png"], ["title" => "::(酸爽)", "img" => "./assets/owo/paopao/E985B8E788BD_2x.png"], ["title" => "::(呀咩蹀)", "img" => "./assets/owo/paopao/E59180E592A9E788B9_2x.png"], ["title" => "::(委屈)", "img" => "./assets/owo/paopao/E5A794E5B188_2x.png"], ["title" => "::(惊讶)", "img" => "./assets/owo/paopao/E6838AE8AEB6_2x.png"], ["title" => "::(睡觉)", "img" => "./assets/owo/paopao/E79DA1E8A789_2x.png"], ["title" => "::(笑尿)", "img" => "./assets/owo/paopao/E7AC91E5B0BF_2x.png"], ["title" => "::(挖鼻)", "img" => "./assets/owo/paopao/E68C96E9BCBB_2x.png"], ["title" => "::(吐)", "img" => "./assets/owo/paopao/E59090_2x.png"], ["title" => "::(犀利)", "img" => "./assets/owo/paopao/E78A80E588A9_2x.png"], ["title" => "::(小红脸)", "img" => "./assets/owo/paopao/E5B08FE7BAA2E884B8_2x.png"], ["title" => "::(懒得理)", "img" => "./assets/owo/paopao/E68792E5BE97E79086_2x.png"], ["title" => "::(勉强)", "img" => "./assets/owo/paopao/E58B89E5BCBA_2x.png"], ["title" => "::(爱心)", "img" => "./assets/owo/paopao/E788B1E5BF83_2x.png"], ["title" => "::(心碎)", "img" => "./assets/owo/paopao/E5BF83E7A28E_2x.png"], ["title" => "::(玫瑰)", "img" => "./assets/owo/paopao/E78EABE791B0_2x.png"], ["title" => "::(礼物)", "img" => "./assets/owo/paopao/E7A4BCE789A9_2x.png"], ["title" => "::(彩虹)", "img" => "./assets/owo/paopao/E5BDA9E899B9_2x.png"], ["title" => "::(太阳)", "img" => "./assets/owo/paopao/E5A4AAE998B3_2x.png"], ["title" => "::(星星月亮)", "img" => "./assets/owo/paopao/E6989FE6989FE69C88E4BAAE_2x.png"], ["title" => "::(钱币)", "img" => "./assets/owo/paopao/E992B1E5B881_2x.png"], ["title" => "::(茶杯)", "img" => "./assets/owo/paopao/E88CB6E69DAF_2x.png"], ["title" => "::(蛋糕)", "img" => "./assets/owo/paopao/E89B8BE7B395_2x.png"], ["title" => "::(大拇指)", "img" => "./assets/owo/paopao/E5A4A7E68B87E68C87_2x.png"], ["title" => "::(胜利)", "img" => "./assets/owo/paopao/E8839CE588A9_2x.png"], ["title" => "::(haha)", "img" => "./assets/owo/paopao/haha_2x.png"], ["title" => "::(OK)", "img" => "./assets/owo/paopao/OK_2x.png"], ["title" => "::(沙发)", "img" => "./assets/owo/paopao/E6B299E58F91_2x.png"], ["title" => "::手纸", "img" => "./assets/owo/paopao/E6898BE7BAB8_2x.png"], ["title" => "::(香蕉)", "img" => "./assets/owo/paopao/E9A699E89589_2x.png"], ["title" => "::(便便)", "img" => "./assets/owo/paopao/E4BEBFE4BEBF_2x.png"], ["title" => "::(药丸)", "img" => "./assets/owo/paopao/E88DAFE4B8B8_2x.png"], ["title" => "::(红领巾)", "img" => "./assets/owo/paopao/E7BAA2E9A286E5B7BE_2x.png"], ["title" => "::(蜡烛)", "img" => "./assets/owo/paopao/E89CA1E7839B_2x.png"], ["title" => "::(音乐)", "img" => "./assets/owo/paopao/E99FB3E4B990_2x.png"], ["title" => "::(灯泡)", "img" => "./assets/owo/paopao/E781AFE6B3A1_2x.png"]];
for ($i = 0; $i < count($arr); $i++) {
	$danm = $arr[$i]["title"];
	$dang = $arr[$i]["img"];
	$aeimg = "<span class=\"sh-nr-bq-img-wk\"><img src=\"./assets/img/thumbnail.svg\" data-src=\"" . $dang . "\" class=\"sh-nr-bq-img\" alt=\"" . $danm . "\"></span>";
	$textes = str_ireplace($danm, $aeimg, $textes);
}
$text = $textes;
if ($zjzhq == $glyusername) {
	$ptpaud = 0;
	$ptpaudzt = 1;
} else {
	if ($ptpaud == 0) {
		$ptpaudzt = 1;
	} else {
		$ptpaudzt = 0;
	}
}
if ($imageMode !== 'qiniu' && !is_dir("../upload/")) {
	mkdir("../upload/");
}
if ($edlx == "edites") {
	if ($edwzcid != "") {
		$sql = "select * from essay where cid = '{$edwzcid}'";
		$result = $conn->query($sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$edbjwzid = $row["id"];
			$ptpimag = $row["ptpimag"];
		} else {
			$referer = $_SERVER["HTTP_REFERER"];
			exit("<script language=\"JavaScript\">;alert(\"文章不存在\");location.href=\"" . $referer . "\";</script>;");
		}
	} else {
		$referer = $_SERVER["HTTP_REFERER"];
		exit("<script language=\"JavaScript\">;alert(\"未传入文章数据\");location.href=\"" . $referer . "\";</script>;");
	}
	if ($zjzhq == $glyadmin) {
	} else {
		$sql = "select * from essay where cid= '{$edwzcid}'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$ptpuser = $row["ptpuser"];
		} else {
			$referer = $_SERVER["HTTP_REFERER"];
			exit("<script language=\"JavaScript\">;alert(\"未获取到文章数据\");location.href=\"" . $referer . "\";</script>;");
		}
		if ($zjzhq == $ptpuser) {
		} else {
			$referer = $_SERVER["HTTP_REFERER"];
			exit("<script language=\"JavaScript\">;alert(\"您无权操作其他用户的文章\");location.href=\"" . $referer . "\";</script>;");
		}
	}
	function isTimeString($str)
	{
		$format = "Y-m-d H:i:s";
		$dateTime = DateTime::createFromFormat($format, $str);
		return $dateTime && $dateTime->format($format) === $str;
	}
	$isTime = isTimeString($fbtime);
	if ($isTime) {
		$wftime = ",ptptime='{$fbtime}'";
	} else {
		$wftime = "";
	}
	$updatedImages = $ptpimag;
	$updatedVideo = (string) ($row["ptpvideo"] ?? '');
	$updatedType = (string) ($row["ptplx"] ?? 'only');
	if ($removeImages) {
		$updatedImages = '';
		if ($updatedType === 'img') {
			$updatedType = 'only';
		}
	} elseif ($imageMode === 'qiniu' && count($base64Arr) > 0) {
		$updatedImages = implode('(+@+)', form_qiniu_urls($base64Arr, $conn, $edwzcid));
		$updatedVideo = '';
	} elseif ($imageMode === 'external' && $imgul !== '') {
		$arrwlt = form_external_urls($imgul);
		if (count($arrwlt) > 15) {
			exit("<script>alert('图片外链不可超过15条');location.href='../edit.php?cid={$edwzcid}';</script>");
		}
		foreach ($arrwlt as $imageUrl) {
			$scheme = strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME));
			if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false || !in_array($scheme, ['http', 'https'], true)) {
				exit("<script>alert('请输入正确的 http/https 图片链接');location.href='../edit.php?cid={$edwzcid}';</script>");
			}
		}
		if (count($arrwlt) === 1 && form_is_video_url($arrwlt[0])) {
			$updatedImages = '';
			$updatedVideo = $arrwlt[0] . '|';
			$updatedType = 'video';
		} else {
			foreach ($arrwlt as $imageUrl) {
				if (form_is_video_url($imageUrl)) {
					exit("<script>alert('视频图链请单独输入，一篇文章仅支持一个视频');location.href='../edit.php?cid={$edwzcid}';</script>");
				}
			}
			$updatedImages = implode('(+@+)', $arrwlt);
			$updatedVideo = '';
			$updatedType = 'img';
		}
	} elseif ($imageMode === 'upload' && is_array($base64Arr) && count($base64Arr) > 0) {
		$arr = [];
		foreach ($base64Arr as $base64) {
			$base64Image = str_replace(' ', '+', $base64);
			if (!preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64Image, $imageMatch)) {
				exit("<script>alert('未匹配到图片文件,请检查文件格式');location.href='../edit.php?cid={$edwzcid}';</script>");
			}
			$type = strtolower($imageMatch[2]);
			if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
				exit("<script>alert('不支持该图片格式');location.href='../edit.php?cid={$edwzcid}';</script>");
			}
			$fileName = str_replace('.', '', microtime(true)) . mt_rand() . substr(md5($zjzhq), 0, 12) . '.' . $type;
			$newFile = '../upload/' . $fileName;
			if (file_put_contents($newFile, base64_decode(str_replace($imageMatch[1], '', $base64Image), true)) === false) {
				exit("<script>alert('写入文件失败,请检查服务器权限');location.href='../edit.php?cid={$edwzcid}';</script>");
			}
			$arr[] = './upload/' . $fileName;
		}
		$updatedImages = implode('(+@+)', $arr);
		$updatedVideo = '';
	}
	$updatedImages = mysqli_real_escape_string($conn, $updatedImages);
	if ($updatedType !== 'video') {
		$updatedType = $updatedImages === '' ? 'only' : 'img';
	}
	$updatedVideo = mysqli_real_escape_string($conn, $updatedVideo);
	$sql = "UPDATE essay SET ptptext='{$text}',ptpimag='{$updatedImages}',ptpvideo='{$updatedVideo}',ptplx='{$updatedType}',ptpdw='{$dw}' {$wftime},ptpgg='{$gg}',ptpggurl='{$gglj}',commauth='{$yxplkg}' WHERE id='{$edbjwzid}'";
	$result = $conn->query($sql);
	if ($result) {
		unset($_SESSION["allkey"]);
		header("location:../view.php?cid={$edwzcid}");
	} else {
		$referer = $_SERVER["HTTP_REFERER"];
		exit("<script language=\"JavaScript\">;alert(\"文章编辑出错,请重试\");location.href=\"" . $referer . "\";</script>;");
	}
	exit;
}
if ($lx == 1) {
	$tuplj = '';
	$wzlx = 'only';
	if ($imageMode === 'qiniu') {
		$qiniuUrls = form_qiniu_urls($base64Arr, $conn);
		if (count($qiniuUrls) > 0) {
			$tuplj = mysqli_real_escape_string($conn, implode("(+@+)", $qiniuUrls));
			$wzlx = "img";
		}
	} elseif ($imageMode === 'external' && $imgul != "") {
		$arrwlt = form_external_urls($imgul);
		if (count($arrwlt) > 15) {
			exit("<script language=\"JavaScript\">;alert(\"不可超过15张图片!\");location.href=\"../edit.php\";</script>;");
		}
		for ($i = 0; $i < count($arrwlt); $i++) {
			if (filter_var($arrwlt[$i], FILTER_VALIDATE_URL) === false || !in_array(strtolower((string) parse_url($arrwlt[$i], PHP_URL_SCHEME)), ['http', 'https'], true)) {
				exit("<script language=\"JavaScript\">;alert(\"请输入正确的图片链接!\");location.href=\"../edit.php\";</script>;");
			}
		}
		if (count($arrwlt) === 1 && form_is_video_url($arrwlt[0])) {
			$wzlx = 'video';
			$spljd = mysqli_real_escape_string($conn, $arrwlt[0] . '|');
		} else {
			foreach ($arrwlt as $imageUrl) {
				if (form_is_video_url($imageUrl)) {
					exit("<script>alert('视频图链请单独输入，一篇文章仅支持一个视频');location.href='../edit.php';</script>");
				}
			}
			$wzlx = "img";
			$tuplj = mysqli_real_escape_string($conn, implode("(+@+)", $arrwlt));
		}
	} elseif ($imageMode === 'upload') {
		if (count($base64Arr) > 0) {
			$arr = [];
			foreach ($base64Arr as $key => $base64) {
				$base64Image = str_replace(" ", "+", $base64);
				if (preg_match("/^(data:\\s*image\\/(\\w+);base64,)/", $base64Image, $result)) {
					$type = $result[2];
					$filePath = "../upload/";
					if (!file_exists($filePath)) {
						mkdir($filePath, 493);
					}
					$fileName = str_replace(".", "", microtime(true)) . mt_rand() . substr(md5($zjzhq), 0, 12);
					$newFile = $filePath . $fileName . ".{$type}";
					if (file_put_contents($newFile, base64_decode(str_replace($result[1], "", $base64Image)))) {
						array_push($arr, "./upload/" . $fileName . ".{$type}");
						// Compression is opt-in; never process uploads when the stored switch is off.
						if (isset($imgpres) && (int) $imgpres === 1) {
							compress($newFile, $newFile);
						}
					} else {
						exit("<script>alert('写入文件失败,请检查服务器权限');location.href='../edit.php';</script>");
						exit("error");
					}
				} else {
					exit("<script>alert('未匹配到图片文件,请检查文件格式');location.href='../edit.php';</script>");
					exit("error");
				}
			}
			$tuplj = implode("(+@+)", $arr);
			$wzlx = "img";
		}
	}
	if ($text === '' && $tuplj === '' && $wzlx !== 'video') {
		exit("<script language=\"JavaScript\">;alert(\"请输入内容或上传图片!\");location.href=\"../edit.php\";</script>;");
	}
	$bdm = "essay";
	$externalVideo = isset($spljd) && $wzlx === 'video' ? $spljd : '';
	$sql = "INSERT INTO {$bdm} (ptpuser,ptpimg,ptpname,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,ip,cid)
	VALUES ('{$zjzhq}','{$zjimg}','{$zjnc}','{$text}','{$tuplj}','{$externalVideo}','','{$wzlx}','{$dw}','{$sj}','{$gg}','{$gglj}','1','{$yxplkg}','{$ptpaudzt}','{$ip}','{$str}')";
	if ($conn->query($sql) === true) {
		if ($ptpaud == 0) {
			header("Location: ../index.php");
		} else {
			$fsyjtzemail = 1;
			unset($_SESSION["allkey"]);
			?><script>alert('文章发布成功,审核通过后即可显示!');location.href='../index.php';</script><?php 
		}
	} else {
		?><script>alert('文章发布失败,写入数据库出错');location.href='../edit.php';</script><?php 
	}
}
if ($lx == 2) {
	$wzlx = "video";
	if ($text == "") {
		exit("<script language=\"JavaScript\">;alert(\"请输入内容!\");location.href=\"../edit.php\";</script>;");
	}
	if ($videoMode === 'external') {
		if ($spp === '') {
			exit("<script>alert('请填写视频外链');location.href='../edit.php';</script>");
		}
		$videoScheme = strtolower((string) parse_url($spp, PHP_URL_SCHEME));
		if (filter_var($spp, FILTER_VALIDATE_URL) === false || !in_array($videoScheme, ['http', 'https'], true)) {
			exit("<script>alert('请输入正确的 http/https 视频外链');location.href='../edit.php';</script>");
		}
		if ($sppfm !== '') {
			$coverScheme = strtolower((string) parse_url($sppfm, PHP_URL_SCHEME));
			if (filter_var($sppfm, FILTER_VALIDATE_URL) === false || !in_array($coverScheme, ['http', 'https'], true)) {
				exit("<script>alert('请输入正确的 http/https 视频封面外链');location.href='../edit.php';</script>");
			}
		}
		$spljd = $spp . "|" . $sppfm;
	} else {
		if (!isset($_FILES["file"]) || $_FILES["file"]["name"] === '' || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
			exit("<script>alert('请上传视频');location.href='../edit.php';</script>");
		}
		$fileEx = strtolower(substr(strrchr($_FILES["file"]["name"], "."), 1));
		$allowedVideoTypes = ["video/mp4", "video/mpeg", "video/quicktime", "video/x-msvideo", "video/x-flv"];
		$uploadedFileType = $_FILES["file"]["type"];
		if (in_array($uploadedFileType, $allowedVideoTypes)) {
		} else {
			exit("<script language=\"JavaScript\">alert(\"文件类型错误,请上传视频!\");location.href=\"../edit.php\";</script>");
		}
		$fileName = str_replace(".", "", microtime(true)) . mt_rand() . substr(md5($zjzhq), 0, 12) . "." . $fileEx;
		move_uploaded_file($_FILES["file"]["tmp_name"], "../upload/" . $fileName);
		$spljd = "/upload/" . $fileName . "|" . $sppfm;
	}
	$bdm = "essay";
	$sql = "INSERT INTO {$bdm} (ptpuser,ptpimg,ptpname,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,ip,cid)
    VALUES ('{$zjzhq}','{$zjimg}','{$zjnc}','{$text}','{$tuplj}','{$spljd}','','{$wzlx}','{$dw}','{$sj}','{$gg}','{$gglj}','1','{$yxplkg}','{$ptpaudzt}','{$ip}','{$str}')";
	if ($conn->query($sql) === true) {
		if ($ptpaud == 0) {
			header("Location: ../index.php");
		} else {
			$fsyjtzemail = 1;
			unset($_SESSION["allkey"]);
			?><script>alert('文章发布成功,审核通过后即可显示!');location.href='../index.php';</script><?php 
		}
	} else {
		?><script>alert('文章发布失败,写入数据库出错');location.href='../edit.php';</script><?php 
	}
}
if ($lx == 3) {
	$wzlx = "music";
	if ($text == "") {
		exit("<script language=\"JavaScript\">;alert(\"请输入内容!\");location.href=\"../edit.php\";</script>;");
	}
	if ($music == "") {
		exit("<script language=\"JavaScript\">;alert(\"请输入音乐链接!\");location.href=\"../edit.php\";</script>;");
	}
	if ($musicm == "") {
		exit("<script language=\"JavaScript\">;alert(\"请输入歌名!\");location.href=\"../edit.php\";</script>;");
	}
	if ($musics == "") {
		exit("<script language=\"JavaScript\">;alert(\"请输入歌手!\");location.href=\"../edit.php\";</script>;");
	}
	if ($musict != "") {
		$pattern = "/^(http|https):\\/\\/[a-zA-Z0-9\\-\\.]+\\.[a-zA-Z]{2,}(\\/\\S*)?\$/";
		if (!preg_match($pattern, $musict)) {
			$musict = "./assets/img/musicba.jpg";
		}
	}
	if (is_numeric($music)) {
		$music = "//music.163.com/song/media/outer/url?id=" . $music . ".mp3";
	}
	$music = $music . "|" . $musicm . "|" . $musics . "|" . $musict;
	$bdm = "essay";
	$sql = "INSERT INTO {$bdm} (ptpuser,ptpimg,ptpname,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,ip,cid)
    VALUES ('{$zjzhq}','{$zjimg}','{$zjnc}','{$text}','{$tuplj}','','{$music}','{$wzlx}','{$dw}','{$sj}','{$gg}','{$gglj}','1','{$yxplkg}','{$ptpaudzt}','{$ip}','{$str}')";
	if ($conn->query($sql) === true) {
		if ($ptpaud == 0) {
			header("Location: ../index.php");
		} else {
			$fsyjtzemail = 1;
			unset($_SESSION["allkey"]);
			?><script>alert('文章发布成功,审核通过后即可显示!');location.href='../index.php';</script><?php 
		}
	} else {
		exit("文章发布失败，请稍后重试。");
	}
}
if ($fsyjtzemail == 1) {
	$data_resultgl = mysqli_query($conn, "select * from user where username='{$glyusername}'");
	$data_rowgl = mysqli_fetch_array($data_resultgl);
	$glydem = $data_rowgl["email"];
	$mailapfs = "nopost";
	$mailtitle = "[" . $wzname . "]" . "您有新的待审核文章!";
	$title = "待审核的文章 - 文章主题：";
	$text = $emwz;
	$mailbox = $glydem;
	include "./sendmail.php";
}
$conn->close();
function form_qiniu_urls(array $values, mysqli $conn, string $cid = ''): array
{
	$settings = qiniu_settings($conn);
	if (!qiniu_valid_settings($settings)) {
		exit("<script>alert('七牛云配置不完整，请先在后台保存配置');location.href='../edit.php" . ($cid !== '' ? '?cid=' . rawurlencode($cid) : '') . "';</script>");
	}
	$urls = [];
	foreach ($values as $value) {
		$url = trim((string) $value);
		if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false || !in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true) || !qiniu_key_matches_domain($url, $settings)) {
			exit("<script>alert('七牛云图片地址无效');location.href='../edit.php" . ($cid !== '' ? '?cid=' . rawurlencode($cid) : '') . "';</script>");
		}
		$urls[] = $url;
	}
	$urls = array_values(array_unique($urls));
	if (count($urls) > 15) {
		exit("<script>alert('图片不可超过15张');location.href='../edit.php" . ($cid !== '' ? '?cid=' . rawurlencode($cid) : '') . "';</script>");
	}
	return $urls;
}

function filterEmoji($str)
{
	$str = preg_replace_callback("/./u", function (array $match) {
		return strlen($match[0]) >= 4 ? "" : $match[0];
	}, $str);
	return $str;
}
/** Parse one URL per line. Commas are also accepted for pasted URL lists. */
function form_external_urls($value)
{
	$items = preg_split('/(?:\R|[,，])+/u', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
	return array_values(array_unique(array_filter(array_map('trim', $items), static function ($url) {
		return $url !== '';
	})));
}

/** Detect direct browser-playable video URLs while ignoring query strings/fragments. */
function form_is_video_url($url)
{
	$path = (string) parse_url((string) $url, PHP_URL_PATH);
	$extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
	return in_array($extension, ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'm4v'], true);
}

function parseUrls($string)
{
	$urls = [];
	$pattern = "/\\b(?:https?:\\/\\/|www\\.)([a-z0-9.-]+\\.[a-z]{2,4}(?:\\/[^\\s]*)?)/i";
	preg_match_all($pattern, $string, $matches);
	foreach ($matches[0] as $url) {
		if (strpos($url, "http://") !== 0 && strpos($url, "https://") !== 0) {
			$url = "http://" . $url;
		}
		$urls[] = "<a href=\"" . $url . "\" target=\"_blank\" class=\"sh-wz-nr-url\">" . $url . "</a>";
	}
	$string = str_replace($matches[0], $urls, $string);
	return $string;
}
function rotateAndSaveImage($source_image, $destination_image)
{
	$exif = exif_read_data($source_image);
	$orientation = isset($exif["Orientation"]) ? $exif["Orientation"] : null;
	$image = imagecreatefromjpeg($source_image);
	switch ($orientation) {
		case 3:
			$image = imagerotate($image, 180, 0);
			break;
		case 6:
			$image = imagerotate($image, -90, 0);
			break;
		case 8:
			$image = imagerotate($image, 90, 0);
			break;
	}
	imagejpeg($image, $destination_image);
	imagedestroy($image);
}
function compress($source_image, $compressed_image)
{
	$extension = pathinfo($source_image, PATHINFO_EXTENSION);
	if ($extension == "gif" || $extension == "webp") {
		return null;
	}
	if (!file_exists($source_image)) {
		trigger_error("Source image not found: {$source_image}", E_USER_ERROR);
		return null;
	}
	$image_info = @getimagesize($source_image);
	if ($image_info === false || $image_info["mime"] == "image/webp" || $image_info["mime"] == "image/gif") {
		return null;
	}
	$source_image = $source_image;
	$destination_image = $compressed_image;
	$source_image = $source_image;
	$compressed_image = $compressed_image;
	$compression_quality = 75;
	list($width, $height, $type) = getimagesize($source_image);
	switch ($type) {
		case IMAGETYPE_JPEG:
			$source = imagecreatefromjpeg($source_image);
			break;
		case IMAGETYPE_PNG:
			$source = imagecreatefrompng($source_image);
			break;
		case IMAGETYPE_GIF:
			$source = imagecreatefromgif($source_image);
			break;
		case IMAGETYPE_JPG:
			$source = imagecreatefromgif($source_image);
			break;
	}
	$new_width = $width / 2;
	$new_height = $height / 2;
	$destination = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	imagejpeg($destination, $compressed_image, $compression_quality);
	imagedestroy($source);
	imagedestroy($destination);
}
