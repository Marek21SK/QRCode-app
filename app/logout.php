<?php
include 'config/common.php';
// Po odhlásení používateľa zničí session daného užívateľa a redirektne na login.php
//session_start();
var_dump($_SESSION);
//die;
//unset($_SESSION['user_id']);
session_destroy();
header("Location: /qrcode-app/app/index.php");
exit();
?>