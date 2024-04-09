<?php

include 'database.php';
header('Content-Type: application/json');

// Ak nie je inicializovaná session, inicializujeme ju
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kontrola, či boli údaje odoslané
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ibanName = $_POST["iban-name"];
    $id = $_POST["iban-id"];
    //$ibanId = $_POST["iban-id"];

    // Pripravíme SQL príkaz
    $sql = "UPDATE iban SET iban_name = ? WHERE id = ?";

    // Vytvoríme prepared statement
    if ($stmt = $conn->prepare($sql)) {
        // Pripojíme parametre
        $stmt->bind_param("si", $ibanName, $id);

        // Vykonáme príkaz
        if ($stmt->execute()) {
            // Zmeny boli úspešne uložené
            $response = array(
                "success" => true,
                "ibanName" => $ibanName,
                "id" => $id,
                //"ibanId" => $ibanId,
                "userId" => $_SESSION["user_id"]
            );
            echo json_encode($response);
        } else {
            // Došlo k chybe pri ukladaní zmien
            echo json_encode(array("success" => false, "error" => $stmt->error));
        }

        // Zatvoríme statement
        $stmt->close();
    }
}

$conn->close();

?>