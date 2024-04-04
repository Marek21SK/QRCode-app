<?php include 'config/common.php';
      include 'config/database.php';?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link href="https://cdn.datatables.net/v/dt/dt-2.0.3/datatables.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.datatables.net/v/dt/dt-2.0.3/datatables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="styles/style.css"> 
  <title>Zdieľanie platby</title>
</head>
<body>
  <nav class="navbar navbar-expand-sm navbar-light bg-light mb-4">
    <div class="container">
      <a class="navbar-brand" href="#" style="cursor:default" rel="nofollow">
        <img src="https://chart.googleapis.com/chart?cht=qr&chl=Test&chs=180x180&choe=UTF-8&chld=L|2" alt="Logo" height="30" class="d-inline-block align-top">
        QR Code</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">IBAN</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="/qrcode-app/app/index.php">Prehľad IBAN účtov</a>
              <?php if (isset($_SESSION['user_id']))
                echo '<a class="dropdown-item" href="/qrcode-app/app/ibans.php">Pridať IBAN</a>'
              ?>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Platby</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="/qrcode-app/app/payment.php">Zdieľanie platby</a>
              <?php
                if (isset($_SESSION['user_id'])){
                  $user_id = $_SESSION['user_id'];
                  $sql = "SELECT 1 FROM payment WHERE payment_id = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $user_id);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows > 0){
                    // Používateľ je prihlásený a má uložené platby, zobrazíme dropdown item
                    echo '<a class="dropdown-item" href="/qrcode-app/app/saved_payments.php">Uložené platby</a>';
                  }
                  $stmt->close();
                }
              ?>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/qrcode-app/app/about.php">O apke</a>
          </li>
          <?php 
              if (isset($_SESSION['user_id'])){
                // Používateľ je prihlásený, zobrazíme informácie o prihlásení
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT nickname FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0){
                    $row = $result->fetch_assoc();
                    echo '<li class="nav-item" style="background-color: #cccccc; border-radius: 5px;">
                            <a class="nav-link" href="#" style="font-weight: bold; color: #000;">Vitajte, ' . $row['nickname'] . '</a>
                        </li>';
                }
                $stmt->close();
              } else {
                // Používateľ nie je prihlásený, zobrazíme odkaz na prihlásenie alebo registráciu
                echo '<li class="nav-item" style="background-color: #cccccc; border-radius: 5px;">
                        <a class="nav-link" href="/qrcode-app/app/login.php" style="font-weight: bold; color: #000;">Registrácia | Prihlásenie</a>
                    </li>';}
          ?>
        </ul>
      </div>
  </div>
</nav>

<main>
  <div class="container d-flex flex-column align-items-center">