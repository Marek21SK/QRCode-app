<?php include 'inc/header.php';?>
<?php include 'config/database.php';?>
<?php

    $sum = $selectIBAN = $moneytype = "";
    $sumErr = $ibanErr = $moneytypeErr = "";

    // Odoslať formulár
    if (isset($_POST['submit'])){    
        // Overiť sumu
        if (empty($_POST['sum'])){
            $sumErr = 'Suma je potrebná';
        }else{
            $sum = filter_input(INPUT_POST, 'sum', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }

        // Overiť IBAN
        $selectIBAN = isset($_POST['payment_id']);
        if (empty($selectIBAN)){
          $ibanErr = 'IBAN je potrebný';
        } else {
          $iban = filter_input(INPUT_POST, 'payment_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
          // Získanie id pre vybraný IBAN od používateľa, ktorý v poradí pridal daný IBAN
          $getIBANid = "SELECT iban.id FROM iban INNER JOIN users ON iban.iban_id = users.id WHERE iban.iban = ? AND iban.iban_id = ?";
          $stmt = $conn->prepare($getIBANid);
          $stmt->bind_param("si", $iban, $user_id);
          $stmt->execute();
          $stmt->bind_result($iban_id);
          $stmt->fetch();
          $stmt->close();
        }

        // Overiť či bola vo formulári zadaná aj "Mena prevodu"
        if (empty($_POST['moneytype'])){
          $moneytypeErr = 'Mena prevodu je potrebná';
        }else{
          $moneytype = filter_input(INPUT_POST, 'moneytype', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        if (empty($sumErr) && empty($ibanErr) && empty($moneytypeErr)){
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
          $payment_name = !empty($_POST['payment_name']) ? $_POST['payment_name'] : NULL;
        
            // Predpokladáme, že používateľ je prihlásený
            $user_id = $_SESSION['user_id'];
            
            // Pridať do databázy (id_používateľa, id_iban-u, suma, vs, ks, mena, meno, sprava pre príjemcu, adresa1, adresa2, dátum)
            $sql = "INSERT INTO payment (payment_id, iban_id, sum, vs, ss, ks, moneytype, name, info_name, adress, adress2, date_iban, payment_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iidssssssssss", $user_id, $iban_id, $sum, $vs, $ss, $ks, $moneytype, $name, $info_name, $adress, $adress2, $date_iban, $payment_name);

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

    // Funkcia na získanie IBAN-ov pre aktuálneho používateľa
    function getSavedIBANs($conn, $user_id){
      $ibanList = array();

      // Príprava SQL dotazu pre získanie IBAN-u z databázy
      $sql = "SELECT id, iban FROM iban WHERE iban_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      // Načítanie IBAN-ov do poľa
      while ($row = $result->fetch_assoc()){
        $ibanList[$row['id']] = $row['iban'];
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

    // Funkcia na zobrazenie IBAN s medzerami po 4 znakoch
    function formatIBAN($iban) {
      $formattedIBAN = chunk_split($iban, 4, ' ');
      // Odstránenie medzery na konci
      return rtrim($formattedIBAN);
    }
?>

<!-- Formulár pre všetky inputy -->
<div class="payment-container container d-flex flex-column align-items-center">
<form action="payment.php" method="POST" class="mt-4 w-75">
  <div class="row">
  <h3 class="form-title display-8 mx-auto">Formulár na pridanie novej platby</h3><hr>
    <div class="mb-3">
      <label for="payment_name" class="form-label">Názov platby</label>
      <input type="text" class="form-control" id="payment_name" name="payment_name" placeholder="Názov platby" <?php echo $loggedUser ? '' : 'disabled'; ?>>
    </div>
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

      <div class="mb-3"> <!-- Ak by som musel skriť tento HTML element style="display: none;" + na koniec mimo div <input type="hidden" name="moneytype" value="EUR"/> -->
        <label for="moneytype" class="form-label">Mena prevodu</label>
        <select class="form-control <?php echo !$moneytypeErr ?: 'is-invalid';?>" id="moneytype" name="moneytype" <?php echo $loggedUser ? '' : 'disabled'; ?>>
          <option value="EUR" selected>EUR</option>
        </select>
        <small>Mena prevodu automaticky nastavená</small>
        <div class="invalid-feedback">
          <?php echo $moneytypeErr; ?>
        </div>
      </div>

      <div class="mb-3">
        <label for="ks" class="form-label">Konštantný symbol</label>
        <input type="text" class="form-control" id="ks" name="ks" placeholder="1234" <?php echo $loggedUser ? '' : 'disabled'; ?>>
      </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
        <label for="sum" class="form-label">Suma</label>
        <input type="number" step="0.01" class="form-control <?php echo !$sumErr ?: 'is-invalid';?>" id="sum" name="sum" placeholder="10.00" <?php echo $loggedUser ? '' : 'disabled'; ?>>
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
        <!--<input type="hidden" id="payment_name" name="payment_name" value="Uložená platba"> -->
    </div>
  </div>
  
  <div class="button-container">
  <?php if ($loggedUser): ?>
    <input type="submit" name="submit" value="Uložiť platbu" class="btn btn-primary custom-btn">
    <button type="button" class="btn btn-secondary custom-btn" data-bs-toggle="modal" data-bs-target="#previewModal" onclick="generateQRCode()">Ukážka</button>
    <?php else: ?>
      <h5 class="text-center mx-auto">Pre prácu s týmto formulárom musíte byť prihlásený</h5>
    <?php endif; ?>
  </div>
</form>
</div>

<!-- Modálne okno pre zobrazenie ukážky -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="previewModalLabel">Ukážka údajov</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
          <div id="qrPrompt" class="text-center mx-auto mb-3"><h5>Tu sa zobrazí vygenerovaný QR kód</h5></div>
            <div id="qrPrompt2" class="text-center mx-auto mb-3"><h5>Váš vygenerovaný QR kód</h5></div>
            <?php echo '<style> #qrPrompt2 {display: none;} </style>';?>
            <div class="mb-3">
              <label for="preview_payment_name" class="form-label">Názov platby</label>
              <input type="text" class="form-control" id="preview_payment_name" name="preview_payment_name" value="<?php echo isset($_POST['payment_name']) ? htmlspecialchars($_POST['payment_name']) : ''; ?>" disabled>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="prewiev_payment_id" class="form-label">IBAN</label>
                  <select class="form-control" id="preview_payment_id" name="preview_payment_id" disabled>
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
                <select class="form-control" id="preview_moneytype" name="preview_moneytype" disabled>
                  <option value="" selected disabled>Vyberte menu prevodu</option>
                  <option value="EUR" <?php echo (isset($_POST['moneytype']) && $_POST['moneytype'] == 'EUR') ? 'selected' : ''; ?>>EUR</option>
                </select>
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

<!-- IMPLEMENTOVANIE API -->
<!-- Generovanie QR kódov funguje iba na skutočných IBAN číslach -->
<script>
  function generateQRCode() {
    // Získanie hodnôt z príslušných polí vo formulári
    var payment_name = $('#preview_payment_name').val();
    var iban = $('#preview_payment_id').val();
    var moneytype = $('#preview_moneytype').val();
    var ks = $('#preview_ks').val();
    var sum = $('#preview_sum').val();
    var vs = $('#preview_vs').val();	
    var ss = $('#preview_ss').val();
    var date_iban = $('#preview_date_iban').val();
    var info_name = $('#preview_info_name').val();
    var name = $('#preview_name').val();
    var adress = $('#preview_adress').val();
    var adress2 = $('#preview_adress2').val();

    if (!iban || !sum || !moneytype || iban === "0" || sum === "0" || moneytype === "0") {
      Swal.fire({
        title: 'Chyba!',
        text: 'Nie je možné vygenerovať QR kód, pokiaľ nie je zadaný IBAN, suma a mena danej platby.',
        icon: 'error',
      }).then(() => {
        $('#previewModal').modal('hide');
      });
      return;
    }

    // Funkcia na zrušenie hodnôt vo formulári aby sa pri ďalšom otvorení modálneho okna nezobrazovali predchádzajúce hodnoty
    function resetForm() {
      $('#preview_payment_name').val('');
      $('#preview_payment_id').val('');
      $('#preview_moneytype').val('');
      $('#preview_ks').val('');
      $('#preview_sum').val('');
      $('#preview_vs').val('');
      $('#preview_ss').val('');
      $('#preview_date_iban').val('');
      $('#preview_info_name').val('');
      $('#preview_name').val('');
      $('#preview_adress').val('');
      $('#preview_adress2').val('');
    }

    // Skript na zavretie modálneho okna
    $('#previewModal').on('hidden.bs.modal', function () {
      resetForm();
      $('#previewModal .modal-body img').remove();
      $('#qrPrompt2').hide();
      $('#qrPrompt').show();
    });

    // Vymazanie starého QR kódu po aktualizovaní formuláru vygenerovaním nového QR kódu
    $('#previewModal .modal-body img').remove();
    $('#qrPrompt2').hide();
    $('#qrPrompt').show();
    
    // Vytvorenie URL pre GET request (URL upravená podľa dokumentácii API)
    var getUrl = 'https://api.freebysquare.sk/pay/v1/generate-png-v2?size=200&color=3&transparent=true&payments=[{"amount":' + sum + ',"currencyCode":"' + moneytype + '","paymentDueDate":"' + paymentDueDate + '","variableSymbol":"' + vs + '","constantSymbol":"' + ks + '","specificSymbol":"' + ss + '","paymentNote":"' + info_name + '","beneficiaryName":"' + name + '","beneficiaryAddressLine1":"' + adress + '","beneficiaryAddressLine2":"' + adress2 + '","bankAccounts":[{"iban":"' + iban + '","bic":null}]}]';

    // Získanie hodnoty z pola date_iban
    var date_iban = $('#preview_date_iban').val();
     // Preformátovanie na 'YYYYMMDD' tak ako to API vyžaduje
    var paymentDueDate = date_iban ? date_iban.replace(/-/g, '') : '';

    // Vytvorenie JSON dát pre POST request podľa dokumentácii API
    var postData = {
      "size": 200,
      "color": 3,
      "transparent": true,
      "payments": [
        {
          "amount": sum,
          "currencyCode": moneytype,
          "paymentDueDate": paymentDueDate,
          "variableSymbol": vs,
          "constantSymbol": ks,
          "specificSymbol": ss,
          "paymentNote": info_name,
          "beneficiaryName": name,
          "beneficiaryAddressLine1": adress,
          "beneficiaryAddressLine2": adress2,
          "bankAccounts": [
            {
              "iban": iban,
              "bic": null
            }
          ]
        }
      ]
    };

    // Vytvorenie novej XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://api.freebysquare.sk/pay/v1/generate-png-v2', true);
    xhr.responseType = 'blob';
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function(e) {
      if (this.status == 200) {
        var blob = this.response;
        var img = document.createElement('img');
        img.onload = function(e) {
          window.URL.revokeObjectURL(img.src);
        };
        img.src = window.URL.createObjectURL(blob);
        img.className = 'mx-auto d-block';
        $('#previewModal .modal-body').prepend(img);
        $('#qrPrompt').hide();
        $('#qrPrompt2').show();
      }
    };
    xhr.onerror = function() {
      Swal.fire({
        title: 'Chyba!',
        text: 'Chyba pri generovaní QR kódu, skúste to prosím neskôr.',
        icon: 'error',
        footer: '<a href="#">Prečo mi vyskočila táto chyba?</a>',
      }).then(() => {
        $('#previewModal').modal('hide');
      });
    };
    xhr.send(JSON.stringify(postData));
  }
</script>

<?php include 'inc/footer.php'; ?>