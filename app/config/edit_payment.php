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
// Funkcia na aktualizáciu platby
function updatePayment($conn, $payment_id, $newData){
    // Príprava SQL dotazu pre aktualizáciu platby v databáze
    $sql = "UPDATE payment SET sum = ?, vs = ?, ks = ?, ss = ?, name = ?, adress = ?, adress2 = ?, date_iban = ?, info_name = ?, payment_name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dsssssssssi", $newData['sum'], $newData['vs'], $newData['ks'], $newData['ss'], $newData['name'], $newData['adress'], $newData['adress2'], $newData['date_iban'], $newData['info_name'], $newData['payment_name'], $payment_id);

    return $stmt->execute();
}

// Získanie ID platby z URL
$payment_id = $_POST['payment_id'];

// Získanie platby
$payment = getPayment($conn, $payment_id);

//Odoslaný formulár, aktualizujeme uloženú platbu
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $newData = $_POST;
    if (updatePayment($conn, $payment_id, $newData)){
        header("Location: ../saved_payments.php");
        exit();
    } else {
        header("Location: ../saved_payments.php");
        exit();
    }
}
?>