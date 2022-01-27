<?php

session_start();
session_destroy();
setcookie ("cookielogin",$_POST["email"],time()- 3600);
setcookie ("cookielogin",$_POST["password"],time()- 3600);
header("Location: login.php");

?>
