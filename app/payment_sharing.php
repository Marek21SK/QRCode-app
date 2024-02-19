<?php 
    include 'inc/header.php';
    include 'config/database.php';

    $iban = $sum = "";
    $ibanErr = $sumErr = "";

    // Form submit
    if (isset($_POST['submit'])){
        // Validate IBAN
        $selectIBAN = isset($_POST['iban']) ? trim($_POST['iban']): '';
        if (empty($selectIBAN)){
            $ibanErr = 'IBAN je potrebný';
        }else{
            $iban = filter_input(INPUT_POST, 'iban', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
    
        // Validate sum
        if (empty($_POST['sum'])){
            $sumErr = 'Suma je potrebná';
        }else{
            $sum = filter_input(INPUT_POST, 'sum', FILTER_SANITIZE_NUMBER_INT);
        }
    
        if (empty($ibanErr) && empty($sumErr)){
            // Add to database
            $sql = "INSERT INTO qrcode (email, iban, sum, vs, ss, ks, moneytype, name, info_name, adress, date_iban) VALUES ('$email', '$iban', '$sum', '$vs', '$ss', '$ks', '$moneytype', '$name', '$info_name', '$adress', '$date_iban')";
            if (mysqli_query($conn, $sql)){
                // Succes
                header('Location: index.php');
            }else {
                // Error
                echo 'Error: ' . mysqli_error($conn);
            }
        }
    }
    // Zatiaľ iba takto pre overenie.
    // Kontrola, či je používateľ prihlásený
    if (!isset($_SESSION['user_id'])) {
      echo "<p>Pre zobrazenie vašich IBAN-ov sa prosím prihláste.</p>";
    } else {
      // Získanie IBAN-ov pre prihláseného užívateľa
      $user_id = $_SESSION['user_id'];
      $getIBAN = "SELECT iban FROM iban WHERE iban_id = ?";
      $getIBANstmt = $conn->prepare($getIBAN);
      $getIBANstmt->bind_param("i", $user_id);
      $getIBANstmt->execute();
      $result = $getIBANstmt->get_result();

      if ($result->num_rows > 0) {
          echo "<h3>Pridané IBAN-y pre prihláseného užívateľa:</h3>";
          echo "<ul>";
          while ($row = $result->fetch_assoc()) {
              echo "<li>" . htmlspecialchars($row['iban']) . "</li>";
          }
          echo "</ul>";
      } else {
          echo "<p>Žiadne pridané IBAN-y pre prihláseného užívateľa.</p>";
      }

      $getIBANstmt->close();
    }

    // Funkcia na získanie IBAN-ov pre aktuálneho používateľa
    function getSavedIBANs($conn, $user_id){
      $ibanList = array();

      // Príprava SQL dotazu pre získanie IBAN-u z databázy
      $sql = "SELECT iban FROM iban WHERE iban_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      // Načítanie IBAN-ov do poľa
      while ($row = $result->fetch_assoc()){
        $ibanList[] = $row['iban'];
      }
      $stmt->close();

      return $ibanList;
    }

    // Použitie funkcie
    $loggedUser = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($loggedUser){
      $savedIBANs = getSavedIBANs($conn, $loggedUser);
    }else {
      $savedIBANs = array();
    }

    // Funkcia na zobrazenie IBAN s medzerami po 4 znakoch (musím oddtestovať ako sa ukladajú do db)
    function formatIBAN($iban) {
      $formattedIBAN = chunk_split($iban, 4, ' ');
      // Odstránenie medzery na konci
      return rtrim($formattedIBAN);
    }
?>

<!-- Form for all inputs (Túto časť kódu mám ešte rozpracovanú.-->
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="mt-4 w-75">
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3">
        <label for="iban" class="form-label">IBAN</label>
        <select class="form-control" <?php echo !$ibanErr ?: 'is-invalid';?>" id="iban" name="iban">
          <option value="" selected disabled>Vyberte IBAN</option>
          <?php foreach ($savedIBANs as $ibanOption): ?>
            <option value="<?php echo $ibanOption; ?>"><?php echo formatIBAN($ibanOption); ?></option>
        <?php endforeach; ?>
        </select>
          <!--<input type="text" class="form-control <?php //echo !$ibanErr ?: 'is-invalid';?>" id="iban" name="iban" placeholder="SK88 8888 8888 8888 8888 8888">-->
          <div class="invalid-feedback">
            <?php echo $ibanErr; ?>
          </div>
      </div>

      <div class="mb-3">
        <label for="moneytype" class="form-label">Mena prevodu</label>
        <input type="text" class="form-control" id="moneytype" name="moneytype" maxlength="3" placeholder="EUR">
      </div>

      <div class="mb-3">
        <label for="ks" class="form-label">Konštantný symbol</label>
        <input type="text" class="form-control" id="ks" name="ks" placeholder="1234">
      </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
        <label for="sum" class="form-label">Suma</label>
        <input type="number" class="form-control <?php echo !$sumErr ?: 'is-invalid';?>" id="sum" name="sum" placeholder="10.00">
        <div class="invalid-feedback">
          <?php echo $sumErr; ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="vs" class="form-label">Variabilný symbol</label>
        <input type="text" class="form-control" id="vs" name="vs" placeholder="9876543210">
      </div>

      <div class="mb-3">
        <label for="ss" class="form-label">Špecifický symbol</label>
        <input type="text" class="form-control" id="ss" name="ss" placeholder="1234567890">
      </div>

      <div class="mb-3">
        <label for="date_iban" class="form-label">Splatnosť platobného príkazu</label>
        <input type="date" class="form-control" id="date_iban" name="date_iban" placeholder="dd. mm. rrrr">
      </div>
    </div>

    <div class="mb-3">
        <label for="info_name" class="form-label">Informácia pre príjemcu</label>
        <input type="text" class="form-control <" id="info_name" name="info_name" placeholder="Informácia pre príjemcu">
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Názov príjemcu</label>
        <input type="text" class="form-control <" id="name" name="name" placeholder="Názov príjemcu">
    </div>

    <div class="mb-3">
        <label for="adress" class="form-label">Adresa 1. riadok</label>
        <input type="text" class="form-control <" id="adress" name="adress" placeholder="Adresa 1. riadok">
    </div>
  </div>
  
  <div class="mb-3 d-flex justify-content-between">
    <input type="submit" name="submit" value="Odoslať" class="btn btn-primary w-100 mx-2">
    <input type="submit" name="preview" value="Ukážka" class="btn btn-secondary w-100 mx-2">
  </div>
</form>

<?php include 'inc/footer.php'; ?>