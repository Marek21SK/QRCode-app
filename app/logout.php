<?php
include 'config/common.php';
// Po odhlásení používateľa zničí session daného užívateľa a redirektne na login.php
session_destroy();
header("Location: ../index.php");
exit();
?>