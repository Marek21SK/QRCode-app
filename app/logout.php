<?php
session_start();
unset($_SESSION['user_id']);
session_destroy();
header("Location: /qrcode-app/app/login.php");
exit();
?>