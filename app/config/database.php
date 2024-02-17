<?php

//Načítanie údajov z JSON súboru
$configData = file_get_contents(__DIR__ . '/data.json');
$config = json_decode($configData, true);

//Priradenie hodnôt z konfiguračného súboru
$servername = $config["DB_HOST"];
$username = $config["DB_USERNAME"];
$password = $config["DB_PASSWORD"];
$dbname = $config["DB_NAME"];

// Vytvorenie pripojenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Skontrolovať pripojenie
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset("utf8");

//echo 'Connected successfully';