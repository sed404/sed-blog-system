<?php

//decode by nige112
$iteace = "adminapi";
$user_zh = $_COOKIE["username"];
$user_passid = $_COOKIE["passid"];
if ($user_zh == "" || $user_passid == "") {
	exit("<script language=\"JavaScript\">;alert(\"请先登录!\");location.href=\"../login.php\";</script>");
}
include "../../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
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
	$glyzhuser = $row["username"];
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
$zid = $data2_row["id"];
$zjpassword = $data2_row["password"];
if ($user_passid != $passid) {
	setcookie("username", "", time() + -1, "/");
	setcookie("passid", "", time() + -1, "/");
	exit("<script language=\"JavaScript\">;alert(\"账号信息异常,请重新登录!\");location.href=\"../login.php\";</script>");
}
if ($zjzhq != $glyzhuser) {
	exit("<script language=\"JavaScript\">;alert(\"没有权限!\");history.go(-1);</script>");
}
$pass_user = $pass_user_commit;
$re = md5(md5($pass_user));
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
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
$shemail_dshwznr = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Document</title>
</head>
<style>
    html,
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        background: #ecf1f3;
    }
    .main {
        width: 100%;
    /* height: 100%; */
    /* margin-top: 20px;
    margin-bottom: 20px; */
    /* background: #ffffff; */
    display: flex;
    justify-content: center;
    /* flex-direction: column; */
    /* align-items: center;*/
    background: #ecf1f3;
    }
    .main-con{
        width: 800px;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #ffffff;
    box-shadow: 0px 0px 5px 2px #f1f1f1;
    margin-top: 20px;
    margin-bottom: 20px;
    }
    .sh-tu {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .sh-tu-logo {
        width: 200px;
        height: 100px;
        object-fit: contain;
    }
    .sh-wz {
        width: 80%;
        color: #55798d;
    }
    .sh-wz-t {
        margin-bottom: 10px;
        margin-left: 2px;
        font-size: 15px;
    }
    .sh-wz-n {
        width: 100%;
        display: flex;
        min-height: 40px;
        background: #f7f7f7;
        font-size: 15px;
        border-radius: 4px;
    }
    .sh-wz-n-nr {
        width: 100%;
        margin: 10px;
    }
    .sh-wz-b {
        margin-bottom: 10px;
        margin-left: 2px;
        font-size: 15px;
        margin-top: 15px;
    }
    .sh-wz-b2 {
        margin-bottom: 10px;
        margin-left: 2px;
        font-size: 15px;
        margin-top: 50px;
    }
    a {
        text-decoration: none;
        color: #1e5494;
        text-decoration: underline;
    }
</style>
<body>
    <div class=\"main\">
        <div class=\"main-con\">
        <div class=\"sh-tu\">
            <!--img src=\"" . $logo . "\" alt=\"\" class=\"sh-tu-logo\"-->
            <p style=\"font-size: 22px;font-weight: bold;color: #55798d;\">" . $name . "</p>
        </div>
        <div class=\"sh-wz\">
            <div class=\"sh-wz-t\" style=\"color: #55798d;\">邮件主题：</div>
            <div class=\"sh-wz-n\" style=\"background:#f7f7f7\">
                <p class=\"sh-wz-n-nr\" style=\"color: #000000;\">系统测试邮件</p>
            </div>
            <div class=\"sh-wz-b2\" style=\"color: #55798d;\">该邮件为系统测试邮件,若您收到此邮件代表您的网站邮箱配置正确,可以正常收发邮件啦!</div>
            <div style=\"background: #eaf2ff;width: 100%;height: 1px;margin-top: 50px;\"></div>
            <div style=\"font-size: 22px;font-weight: bold;margin-top: 20px;color: #55798d;\">" . $name . "</div>
            <div style=\"margin:20px 0;color: #6a8895;min-height:4.2em;white-space: pre-wrap;\">此信为系统邮件，请不要直接回复。</div>
            <div style=\"margin-bottom: 50px;\"><a href=\"" . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "\" rel=\"noopener\" target=\"_blank\">访问网站</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>";
$post_data = ["emydz" => $emydz, "emssl" => $emssl, "emduk" => $emduk, "key" => $emkey, "username" => $emfs, "fromuser" => $emfs, "fromname" => $emfszm, "title" => "[" . $name . "] 测试邮件!", "nr" => $shemail_dshwznr, "reveuser" => $emfs];
$url = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/site/email/email.php";
$data = $post_data;
$headers = ["Authorization: Basic YWRtaW46YWRtaW4xMjMuLi4=", "Cookie: PHPSESSID=" . session_id(), "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36"];
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($curl, CURLOPT_TIMEOUT, 5);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($curl);
if (curl_errno($curl)) {
}
curl_close($curl);
if ($result == "") {
	exit("<script language=\"JavaScript\">;alert(\"未获取到数据\");history.go(-1);</script>");
} else {
	exit("<script language=\"JavaScript\">;alert(\"" . $result . "\");history.go(-1);</script>");
}
$conn->close();