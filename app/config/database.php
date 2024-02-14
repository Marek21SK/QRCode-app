<?php
$servername = "localhost";
$username = "Admin";
$password = "0022558899mM@";
$dbname = "qrcodoe_dev";

// Vytvorenie pripojenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Skontrolovať pripojenie
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset("utf8");

//echo 'Connected successfully';