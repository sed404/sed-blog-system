<?php
setcookie('username','', ['expires'=>time()-3600,'path'=>'/','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);
setcookie('passid','', ['expires'=>time()-3600,'path'=>'/','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);
setcookie('dark_theme_manual','', time()-3600, '/');
header('Location: ./login.php'); exit;
