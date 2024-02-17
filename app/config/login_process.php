<?php

include 'database.php';

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    // Kontrola, či už je používateľ prihlásený
    if (isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Už ste prihlásený/á | Najprv sa odhláste pre nové prihlásenie.";
        header("Location: /qrcode-app/app/login.php");
        exit();
    }

    // Spracovanie prihlásenia používateľa
    $email = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];

    // Príprava SQL dotazu pre parameter s (reťazec)
    $sql = "SELECT id, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    // Vykonanie a získanie výsledkov
    $stmt->execute();
    $result = $stmt->get_result();

    // Kontrola či už sa používateľ nachádza v databáze 
    if ($result-> num_rows > 0){
        $row = $result->fetch_assoc();
        $passwordHash = $row['password'];

        if (password_verify($password, $passwordHash)){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['success1'] = "Úspešne ste sa prihlásili";
            header("Location: /qrcode-app/app/index.php");
            exit();
        } else {
            $_SESSION['error'] = "Zadali ste nesprávný email alebo heslo";
            header("Location: /qrcode-app/app/login.php");
        }
    } else {
        $_SESSION['error2'] = "Užívateľ s týmto emailom neexistuje";
        header("Location: /qrcode-app/app/login.php");
    }
}
?>
