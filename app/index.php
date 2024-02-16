<?php include 'inc/header.php'; ?>
<?php include 'config/database.php';?>

<?php
session_start();

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
      echo "<h2>Vitajte, používateľ: $nickname</h2>";
      // echo "<a href='/qrcode-app/app/logout.php'>Odhlasiť sa</a>";
      echo '<div class="d-grid">
            <form action="/qrcode-app/app/logout.php" method="POST">
                <button type="submit" class="btn btn-dark">Odhlásit</button>
            </form>
            </div>';
  } else {
      echo "Chyba pri získavaní informácií o užívateľovi.";
  }

  $stmt->close();
} else {
  echo "<p>Vitajte v apke QRCode.</p>";
}

  if (isset($_SESSION['success1'])) { 
    echo '<br><div class="alert alert-success" role="alert">' . $_SESSION['success1'] . '</div>'; unset($_SESSION['success1']);}?>

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
          <input style="padding-right: 25px; margin: 10px auto;" type="text" class="form-control" id="iban" name="iban" placeholder="SK88 8888 8888 8888 8888 8888" maxlength="24" required>
          <div class="d-grid">
            <?php if (isset($_SESSION['error4'])) { 
              echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error4'] . '</div>'; unset($_SESSION['error4']);}
              if (isset($_SESSION['error5'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error5'] . '</div>'; unset($_SESSION['error5']);}
              if (isset($_SESSION['success3'])) { 
                echo '<div class="alert alert-sucess" role="alert">' . $_SESSION['success3'] . '</div>'; unset($_SESSION['success3']);}
              if (isset($_SESSION['error6'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error6'] . '</div>'; unset($_SESSION['error6']);}
              if (isset($_SESSION['error7'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error7'] . '</div>'; unset($_SESSION['error7']);}
              if (isset($_SESSION['error8'])) { 
                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error8'] . '</div>'; unset($_SESSION['error8']);}?>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Pridať IBAN</button>
      </div>
    </div>
  </form>

<?php include 'inc/footer.php'; ?>
