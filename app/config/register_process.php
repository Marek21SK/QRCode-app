<?php

include 'database.php';

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Spracovanie registrácie
    $email = $_POST['registerEmail'];
    $password = $_POST['registerPassword'];
    $nickname = $_POST['registerNickname'];

    // Kontrola, či sa už email nenachádza v databáze
    $checkEmail = "SELECT id FROM users WHERE email = ?";
    $checkEmailStm = $conn->prepare($checkEmail);
    $checkEmailStm -> bind_param("s", $email);
    $checkEmailStm-> execute();
    $checkEmailResult = $checkEmailStm-> get_result();

    if ($checkEmailResult->num_rows > 0){
        // Email už existuje, zobraz chybovú hlášku
        $_SESSION['error3'] = "Tento email sa už používa, zadajte iný email";
        header("Location: ../login.php");
        exit();
    }else{
        // Heslo zahashovať
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        // Vložiť nového používateľa do databázy
        $sql = "INSERT INTO users (email, password, nickname) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $hashPassword, $nickname);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registrácia prebehla úspešne!";
            header("Location: ../login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $checkEmailStm->close();
}
?>