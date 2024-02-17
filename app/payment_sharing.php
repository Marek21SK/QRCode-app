<?php 
    include 'inc/header.php';
    include 'config/database.php';

    $iban = $sum = "";
    $ibanErr = $sumErr = "";
    /*
        $date_iban = $email = $name = $info_name = $adress = $vs = $ss = $ks = $moneytype = "";
        $date_ibanErr = $emailErr = $nameErr = $info_nameErr = $adressErr $vsErr = $ssErr = $ksErr = $moneytypeErr = "";
    */
    // Form submit
    if (isset($_POST['submit'])){
        // Validate IBAN
        if (empty($_POST['iban'])){
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
?>

<!-- Form for all inputs (Túto časť kódu mám ešte rozpracovanú.-->
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="mt-4 w-75">
  <div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="iban" class="form-label">IBAN</label>
            <input type="text" class="form-control <?php echo !$ibanErr ?: 'is-invalid';?>" id="iban" name="iban" placeholder="SK88 8888 8888 8888 8888 8888">
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
  
  <div class="mb-3">
    <input type="submit" name="submit" value="Send" class="btn btn-dark w-100">
  </div>
</form>


<?php include 'inc/footer.php'; ?>