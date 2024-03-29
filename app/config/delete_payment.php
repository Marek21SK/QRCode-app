<?php

include 'database.php';

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Funkcia na získanie platby pre aktuálneho používateľa
function getPayment($conn, $payment_id){
    // Príprava SQL dotazu pre získanie platby z databázy
    $sql = "SELECT * FROM payment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()){
        return $row;
    }
    $stmt->close();

    return null;
}

// Funkcia na vymazanie platby
function deletePayment($conn, $payment_id){
    // Príprava SQL dotazu pre vymazanie platby z databázy
    $sql = "DELETE FROM payment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);

    return $stmt->execute();
}

if(isset($_GET['payment_id'])){
    // Získanie ID platby z URL
    $payment_id = $_GET['payment_id'];

    // Získanie platby
    $payment = getPayment($conn, $payment_id);

    // Vymazanie platby
    if (deletePayment($conn, $payment_id)){
        header("Location: /qrcode-app/app/saved_payments.php");
        exit();
    } else {
        header("Location: /qrcode-app/app/saved_payments.php");
        exit();
    }
} else {
    header("Location: /qrcode-app/app/saved_payments.php");
    exit();
}
?>