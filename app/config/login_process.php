<?php

include 'database.php';
session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

// Skontrolovať pripojenie
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }
  
  $conn->set_charset("utf8");

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

    $sql = "SELECT id, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result-> num_rows > 0){
        $row = $result->fetch_assoc();
        $passwordHash = $row['password'];

        if (password_verify($password, $passwordHash)){
            $_SESSION['user_id'] = $row['id'];
            header("Location: /qrcode-app/app/index.php");
            exit();
        }else {
            $_SESSION['error2'] = "Zadali ste nesprávne meno alebo heslo";
            header("Location: /qrcode-app/app/login.php");
        }
    } else{
        $_SESSION['error'] = "Zadali ste nesprávne meno alebo heslo";
        header("Location: /qrcode-app/app/login.php");
    }
}

$conn->close();

?>
