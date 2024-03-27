<?php include 'inc/header.php'; ?>
<?php include 'config/database.php'; ?>
<?php

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

    // Funkcia na získanie platby pre aktuálneho používateľa
    function getSavedPayments($conn, $user_id){
      $paymentList = array();

      // Príprava SQL dotazu pre získanie platby z databázy
      $sql = "SELECT * FROM payment WHERE payment_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();

      // Načítanie platby do poľa
      while ($row = $result->fetch_assoc()){
        $paymentList[] = $row;
      }
      $stmt->close();

      return $paymentList;
    }

    // Použitie funkcie či je používateľ prihlásený / zobrazenie IBAN-ov, povolenie písania do inputov / zobrazenie tlačidiel
    $loggedUser = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($loggedUser){
        $savedPayments = getSavedPayments($conn, $loggedUser);
        $savedIBANs = getSavedIBANs($conn, $loggedUser);
    }else {
        $savedIBANs = array();
        $savedPayments = array();
    }

    // Funkcia na zobrazenie IBAN s medzerami po 4 znakoch
    function formatIBAN($iban) {
        $formattedIBAN = chunk_split($iban, 4, ' ');
        // Odstránenie medzery na konci
        return rtrim($formattedIBAN);
    }
?>

<!-- Kontajner pre zobrazenie uložených platieb -->
<div class="container mt-4">
    <h1 class=>Uložené platby</h1>
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <?php foreach ($savedPayments as $payment): ?>
            <div class="col-md-4 mb-4">
                <div class="card mb-4 h-100" id="modal-card">
                    <div class="card-body" id="card-body">
                        <h5 class="card-title">Uložená platba</h5>
                        <p id="payment-data">
                            Platba: <?= htmlspecialchars($payment['id']) ?><br>
                            <?php $ibanId = $payment['iban_id'];
                            $iban = $payment['iban_id'] ? $savedIBANs[$payment['iban_id']] : '';
                            $formattedIban = formatIBAN($iban);
                            echo 'IBAN: '. ($ibanId ? htmlspecialchars($formattedIban) : '') . '<br>'; ?>
                            Suma: <?= $payment['sum'] ? htmlspecialchars($payment['sum']): '' ?><br>
                            VS: <?= $payment['vs'] ? htmlspecialchars($payment['vs']): '' ?><br>
                            KS: <?= $payment['ks'] ? htmlspecialchars($payment['ks']): '' ?><br>
                            ŠS: <?= $payment['ss'] ? htmlspecialchars($payment['ss']): '' ?><br>
                            Mena: <?= $payment['moneytype'] ? htmlspecialchars($payment['moneytype']): '' ?><br>
                            Príjemca: <?= $payment['name'] ? htmlspecialchars($payment['name']): '' ?><br>
                            Adresa 1: <?= $payment['adress'] ? htmlspecialchars($payment['adress']): '' ?><br>
                            Adresa 2: <?= $payment['adress2'] ? htmlspecialchars($payment['adress2']): '' ?><br>
                            Splatnosť: <?= $payment['date_iban'] ? date('Y-m-d', strtotime($payment['date_iban'])) : '' ?><br>
                            Informácia: <?= $payment['info_name'] ? htmlspecialchars($payment['info_name']): '' ?><br>
                        </p> 
                        <button type="button" data-bs-toggle="modal" data-bs-target="#previewModal" data-paymentid="<?= $payment['id']; ?>" id="previewModal-button" class="btn btn-primary load-payment-btn" onclick="loadAndShowPreview(<?= $payment['id']; ?>)">Načítať platbu do ukážky</button>                    
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Skript na zobrazenie platby v "Ukážka" po stlačení buttonu-->
<script>
  function loadAndShowPreview(paymentId){
    var payment = <?= json_encode($savedPayments); ?>;
    var selectedPayment = payment.find(p => p.id == paymentId);

    var $savedIBANs = <?= json_encode($savedIBANs); ?>;
    var formattedDate = moment(selectedPayment.date_iban).format("YYYY-MM-DD");

    $('#preview_payment_id').val($savedIBANs[selectedPayment.iban_id]);
    $('#preview_moneytype').val(selectedPayment.moneytype);
    $('#preview_ks').val(selectedPayment.ks);
    $('#preview_sum').val(selectedPayment.sum);
    $('#preview_vs').val(selectedPayment.vs);
    $('#preview_ss').val(selectedPayment.ss);
    $('#preview_date_iban').val(formattedDate);
    $('#preview_info_name').val(selectedPayment.info_name);
    $('#preview_name').val(selectedPayment.name);
    $('#preview_adress').val(selectedPayment.adress);
    $('#preview_adress2').val(selectedPayment.adress2);
    
    $('#previewModal').modal('show');

    generateQRCode();
  } 
</script>

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
      alert("Nie je možné vygenerovať QR kód, pokiaľ nie je zadaný IBAN, suma a mena danej platby.");
      return;
    }

    // Funkcia na zrušenie hodnôt vo formulári aby sa pri ďalšom otvorení modálneho okna nezobrazovali predchádzajúce hodnoty
    function resetForm() {
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
      alert('Chyba pri generovaní QR kódu, skúste to prosím neskôr.');
    };
    xhr.send(JSON.stringify(postData));
  }
</script>

<?php include 'inc/footer.php'; ?>