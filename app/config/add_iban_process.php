<?php

include 'database.php';

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrola, či už je používateľ prihlásený
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error4'] = "Nie ste prihlásený/á. Prihláste sa a skúste to znovu.";
    header("Location: /qrcode-app/app/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Spracovanie nového IBAN-u
    $user_id = $_SESSION['user_id'];
    $iban = $_POST['iban'];

    // Kontrola, IBAN začíná prefixom "SK"
    if (substr($iban, 0, 2) !== 'SK') {
        $_SESSION['error5'] = "IBAN musí začínať prefixom 'SK'.";
        header("Location: /qrcode-app/app/index.php");
        exit();
    }

    // Za prefixom "SK" len číslice
    if (!preg_match('/^SK\d+$/', substr($iban, 0, 4))) {
        $_SESSION['error6'] = "IBAN po prefixe 'SK' môže obsahovať len číslice.";
        header("Location: /qrcode-app/app/index.php");
        exit();
    }

    // Kontrola, či IBAN má správny formát (prefix "SK" a 22 čísiel)
    if (!preg_match('/^SK\d{22}$/', $iban)) {
        $_SESSION['error7'] = "IBAN musí mať správny formát.";
        header("Location: /qrcode-app/app/index.php");
        exit();
    }

    // Kontrola či už IBAN neexistuje v databáze
    $checkIBAN = "SELECT id FROM iban WHERE iban = ?";
    $checkIBANsmt = $conn-> prepare($checkIBAN);
    $checkIBANsmt-> bind_param("s", $iban);
    $checkIBANsmt-> execute();
    $checkIBANresult = $checkIBANsmt->get_result();

    if ($checkIBANresult-> num_rows > 0){
        $_SESSION['error8'] = "Nemôžete si pridať už raz pridaný IBAN, použite iný.";
        header("Location: /qrcode-app/app/index.php");
        exit();
    }

    // Vloženie nového IBAN do databázy
    $sql = "INSERT INTO iban (iban_id, iban) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $iban);

    if ($stmt->execute()) {
        $_SESSION['success3'] = "IBAN bol úspešne pridaný.";
    } else {
        //$_SESSION['error7'] = "Chyba pri pridávaní IBAN-u: ";
        header("Location: /qrcode-app/app/index.php");
    }

    $stmt->close();
}

// Presmerovanie na hlavnú stránku
header("Location: /qrcode-app/app/index.php");
exit();
?>
