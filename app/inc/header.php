<?php include 'config/common.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <title>Zdieľanie platby</title>
</head>
<body>
  <nav class="navbar navbar-expand-sm navbar-light bg-light mb-4">
    <div class="container">
      <a class="navbar-brand" href="#">QR Code</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="/qrcode-app/app/index.php">IBAN</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/qrcode-app/app/payment_sharing.php">Zdieľanie platby</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/qrcode-app/app/about.php">O apke</a>
          </li>
          <?php 
          if (isset($_SESSION['user_id'])){
            // Používateľ je prihlásený, nezobrazujeme prihlasovacie element
            echo ('<li class="nav-item" style="background-color: #cccccc; border-radius: 5px;">
            <a class="nav-link" href="/qrcode-app/app/login.php" style="font-weight: bold; color: #000;">logged in as '. $_SESSION['user_id'] .'</a>
          </li>');
          }else{
            echo ('<li class="nav-item" style="background-color: #cccccc; border-radius: 5px;">
            <a class="nav-link" href="/qrcode-app/app/login.php" style="font-weight: bold; color: #000;">QR Code Login</a>
          </li>');
          }
          ?>
        </ul>
      </div>
  </div>
</nav>

<main>
  <div class="container d-flex flex-column align-items-center">