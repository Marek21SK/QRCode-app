<?php include 'inc/header.php';
      include 'config/database.php';

// Kontrola, či je používateľ prihlásený
if (isset($_SESSION['user_id'])) {
  // Načítanie informácií o používateľovi
  $user_id = $_SESSION['user_id'];
  $sql = "SELECT nickname FROM users WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $nickname = $row['nickname'];
      echo '<div class="container mt-5 d-flex flex-column align-items-center justify-content-center">';
      echo "<h2 class='mb-4'>Vitajte, používateľ: <strong>$nickname</strong></h2>";
      echo '<div class="d-grid gap-2">
            <form action="/qrcode-app/app/logout.php" method="POST">
                <button type="submit" class="btn btn-dark">Odhlásit</button>
            </form><br>
            </div>
            </div>';
      // Získanie IBAN-ov aktuálne prihláseného používateľa
      $sql = "SELECT iban FROM iban WHERE iban_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        echo '<div style="width: 50%; margin: auto;">
              <table class="table table-bordered">
              <thead>
              <tr><th scope="col" style="width: 40%;">IBAN</th><th scope="col" style="width: 60%;">Použitie</th></tr>
              </thead>
              <tbody>';
        while($row = $result->fetch_assoc()) {
            $formattedIBAN = formatIBAN($row["iban"]);
            echo "<tr><td id='iban'>" . $formattedIBAN. "</td><td>" . "Tu musím dostať na čo slúži daný IBAN" . "</td></tr>";
        }
        echo '</tbody></table></div>';
    } else {
        echo "Žiadne IBAN-y pre zobrazenie.";
    }

  } else {
      echo "Chyba pri získavaní informácií o užívateľovi.";
  }

  $stmt->close();
} else {
  echo '<div class="container mt-5 d-flex flex-column align-items-center justify-content-center">
        <h4><strong>Vitajte v aplikácií QRCode.</strong></h4></div>';
}

  if (isset($_SESSION['success1'])) { 
    echo '<br><div class="alert alert-success" role="alert">' . $_SESSION['success1'] . '</div>'; unset($_SESSION['success1']);}?>

<?php
// Funkcia na zobrazenie IBAN s medzerami po 4 znakoch
function formatIBAN($iban) {
  $formattedIBAN = chunk_split($iban, 4, ' ');
  // Odstránenie medzery na konci
  return rtrim($formattedIBAN);}?>

<!-- Skript na schovanie alertu po určitom čase -->
<script>
  setTimeout(function(){
    document.querySelector('.alert').style.display = 'none';
  }, 4000);
</script>

<!-- Formulár na pridanie IBAN -->
<br>
  <form action="config/add_iban_process.php" method="POST">
    <div class="center-container" style="display: flex; ">
      <div class="row">
        <div class="mb-3" style="text-align: center;">
          <label style="text-align: center;" for="iban" class="form-label">Zadajte IBAN:</label>
          <input style="padding-right: 25px; margin: 10px auto;" type="text" class="form-control" id="iban" name="iban" placeholder="SK88 8888 8888 8888 8888 8888" maxlength="29" required>
          <label style="text-align: center;" for="iban_name" class="form-label">Zadajte názov daného IBAN-u:</label>
          <textarea style="padding-right: 25px; margin: 10px auto;" type="text" class="form-control" id="iban_name" name="iban_name" placeholder="Tu si môžete vložiť na čo slúži daný IBAN" rows="3" maxlength="255" required></textarea>
          <!-- Skript, ktorý pri pridávaní IBAN-u sa zobrazí tak, že každé 4 znaky dá medzeru pre lepšie skontrolovanie -->
          <script>
            document.getElementById('iban').addEventListener('input', function(event){
              var ibanSpaces = event.target.value.replace(/\s/g, '');
              var ibanFormat = ibanSpaces.replace(/(.{4})/g, '$1 ').trim();
              event.target.value = ibanFormat;
            });
          </script>
          <div class="d-grid">
            <?php if (isset($_SESSION['error4'])) { 
              echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error4'] . '</div>'; unset($_SESSION['error4']);}
              if (isset($_SESSION['error5'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error5'] . '</div>'; unset($_SESSION['error5']);}
              if (isset($_SESSION['success3'])) { 
                echo '<div class="alert alert-info" role="alert">' . $_SESSION['success3'] . '</div>'; unset($_SESSION['success3']);}
              if (isset($_SESSION['error6'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error6'] . '</div>'; unset($_SESSION['error6']);}
              if (isset($_SESSION['error7'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error7'] . '</div>'; unset($_SESSION['error7']);}
              if (isset($_SESSION['error8'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error8'] . '</div>'; unset($_SESSION['error8']);}
              if (isset($_SESSION['error9'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error9'] . '</div>'; unset($_SESSION['error9']);}?>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Pridať IBAN</button>
      </div>
    </div>
  </form>

<?php include 'inc/footer.php'; ?>
