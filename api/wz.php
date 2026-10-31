<?php

//decode by nige112
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

$user_zh = (string) ($_COOKIE["username"] ?? '');
$user_passid = (string) ($_COOKIE["passid"] ?? '');
if ($user_zh == "" || $user_passid == "") {
	$userdlzt = "0";
} else {
	$userdlzt = "1";
}
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接数据库失败");
}
$user_name = '';
$user_img = '';
$user_url = '';
if ($userdlzt === "1") {
	$stmt = $conn->prepare('SELECT username, name, img, url FROM user WHERE username = ? LIMIT 1');
	$stmt->bind_param('s', $user_zh);
	$stmt->execute();
	$data2s_row = $stmt->get_result()->fetch_assoc();
	$stmt->close();
	if ($data2s_row) {
		$user_zh = (string) $data2s_row["username"];
		$user_name = (string) $data2s_row["name"];
		$user_img = (string) $data2s_row["img"];
		$user_url = (string) $data2s_row["url"];
	} else {
		$userdlzt = "0";
		$user_zh = '';
		$user_passid = '';
	}
}
if (!isset($iteace)) {
	exit("请设置状态!");
}
$appRoot = dirname(__DIR__);
if ($iteace == "0") {
	if (is_dir($appRoot . "/install")) {
		if (!is_file($appRoot . "/install/ins.bak")) {
			exit("请先安装程序！<a href=\"./install/\">点击安装</a>");
		}
	}
	if (!is_file($appRoot . "/config.php")) {
		exit("未检测到配置文件：<span style=\"color: red;\">config.php</span>，若您是首次使用请先进行安装,删除文件夹 <span style=\"color: red;\">[install/]</span> 下的 <span style=\"color: red;\">[ins.bak]</span> 文件后进行安装。");
	}

	$pass_user = $pass_user_commit ?? '';
	$re = md5(md5($pass_user));
} elseif ($iteace == "1") {
	if (is_dir($appRoot . "/install")) {
		if (!is_file($appRoot . "/install/ins.bak")) {
			exit("请先安装程序！<a href=\"../install/\">点击安装</a>");
		}
	}
	if (!is_file($appRoot . "/config.php")) {
		exit("未检测到配置文件：<span style=\"color: red;\">config.php</span>，若您是首次使用请先进行安装,删除文件夹 <span style=\"color: red;\">[install/]</span> 下的 <span style=\"color: red;\">[ins.bak]</span> 文件后进行安装。");
	}
	
	$pass_user = $pass_user_commit ?? '';
	$re = md5(md5($pass_user));
} elseif ($iteace == "2") {
	$pass_user = $pass_user_commit ?? '';
	$re = md5(md5($pass_user));

	if (!extension_loaded("sg11")) {
		exit("sg11扩展未安装");
	}
	if (!extension_loaded("exif")) {
		exit("exif扩展未安装");
	}
} else {
	exit("状态错误!");
}
$lifeConfig = [];
$configResult = $conn->query("SELECT title, text FROM configx WHERE title IN ('default_theme','filtertext','qiniu','site_background','piccir','cucss','cujs','snow_enabled','home_notification_enabled')");
while ($configResult && ($configRow = $configResult->fetch_assoc())) $lifeConfig[$configRow['title']] = (string) $configRow['text'];
if (!isset($_COOKIE["dark_theme_manual"])) {
	// Apply the administrator's default theme before any page renders its body.
	$defaultTheme = 'light';
	$defaultTheme = $lifeConfig['default_theme'] ?? 'light';
	$themeCookie = $defaultTheme === 'dark' ? 'dark-theme' : 'root';
	setcookie("dark_theme", $themeCookie, 0, "/", "", !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', false);
	$_COOKIE['dark_theme'] = $themeCookie;
}
// Theme is a presentation preference, not an authentication cookie. Reissue it
// without HttpOnly so existing visitors can also switch themes in the browser.
$themePreference = ($_COOKIE['dark_theme'] ?? '') === 'dark-theme' ? 'dark-theme' : 'root';
setcookie('dark_theme', $themePreference, ['expires'=>0, 'path'=>'/', 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'httponly'=>false, 'samesite'=>'Lax']);
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$name = $row["name"];
	$subtitle = $row["subtitle"];
	$icon = $row["icon"];
	$logo = $row["logo"];
	$zt = $row["zt"];
	$username = $row["username"];
	$glyadmin = $row["username"];
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$wzmusic = $row["music"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$lnkzt = $row["lnkzt"];
	$regqx = $row["regqx"];
	$kqsy = $row["kqsy"];
	$comaud = $row["comaud"];
	$ptpaud = $row["ptpaud"];
	$ptpfan = $row["ptpfan"];
	$loginkg = $row["loginkg"];
	$notname = $row["notname"];
	$imgpres = $row["imgpres"];
	$rosdomain = $row["rosdomain"];
	$daymode = $row["daymode"];
	$gotop = $row["gotop"];
	$search = $row["search"];
	$videoauplay = $row["videoauplay"];
	$regverify = $row["regverify"];
	$pagepass = $row["pagepass"];
	$emydz = $row["emydz"];
	$emssl = $row["emssl"];
	$emduk = $row["emduk"];
	$emkey = $row["emkey"];
	$emzh = $row["emzh"];
	$emfs = $row["emfs"];
	$emfszm = $row["emfszm"];
	$copyright = $row["copyright"];
	$beian = $row["beian"];
	$topes = $row["topes"];
	$scfont = $row["scfont"];
	$viscomm = $row["viscomm"];
	$musplay = $row["musplay"];
	$date = $row["date"];
}
if ($pagepass == "") {
	if (isset($_COOKIE["pagepass"])) {
		setcookie("pagepass", "", time() + 0, "/");
	}
}
$filtertext = ($lifeConfig['filtertext'] ?? '');
$filterqiniu = ($lifeConfig['qiniu'] ?? '');
$siteBackground = trim($lifeConfig['site_background'] ?? '');
if ($siteBackground === '') {
	foreach (array('jpg', 'jpeg', 'png', 'gif', 'webp') as $legacyBackgroundExtension) {
		$legacyBackgroundPath = './user/bobg/bobg.' . $legacyBackgroundExtension;
		if (is_file('../' . $legacyBackgroundPath) || is_file($legacyBackgroundPath)) {
			$siteBackground = $legacyBackgroundPath;
			break;
		}
	}
}
$filterpiccir = ($lifeConfig['piccir'] ?? '');
$filtercucss = htmlspecialchars_decode($lifeConfig['cucss'] ?? '');
$filtercujs = htmlspecialchars_decode($lifeConfig['cujs'] ?? '');
if ($iteace == "0") {
	if ($zt != 1) {
		header("Location: ../404.php?close=true");
		exit;
	}
} elseif ($iteace == "1") {
} else {
	exit("状态错误!");
}
$ip = (string) ($_SERVER["REMOTE_ADDR"] ?? 'Unknown');
$length = 64;
$allkey = '';
$visykmz_userzh = "游客" . generateRandomString(5);
$visykmz_userip = "vis#-[" . $ip . "]-#vis";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION["allkey"])) {
    $_SESSION["allkey"] = generateRandomString($length);
}
$allkey = (string) $_SESSION["allkey"];
if (empty($_SESSION["visykmz_userzh"]) || empty($_SESSION["visykmz_userip"])) {
	$_SESSION["visykmz_userzh"] = $visykmz_userzh;
	$_SESSION["visykmz_userip"] = $visykmz_userip;
}
if ($scfont != "") {
	$scfontzt = "<style>
        @font-face {
          font-family: \"MyFont\"; 
          src: url(\"" . $scfont . "\"); 
        }
        body{
            font-family: \"MyFont\";
        }
        </style>";
} else {
	$scfontzt = "";
}
$stmt = $conn->prepare('SELECT username, name, img FROM user WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$data_row = $stmt->get_result()->fetch_assoc() ?: [];
$stmt->close();
$glyname = (string) ($data_row["name"] ?? '');
$glyimg = (string) ($data_row["img"] ?? '');
$glyuserzha = (string) ($data_row["username"] ?? '');

$data2_row = [];
if ($userdlzt === "1") {
	$stmt = $conn->prepare('SELECT * FROM user WHERE username = ? LIMIT 1');
	$stmt->bind_param('s', $user_zh);
	$stmt->execute();
	$data2_row = $stmt->get_result()->fetch_assoc() ?: [];
	$stmt->close();
}
$zjzhq = (string) ($data2_row["username"] ?? '');
$zjnc = (string) ($data2_row["name"] ?? '');
$zjimg = (string) ($data2_row["img"] ?? '');
$zjhomeimg = (string) ($data2_row["homeimg"] ?? '-1');
$zjsign = (string) ($data2_row["sign"] ?? '');
$zjemail = (string) ($data2_row["email"] ?? '');
$zjurl = (string) ($data2_row["url"] ?? '');
$zjfbqx = (string) ($data2_row["essqx"] ?? '0');
$zjemtz = (string) ($data2_row["esseam"] ?? '0');
$passid = (string) ($data2_row["passid"] ?? '');
$ban = (string) ($data2_row["ban"] ?? '0');
$bantime = (string) ($data2_row["bantime"] ?? 'false');
if ($userdlzt == 1) {
	if ($ban != "0" && $bantime != "false") {
		if ($bantime === "true") {
			setcookie("username", "", time() + -1, "/");
			setcookie("passid", "", time() + -1, "/");
			exit("<script language=\"JavaScript\">;alert(\"该账号涉嫌违规,已被永久封禁!\");location.href=\"./index.php\";</script>");
		} else {
			setcookie("username", "", time() + -1, "/");
			setcookie("passid", "", time() + -1, "/");
			exit("<script language=\"JavaScript\">;alert(\"你的账号已被封禁！解封时间为:" . $bantime . "\");location.href=\"./index.php\";</script>");
		}
	}
}
if ($userdlzt === "1" && !hash_equals($passid, $user_passid)) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"./index.php\";</script>");
}
$iconurl_url = "//at.alicdn.com/t/c/font_3781624_acf7eqdy5ke.css";
// Stable local asset version; normal page loads never contact the obsolete updater.
$resversion = (string) max(filemtime(__FILE__), filemtime(__DIR__ . '/../assets/css/style.css'), filemtime(__DIR__ . '/../assets/js/index.js'));
function generateRandomString($length)
{
	$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$randomString = "";
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}
