<?php

include 'database.php';

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Funkcia na získanie platby pre aktuálneho používateľa
function getPayment($conn, $id){
    // Príprava SQL dotazu pre získanie platby z databázy
    $sql = "SELECT * FROM payment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()){
        return $row;
    }
    $stmt->close();

    return null;
}

// Funkcia na vymazanie platby
function deletePayment($conn, $id){
    // Príprava SQL dotazu pre vymazanie platby z databázy
    $sql = "DELETE FROM payment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}

if(isset($_POST['payment_id'])){
    // Získanie ID platby z URL
    $id = $_POST['payment_id'];

    // Získanie platby
    $payment = getPayment($conn, $id);

    // Vymazanie platby
    if (deletePayment($conn, $id)){
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nepodarilo sa odstrániť záznam.']);
    }
}
?>