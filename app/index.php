<?php include 'inc/header.php'; ?>
<?php include 'config/database.php'; ?>
<style><?php include 'styles/style.css';?></style>

<!-- Kontrola, či je používateľ prihlásený -->
<?php if (isset($_SESSION['user_id'])) : ?>
  <!-- Načítanie informácií o používateľovi -->
  <?php
  $user_id = $_SESSION['user_id'];
  $sql = "SELECT nickname FROM users WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nickname = $row['nickname'];
    ?>
    <div class="index-custom container d-flex flex-column align-items-center justify-content-center">
    <div class="container mt-4 d-flex flex-column align-items-center justify-content-center">
      <div style="display: contents;">
        <h5 style="font-weight: bold;">Vitajte, používateľ: <strong><?php echo $nickname; ?></strong></h5>
        <form action="/qrcode-app/app/logout.php" method="POST">
          <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center" style="height: 25px;">
            <span style="line-height: 10px;">Odhlásiť</span>
          </button>
        </form><br>
      </div>
    </div>

    <div class="container" style="margin-bottom: 10px;">
    <button class="toggleButton btn btn-primary mx-auto d-flex justify-content-center align-items-center" style="height: 25px; font-size: 0.6rem; text-align: left;">Otvoriť prehľad IBAN-ov</button>
      <div class="row" style="display: none;"><br>
        <div class="col-md-12">
          <!-- Zobrazenie prehľadu IBAN-ov -->
          <?php
          // Získanie IBAN-ov aktuálne prihláseného používateľa
          $sql = "SELECT iban, iban_name FROM iban WHERE iban_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
          ?>
            <div style="width: 100%; max-width: 800px; margin: auto;">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th colspan="2" class="text-center">Prehľad IBAN-ov</th>
                    </tr>
                    <tr>
                      <th scope="col" style="width: 40%;">IBAN</th>
                      <th scope="col" style="width: 60%;">Názov</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                      $formattedIBAN = formatIBAN($row["iban"]);
                      $iban_name = $row["iban_name"];
                      echo "<tr><td class='iban' style='white-space: nowrap;'>" . $formattedIBAN . "</td><td style='white-space: nowrap;'>" . $iban_name . "</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php
          } else {
            echo "<div class='mx-auto d-flex justify-content-center align-items-center'>Žiadne IBAN pre zobrazenie.</div>";
          }
          ?>
        </div>
      </div>

      <!-- Formulár na pridanie IBAN -->
      <div class="row mt-3">
        <div class="col-md-6 mx-auto d-flex justify-content-center align-items-center"><br>
          <div class="card" style="padding: 10px; margin-bottom: 10px; border-radius: 15px; border-width: medium; border-color: lightgrey;">
            <div class="card-body">
              <form action="config/add_iban_process.php" method="POST">
                <div class="center-container" style="display: flex; justify-content: center;">
                  <div class="row">
                    <div class="mb-3">
                      <label for="add_iban" class="form-label">Zadajte IBAN:</label>
                      <input style="padding-right: 25px; margin: 10px auto;" type="text" class="form-control" id="add_iban" name="iban" placeholder="SK88 8888 8888 8888 8888 8888" maxlength="29" required>
                      <label for="iban_name" class="form-label">Popis IBAN-u:</label>
                      <textarea style="padding-right: 25px; margin: 10px auto;" type="text" class="form-control" id="iban_name" name="iban_name" placeholder="Prosím, uveďte účel použitia tohto IBAN" rows="3" maxlength="255" required></textarea>
                      <!-- Skript, ktorý pri pridávaní IBAN-u sa zobrazí tak, že každé 4 znaky dá medzeru pre lepšie skontrolovanie -->
                      <script>
                        document.getElementById('add_iban').addEventListener('input', function (event) {
                          var ibanSpaces = event.target.value.replace(/\s/g, '');
                          var ibanFormat = ibanSpaces.replace(/(.{4})/g, '$1 ').trim();
                          event.target.value = ibanFormat;
                        });
                      </script>
                      <div class="d-grid">
                        <?php if (isset($_SESSION['error4'])) {
                          echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error4'] . '</div>';
                          unset($_SESSION['error4']);
                        }
                        if (isset($_SESSION['error5'])) {
                          echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error5'] . '</div>';
                          unset($_SESSION['error5']);
                        }
                        if (isset($_SESSION['success3'])) {
                          echo '<div class="alert alert-info" role="alert">' . $_SESSION['success3'] . '</div>';
                          unset($_SESSION['success3']);
                        }
                        if (isset($_SESSION['error6'])) {
                          echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error6'] . '</div>';
                          unset($_SESSION['error6']);
                        }
                        if (isset($_SESSION['error7'])) {
                          echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error7'] . '</div>';
                          unset($_SESSION['error7']);
                        }
                        if (isset($_SESSION['error8'])) {
                          echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error8'] . '</div>';
                          unset($_SESSION['error8']);
                        }
                        if (isset($_SESSION['error9'])) {
                          echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error9'] . '</div>';
                          unset($_SESSION['error9']);
                        } ?>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Pridať IBAN</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  <?php
  } else {
    echo "Chyba pri získavaní informácií o užívateľovi.";
  }
  $stmt->close();
  ?>
<?php else : ?>
  <div class="container mt-5 d-flex flex-column align-items-center justify-content-center">
    <h4><strong>Vitajte v aplikácií QRCode.</strong></h4>
    <?php if (isset($_SESSION['success1'])) {
      echo '<br><div class="alert alert-success" role="alert">' . $_SESSION['success1'] . '</div>';
      unset($_SESSION['success1']);
    } ?>
  </div>
<?php endif; ?>

<?php
// Funkcia na zobrazenie IBAN s medzerami po 4 znakoch
function formatIBAN($iban)
{
  $formattedIBAN = chunk_split($iban, 4, ' ');
  // Odstránenie medzery na konci
  return rtrim($formattedIBAN);
}
?>

<!-- Skript na zobrazenie/skrytie IBAN-ov -->
<script>
  document.querySelectorAll(".toggleButton").forEach(function(button) {
    button.addEventListener("click", function() {
      var content = this.nextElementSibling;
      if (content.style.display === "none") {
        content.style.display = "block";
        this.textContent = "Zatvoriť prehľad IBAN-ov";
      } else {
        content.style.display = "none";
        this.textContent = "Otvoriť prehľad IBAN-ov";
      }
    });
  });
</script>

<!-- Skript na schovanie alertu po určitom čase -->
<script>
  setTimeout(function () {
    document.querySelector('.alert').style.display = 'none';
  }, 4000);
</script>
<?php include 'inc/footer.php'; ?>
