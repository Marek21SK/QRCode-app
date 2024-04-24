<?php include 'database.php';

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrola, či už je používateľ prihlásený
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error4'] = "Nie ste prihlásený/á. Prihláste sa a skúste to znovu.";
    header("Location: ../index.php");
    exit();
}

// Funkcia na kontrolu platnosti IBAN-u pre Slovensko
function isIBANValid($iban) {
    $iban = strtolower(str_replace(' ','',$iban));

    $MovedChar = substr($iban, 4).substr($iban,0,4);
    $MovedCharArray = str_split($MovedChar);
    $NewString = "";

    foreach($MovedCharArray AS $key => $value){
        if(!is_numeric($MovedCharArray[$key])){
            $MovedCharArray[$key] = ord($MovedCharArray[$key]) - 87;
        }
        $NewString .= $MovedCharArray[$key];
    }

    if(bcmod($NewString, '97') != 1)
    {
        return false;
    }

    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Spracovanie nového IBAN-u
    $iban = $_POST['iban'];
    $iban_name = $_POST['iban_name'];
    $user_id = $_SESSION['user_id'];

    // IBAN sa do databázy uloží bez medzier
    $iban = str_replace(' ', '', $iban);

    // Kontrola, IBAN začíná prefixom "SK"
    if (substr($iban, 0, 2) !== 'SK') {
        $_SESSION['error5'] = "IBAN musí začínať prefixom 'SK'.";
        header("Location: ../ibans.php");
        exit();
    }

    // Za prefixom "SK" len číslice
    if (!preg_match('/^SK\d+$/', substr($iban, 0, 4))) {
        $_SESSION['error6'] = "IBAN po prefixe 'SK' môže obsahovať len číslice.";
        header("Location: ../ibans.php");
        exit();
    }

    // Kontrola, či IBAN má správny formát (prefix "SK" a 22 čísiel)
    if (!preg_match('/^SK\d{22}$/', $iban)) {
        $_SESSION['error7'] = "IBAN musí mať správny formát.";
        header("Location: ../ibans.php");
        exit();
    }

    // Kontrola, či je IBAN platný (Presun prvých 4 znakov na koniec a kontrola či je číslo deliteľné 97)
    if (!isIBANValid($iban)) {
        $_SESSION['error9'] = "IBAN nie je platný.";
        header("Location: ../ibans.php");
        exit();
    }

    // Kontrola či už IBAN neexistuje v databáze
    $checkIBAN = "SELECT id FROM iban WHERE iban = ? AND user_id = ?";
    $checkIBANsmt = $conn-> prepare($checkIBAN);
    $checkIBANsmt-> bind_param("si", $iban, $user_id);
    $checkIBANsmt-> execute();
    $checkIBANresult = $checkIBANsmt->get_result();

    if ($checkIBANresult-> num_rows > 0){
        $_SESSION['error8'] = "Nemôžete si pridať už raz pridaný IBAN, použite iný.";
        header("Location: ../ibans.php");
        exit();
    }

    // Vloženie nového IBAN do databázy
    $sql = "INSERT INTO iban (user_id, iban, iban_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $iban, $iban_name);

    if ($stmt->execute()) {
        $_SESSION['success3'] = "IBAN bol úspešne pridaný.";
    } else {
        //$_SESSION['error7'] = "Chyba pri pridávaní IBAN-u: ";
        header("Location: ../ibans.php");
    }

    $stmt->close();
}

// Presmerovanie na hlavnú stránku
header("Location: ../ibans.php");
exit();
?>
