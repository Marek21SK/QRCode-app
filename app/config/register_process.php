<?php

include 'database.php';

// Vytvorenie pripojenia
$conn = new mysqli($servername, $username, $password, $dbname);

// Skontrolovať pripojenie
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Spracovanie registrácie
    $email = $_POST['registerEmail'];
    $password = $_POST['registerPassword'];
    $nickname = $_POST['registerNickname'];

    // Heslo zahashovať
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);

    // Vložiť nového používateľa do databázy
    $sql = "INSERT INTO users (email, password, nickname) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $hashPassword, $nickname);

    if ($stmt->execute()) {
        header("Location: /qrcode-app/app/login.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

?>