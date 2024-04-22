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
        header("Location: ../login.php");
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
            //$_SESSION['success1'] = "Úspešne ste sa prihlásili";

            // Príprava SQL dotazu pre kontrolu, či už má používateľ uložené platby
            $sql = "SELECT 1 FROM payment WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $row['id']);

            // Vykonanie a získanie výsledkov
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0){
                header("Location: /qrcode-app/app/saved_payments.php");
            } else {

                // Príprava SQL dotazu pre kontrolu, či už má používateľ uložený IBAN
                $sql = "SELECT 1 FROM iban WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $row['id']);

                // Vykonanie a získanie výsledkov
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0){
                    header("Location: /qrcode-app/app/payment.php");
                } else {
                    header("Location: /qrcode-app/app/ibans.php");
                }
            }
            exit();
        } else {
            $_SESSION['error'] = "Zadali ste nesprávný email alebo heslo";
            header("Location: ../login.php");
        }
    } else {
        $_SESSION['error2'] = "Užívateľ s týmto emailom neexistuje";
        header("Location: ../login.php");
    }
}
?>
