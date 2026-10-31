<?php

//decode by nige112
include "../config.php";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	$arr = [["code" => "201", "msg" => "连接数据库失败"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
$q = $_POST["q"];
if ($q == "") {
	$arr = [["code" => "201", "msg" => "空参数!"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
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
	$homimg = $row["homimg"];
	$sign = $row["sign"];
	$wzmusic = $row["music"];
	$essgs = $row["essgs"];
	$commgs = $row["commgs"];
	$lnkzt = $row["lnkzt"];
	$regqx = $row["regqx"];
	$kqsy = $row["kqsy"];
}
if ($wzmusic == -1) {
	$arr = [["code" => "201", "mum" => "", "muurl" => "", "msg" => "音乐播放器未启用"]];
	exit(json_encode($arr, JSON_UNESCAPED_UNICODE));
}
require_once __DIR__ . '/../includes/music-playlist.php';
$tracks = life_music_playlist((string) $wzmusic);
$ary = count($tracks);
if ($ary === 0) {
	echo json_encode([["code" => "201", "msg" => "没有可播放的歌曲"]], JSON_UNESCAPED_UNICODE);
	exit;
}
$mu = (((int) ($_SESSION['music_index'] ?? -1)) + 1) % $ary;
$_SESSION['music_index'] = $mu;
$mum = $tracks[$mu]['name'];
$muurl = $tracks[$mu]['url'];
$arr = [["code" => "200", "mum" => $mum, "muurl" => $muurl]];
echo json_encode($arr);
$conn->close();
