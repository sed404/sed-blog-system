<?php
session_start();
if (is_file(__DIR__.'/ins.bak') || is_file(__DIR__.'/../config.php')) exit('已安装，禁止重复安装。');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!hash_equals($_SESSION['install_csrf'] ?? '', (string)($_POST['csrf_token'] ?? '')) || empty($_SESSION['install_csrf'])) { http_response_code(403); exit('请返回安装页面重试。'); }
$guard=fopen(__DIR__.'/install.lock','c');
if (!$guard || !flock($guard,LOCK_EX|LOCK_NB)) exit('安装正在进行。');
if (is_file(__DIR__.'/../config.php')) exit('已安装。');
try {
 mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
 $host=trim((string)($_POST['servername']??'')); $db=trim((string)($_POST['dbname']??''));
 $user=(string)($_POST['username']??''); $password=(string)($_POST['password']??'');
 $admin=trim((string)($_POST['admin']??'')); $pass=(string)($_POST['admin_password']??'');
 if(!preg_match('/^[a-zA-Z0-9_]{5,32}$/D',$admin)||strlen($pass)<8||strlen($pass)>72) throw new RuntimeException('管理员账号须为5至32位字母、数字或下划线；密码须为8至72字节。');
 if(!is_writable(dirname(__DIR__))) throw new RuntimeException('网站根目录不可写。');
 $conn=new mysqli($host,$user,$password,$db); $conn->set_charset('utf8mb4');
 if($conn->query('SHOW TABLES')->num_rows>0) throw new RuntimeException('请使用空数据库，避免覆盖数据。');
 $files=array_merge([__DIR__.'/base.sql'],glob(__DIR__.'/../migrations/[0-9]*.sql'));
 foreach($files as $file){$conn->multi_query(file_get_contents($file));do{if($r=$conn->store_result())$r->free();}while($conn->more_results()&&$conn->next_result());}
 function seed(mysqli $conn,string $table,array $values):void {
  $fields=[];$data=[];$r=$conn->query('SHOW COLUMNS FROM `'.$table.'`');
  while($c=$r->fetch_assoc()){if($c['Field']==='id')continue;$fields[]='`'.$c['Field'].'`';$data[]=(string)($values[$c['Field']]??'');}
  $stmt=$conn->prepare('INSERT INTO `'.$table.'` ('.implode(',',$fields).') VALUES ('.implode(',',array_fill(0,count($data),'?')).')');$stmt->bind_param(str_repeat('s',count($data)),...$data);$stmt->execute();
 }
 $now=date('Y-m-d H:i:s');
 seed($conn,'admin',['name'=>'我的朋友圈','subtitle'=>'分享生活中的瞬间','username'=>$admin,'zt'=>'1','icon'=>'./assets/img/logogolden.svg','logo'=>'./assets/img/logogolden.svg','music'=>'-1','essgs'=>'10','commgs'=>'10','lnkzt'=>'0','regqx'=>'-1','kqsy'=>'0','comaud'=>'1','ptpaud'=>'1','ptpfan'=>'1','loginkg'=>'1','notname'=>'0','imgpres'=>'0','rosdomain'=>'0','daymode'=>'1','gotop'=>'1','search'=>'1','videoauplay'=>'0','regverify'=>'0','viscomm'=>'-1','musplay'=>'1','date'=>$now]);
 seed($conn,'user',['username'=>$admin,'password'=>password_hash($pass,PASSWORD_DEFAULT),'name'=>'管理员','img'=>'./assets/img/logogolden.svg','homeimg'=>'-1','essqx'=>'1','esseam'=>'0','regtime'=>$now,'logtime'=>$now,'ban'=>'0','bantime'=>'false','passid'=>bin2hex(random_bytes(32))]);
 $config="<?php\n";foreach(['servername'=>$host,'username'=>$user,'password'=>$password,'dbname'=>$db] as $key=>$value)$config.='$'.$key.' = '.var_export($value,true).";\n";
 if(file_put_contents(__DIR__.'/../config.php',$config,LOCK_EX)===false)throw new RuntimeException('写入配置失败。');
 file_put_contents(__DIR__.'/ins.bak','installed'); unset($_SESSION['install_csrf']);
 echo '安装成功。<a href="../admin/">登录后台</a>。请删除 install 目录。';
} catch(Throwable $e){http_response_code(500);echo '安装失败：'.htmlspecialchars($e instanceof RuntimeException && !($e instanceof mysqli_sql_exception)?$e->getMessage():'请检查数据库连接、权限及扩展。若已建表，请换用空数据库重试。',ENT_QUOTES,'UTF-8');}
