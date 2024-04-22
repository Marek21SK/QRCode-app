<?php

include 'database.php';

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Funkcia na získanie IBAN pre aktuálneho používateľa
function getIban($conn, $id)
{
    // Príprava SQL dotazu pre získanie IBAN z databázy
    $sql = "SELECT * FROM iban WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    $stmt->close();

    return null;
}

// Funkcia na kontrolu, či IBAN má uložené platby
function hasPayments($conn, $id)
{
    // Príprava SQL dotazu pre získanie platieb pre daný IBAN
    $sql = "SELECT * FROM payment WHERE iban_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ak existujú platby pre daný IBAN, vrátime true
    if ($result->num_rows > 0) {
        return true;
    }

    return false;
}

// Funkcia na vymazanie IBAN
function deleteIban($conn, $id)
{
    // Príprava SQL dotazu pre vymazanie IBAN z databázy
    $sql = "DELETE FROM iban WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    return $stmt->execute();
}

if (isset($_POST['data-id-iban'])) {
    // Získanie ID IBAN z URL
    $id = $_POST['data-id-iban'];

    // Kontrola, či IBAN má uložené platby
    if (hasPayments($conn, $id)) {
        echo json_encode(['success' => false, 'error' => 'Nemožno odstrániť IBAN v uložených platbách. <br> Pred jeho odstránením prosím odstráňte jeho uložené platby.']);
        exit();
    }

    // Získanie IBAN
    $iban = getIban($conn, $id);

    // Vymazanie IBAN
    if (deleteIban($conn, $id)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nepodarilo sa odstrániť záznam.']);
    }
}
?>