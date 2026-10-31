<?php
// 引入PHPMailer的核心文件
$username = $_POST['username'];
$emydz = $_POST['emydz'];
$emssl = $_POST['emssl'];
$emduk = $_POST['emduk'];
$key = $_POST['key'];
$fromuser = $_POST['fromuser'];
$fromname = $_POST['fromname'];
$title = $_POST['title'];
$nr = $_POST['nr'];
$reveuser = $_POST["reveuser"];
if ($username==""&&$emydz==""&&$emssl==""&&$emduk==""&&$key==""&&$fromuser==""&&$fromname==""&&$title==""&&$nr==""&&$reveuser=="") {
    $word = "发送邮件[时间]:".date('Y-m-d H:i:s')." | 发送状态[发送失败,参数不完整] | 发送给[$reveuser]\n";
    $fh = fopen("log.txt", "a");
    fwrite($fh, $word);
    fclose($fh);
	exit("发送失败,参数不完整");
}

toMail($fromname,$username,$emydz,$emssl,$emduk,$key,$fromuser,$reveuser,$title,$nr);

function toMail($fromname,$username,$emydz,$emssl,$emduk,$key,$fromuser,$reveuser,$title,$nr){
require_once("PHPMailer/class.phpmailer.php");
require_once("PHPMailer/class.smtp.php");

// 实例化PHPMailer核心类
$mail = new PHPMailer();
// 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
//$mail->SMTPDebug = 1;
// 使用smtp鉴权方式发送邮件
$mail->isSMTP();
// smtp需要鉴权 这个必须是true
$mail->SMTPAuth = true;
// 链接qq域名邮箱的服务器地址
$mail->Host = $emydz;//<<<<<<<<<<<<<<------------------修改为你的邮箱服务器地址 如:smtp.qq.com  smtp.exmail.qq.com
// 设置使用ssl加密方式登录鉴权
$mail->SMTPSecure = $emssl;
// 设置ssl连接smtp服务器的远程服务器端口号
$mail->Port = $emduk;
// 设置发送的邮件的编码
$mail->CharSet = 'UTF-8';
// 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
$mail->FromName = $fromname;
// smtp登录的账号 QQ邮箱即可
$mail->Username = $username;
// smtp登录的密码 使用生成的授权码
$mail->Password = $key;
// 设置发件人邮箱地址 同登录账号
$mail->From = $fromuser;
// 邮件正文是否为html编码 注意此处是一个方法
$mail->isHTML(true);
// 设置收件人邮箱地址
$mail->addAddress($reveuser);
// 添加多个收件人 则多次调用方法即可
// 添加该邮件的主题
$mail->Subject = $title;
// 添加邮件正文
$mail->Body = $nr;
// 发送邮件 返回状态
   if(!$mail->send()) {
       $word = "发送邮件[时间]:".date('Y-m-d H:i:s')." | 发送状态[发送失败,返回状态错误,请检查邮箱配置或收件邮箱是否正确] | 发送给[$reveuser]\n";
       $fh = fopen("log.txt", "a");
       fwrite($fh, $word);
       fclose($fh);
     	exit("发送失败");
    } else {
        $word = "发送邮件[时间]:".date('Y-m-d H:i:s')." | 发送状态[发送成功] | 发送给[$reveuser]\n";
        $fh = fopen("log.txt", "a");
        fwrite($fh, $word);
        fclose($fh);
      	exit("发送成功");
    }
}

?>