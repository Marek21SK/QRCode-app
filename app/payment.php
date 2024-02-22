<?php 
    include 'inc/header.php';
    include 'config/database.php';

    $sum = $selectIBAN = "";
    $sumErr = $ibanErr = "";

    // Odoslať formulár
    if (isset($_POST['submit'])){    
        // Overiť sumu
        if (empty($_POST['sum'])){
            $sumErr = 'Suma je potrebná';
        }else{
            $sum = filter_input(INPUT_POST, 'sum', FILTER_SANITIZE_NUMBER_INT);
        }

        // Overiť IBAN
        $selectIBAN = isset($_POST['payment_id']);
        if (empty($selectIBAN)){
          $ibanErr = 'IBAN je potrebný';
        } else {
          $iban = filter_input(INPUT_POST, 'payment_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
          // Získanie id pre vybraný IBAN od používateľa, ktorý v poradí pridal daný IBAN
          $getIBANid = "SELECT iban.id FROM iban INNER JOIN users ON iban.iban_id = users.id WHERE iban.iban = ? AND iban.iban_id = ?"; //"SELECT id FROM iban WHERE iban = ?";
          $stmt = $conn->prepare($getIBANid);
          $stmt->bind_param("si", $iban, $user_id);
          $stmt->execute();
          $stmt->bind_result($iban_id);
          $stmt->fetch();
          $stmt->close();
        }

        if (empty($sumErr) && empty($ibanErr)){
          // Overiť hodnoty, ak nie sú vyplnené, tak nastaviť ich hodnotu na NULL ak sú vyplnené uložia sa vyplnené hodnoty do databázy
          $vs = !empty($_POST['vs']) ? $_POST['vs'] : NULL;
          $ss = !empty($_POST['ss']) ? $_POST['ss'] : NULL; 
          $ks = !empty($_POST['ks']) ? $_POST['ks'] : NULL;
          $moneytype = !empty($_POST['moneytype']) ? $_POST['moneytype'] : NULL;
          $name = !empty($_POST['name']) ? $_POST['name'] : NULL;
          $info_name = !empty($_POST['info_name']) ? $_POST['info_name'] : NULL;
          $adress = !empty($_POST['adress']) ? $_POST['adress'] : NULL;
          $adress2 = !empty($_POST['adress2']) ? $_POST['adress2'] : NULL;
          $date_iban = !empty($_POST['date_iban']) ? $_POST['date_iban'] : NULL;
        
            // Predpokladáme, že používateľ je prihlásený
            $user_id = $_SESSION['user_id'];
            
            // Pridať do databázy (id_používateľa, id_iban-u, suma, vs, ks, mena, meno, sprava pre príjemcu, adresa1, adresa2, dátum)
            $sql = "INSERT INTO payment (payment_id, iban_id, sum, vs, ss, ks, moneytype, name, info_name, adress, adress2, date_iban) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiisssssssss", $user_id, $iban_id, $sum, $vs, $ss, $ks, $moneytype, $name, $info_name, $adress, $adress2, $date_iban);

            if ($stmt->execute()){
                // Úspech
                header('Location: index.php');
            }else {
                // Chyba
                echo 'Error: ' . $stmt->error;
            }
            $stmt->close();
        }
    }

    /*
    // Kontrola, či je používateľ prihlásený / 1. spôsob overenie, momentálne nepoužívané
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
    */

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

    // Použitie funkcie či je používateľ prihlásený / zobrazenie IBAN-ov, povolenie písania do inputov / zobrazenie tlačidiel
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

<!-- Formulár pre všetky inputy -->
<form action="payment.php" method="POST" class="mt-4 w-75">
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3">
        <label for="payment_id" class="form-label">IBAN</label>
        <?php if ($ibanErr): ?>
          <select class="form-control is-invalid" id="payment_id" name="payment_id" <?php echo $loggedUser ? '' : 'disabled'; ?>>
        <?php else: ?>
          <select class="form-control" id="payment_id" name="payment_id" <?php echo $loggedUser ? '' : 'disabled'; ?>>
        <?php endif; ?>
          <option value="" selected disabled>Vyberte IBAN</option>
          <?php foreach ($savedIBANs as $ibanOption): ?>
            <option value="<?php echo $ibanOption; ?>"><?php echo formatIBAN($ibanOption); ?></option>
          <?php endforeach; ?>
        </select>
        <div class="invalid-feedback">
          <?php echo $ibanErr; ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="moneytype" class="form-label">Mena prevodu</label>
        <input type="text" class="form-control" id="moneytype" name="moneytype" maxlength="3" placeholder="EUR" <?php echo $loggedUser ? '' : 'disabled'; ?>>
      </div>

      <div class="mb-3">
        <label for="ks" class="form-label">Konštantný symbol</label>
        <input type="text" class="form-control" id="ks" name="ks" placeholder="1234" <?php echo $loggedUser ? '' : 'disabled'; ?>>
      </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
        <label for="sum" class="form-label">Suma</label>
        <input type="number" class="form-control <?php echo !$sumErr ?: 'is-invalid';?>" id="sum" name="sum" placeholder="10.00" <?php echo $loggedUser ? '' : 'disabled'; ?>>
        <div class="invalid-feedback">
          <?php echo $sumErr; ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="vs" class="form-label">Variabilný symbol</label>
        <input type="text" class="form-control" id="vs" name="vs" placeholder="9876543210" <?php echo $loggedUser ? '' : 'disabled'; ?>>
      </div>

      <div class="mb-3">
        <label for="ss" class="form-label">Špecifický symbol</label>
        <input type="text" class="form-control" id="ss" name="ss" placeholder="1234567890" <?php echo $loggedUser ? '' : 'disabled'; ?>>
      </div>

      <div class="mb-3">
        <label for="date_iban" class="form-label">Splatnosť platobného príkazu</label>
        <input type="date" class="form-control" id="date_iban" name="date_iban" placeholder="dd. mm. rrrr" <?php echo $loggedUser ? '' : 'disabled'; ?>>
      </div>
    </div>

    <div class="mb-3">
        <label for="info_name" class="form-label">Informácia pre príjemcu</label>
        <input type="text" class="form-control <" id="info_name" name="info_name" placeholder="Informácia pre príjemcu" <?php echo $loggedUser ? '' : 'disabled'; ?>>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Názov príjemcu</label>
        <input type="text" class="form-control <" id="name" name="name" placeholder="Názov príjemcu" <?php echo $loggedUser ? '' : 'disabled'; ?>>
    </div>

    <div class="mb-3">
        <label for="adress" class="form-label">Adresa 1. riadok</label>
        <input type="text" class="form-control <" id="adress" name="adress" placeholder="Adresa 1. riadok" <?php echo $loggedUser ? '' : 'disabled'; ?>>
    </div>

    <div class="mb-3">
        <label for="adress2" class="form-label">Adresa 2. riadok</label>
        <input type="text" class="form-control <" id="adress2" name="adress2" placeholder="Adresa 2. riadok" <?php echo $loggedUser ? '' : 'disabled'; ?>>
    </div>
  </div>
  
  <div class="mb-3 d-flex justify-content-between">
  <?php if ($loggedUser): ?>
    <input type="submit" name="submit" value="Odoslať" class="btn btn-primary ms-auto mx-2" style="width: 450px;">
    <button type="button" class="btn btn-secondary me-auto mx-2" data-bs-toggle="modal" data-bs-target="#previewModal" style="width: 450px;">Ukážka</button>
    <?php else: ?>
      <h5 class="text-center mx-auto">Pre prácu s týmto formulárom musíte byť prihlásený</h5>
    <?php endif; ?>
  </div>
</form>

<!-- Modálne okno pre zobrazenie ukážky -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="previewModalLabel">Ukážka údajov</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="prewiev_payment_id" class="form-label">IBAN</label>
                  <select class="form-control" id="prewiev_payment_id" name="prewiev_payment_id" disabled>
                  <option value="" selected disabled>Vyberte IBAN</option>
                  <?php foreach ($savedIBANs as $ibanOption): ?>
                    <option value="<?php echo $ibanOption; ?>" <?php echo (isset($_POST['payment_id']) && $_POST['payment_id'] == $ibanOption) ? 'selected' : ''; ?>>
                      <?php echo formatIBAN($ibanOption); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="preview_moneytype" class="form-label">Mena prevodu</label>
                <input type="text" class="form-control" id="preview_moneytype" name="preview_moneytype" value="<?php echo isset($_POST['moneytype']) ? htmlspecialchars($_POST['moneytype']) : ''; ?>" disabled>
              </div>

              <div class="mb-3">
                <label for="preview_ks" class="form-label">Konštantný symbol</label>
                <input type="text" class="form-control" id="preview_ks" name="preview_ks" value="<?php echo isset($_POST['ks']) ? htmlspecialchars($_POST['ks']) : ''; ?>" disabled>
              </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                <label for="preview_sum" class="form-label">Suma</label>
                <input type="number" class="form-control" id="preview_sum" name="preview_sum" value="<?php echo isset($_POST['sum']) ? htmlspecialchars($_POST['sum']) : ''; ?>" disabled>
              </div>

              <div class="mb-3">
                <label for="preview_vs" class="form-label">Variabilný symbol</label>
                <input type="text" class="form-control" id="preview_vs" name="preview_vs" value="<?php echo isset($_POST['vs']) ? htmlspecialchars($_POST['vs']) : ''; ?>" disabled>
              </div>

              <div class="mb-3">
                <label for="preview_ss" class="form-label">Špecifický symbol</label>
                <input type="text" class="form-control" id="preview_ss" name="preview_ss" value="<?php echo isset($_POST['ss']) ? htmlspecialchars($_POST['ss']) : ''; ?>" disabled>
              </div>

              <div class="mb-3">
                <label for="preview_date_iban" class="form-label">Splatnosť platobného príkazu</label>
                <input type="date" class="form-control" id="preview_date_iban" name="preview_date_iban" value="<?php echo isset($_POST['date_iban']) ? htmlspecialchars($_POST['date_iban']) : ''; ?>" disabled>
              </div>
            </div>

            <div class="mb-3">
                <label for="preview_info_name" class="form-label">Informácia pre príjemcu</label>
                <input type="text" class="form-control <" id="preview_info_name" name="preview_info_name" value="<?php echo isset($_POST['info_name']) ? htmlspecialchars($_POST['info_name']) : ''; ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="preview_name" class="form-label">Názov príjemcu</label>
                <input type="text" class="form-control <" id="preview_name" name="preview_name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="preview_adress" class="form-label">Adresa 1. riadok</label>
                <input type="text" class="form-control <" id="preview_adress" name="preview_adress" value="<?php echo isset($_POST['adress']) ? htmlspecialchars($_POST['adress']) : ''; ?>" disabled>
            </div>

            <div class="mb-3">
                <label for="preview_adress2" class="form-label">Adresa 2. riadok</label>
                <input type="text" class="form-control <" id="preview_adress2" name="preview_adress2" value="<?php echo isset($_POST['adress2']) ? htmlspecialchars($_POST['adress2']) : ''; ?>" disabled>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
        </div>
    </div>
</div>
</div>

<?php include 'inc/footer.php'; ?>