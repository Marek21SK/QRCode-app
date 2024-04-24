<?php
require __DIR__ . '/../vendor/autoload.php';

/* Starý spôsob konfigurácie databázy
//Načítanie údajov z JSON súboru
$configData = file_get_contents(__DIR__ . '/data.json');
$config = json_decode($configData, true);

//Priradenie hodnôt z konfiguračného súboru
$servername = "db"; // Názov kontajneru MySQL serveru v Dockeri
$username = $config["DB_USERNAME"];
$password = $config["DB_PASSWORD"];
$dbname = $config["DB_NAME"];
*/

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Priradenie hodnôt z .env súboru
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

// Vytvorenie pripojenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Skontrolovať pripojenie
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset("utf8");

//echo 'Connected successfully';