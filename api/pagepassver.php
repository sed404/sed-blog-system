<?php
require_once __DIR__ . "/security.php";

//decode by nige112
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	$arr = [["code" => "201", "msg" => "非法请求"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$uspagepass = (string) ($_POST["pagepass"] ?? "");
if ($uspagepass == "") {
	exit("请传入访问密码");
}
include "../config.php";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	exit("连接失败: " . $conn->connect_error);
}
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$pagepass = $row["pagepass"];
}
$conn->close();
if ($pagepass != "") {
	$attempt = life_check_password('page-access', static fn() => hash_equals((string) $pagepass, $uspagepass));
    if ($attempt['retry'] > 0) {
        header('Retry-After: ' . $attempt['retry']);
        exit('连续错误6次，已锁定，请' . ceil($attempt['retry'] / 60) . '分钟后重试');
    }
    if (!$attempt['success']) {
		exit("密码错误");
	} else {
		yxos_set_auth_cookie("pagepass", md5(md5($pagepass)), time() + 604800);
		exit("密码正确");
	}
} else {
	if ($pagepass == "") {
		if (isset($_COOKIE["pagepass"])) {
			setcookie("pagepass", "", time() + 0, "/");
		}
	}
	exit("网站未设置密码");
}