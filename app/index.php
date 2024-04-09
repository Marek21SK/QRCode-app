<?php include 'inc/header.php'; ?>
<?php include 'config/database.php'; ?>

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
        <?php if (isset($_SESSION['success1'])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION['success1'] . '</div>';
            unset($_SESSION['success1']);} ?>
      </div>
    </div>

    <div class="container" style="margin-bottom: 10px;">
    <h4 class="d-flex justify-content-center align-items-center">Prehľad IBAN-ov</h4><br>
      <div class="row"><br>
        <div class="col-md-12">
          <!-- Zobrazenie prehľadu IBAN-ov -->
          <?php
          // Získanie IBAN-ov aktuálne prihláseného používateľa
          $sql = "SELECT iban, iban_name, id FROM iban WHERE iban_id = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows > 0) {
          ?>
            <div style="width: 100%; max-width: 800px; margin: auto;">
              <div class="table-responsive">
                <table id="ibanTable" class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 40%;">IBAN</th>
                      <th scope="col" style="width: 50%;">Názov</th>
                      <th scope="col" style="width: 10%;">Zmeny</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                      $formattedIBAN = formatIBAN($row["iban"]);
                      $iban_name = $row["iban_name"];
                      $iban_id = $row["iban"];
                      $iban = $row["id"];
                      echo "<tr data-id-iban='" . $iban . "'><td class='iban' style='white-space: nowrap;'>" . $formattedIBAN . "</td><td class='iban-name' style='white-space: nowrap;'>" . $iban_name . "</td><td class='centered-button'><button class='btn btn-success edit-button btn-block'>Editovať</button></td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php
          } else {
            echo "<div class='mx-auto d-flex justify-content-center align-items-center'>
                    <h6>Žiadne IBAN pre zobrazenie.</h6></div>";}?>
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
  </div>
<?php endif; ?>

<!-- Modálne okno pre editovanie IBAN-u -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editovať názov IBAN</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          <div class="form-group">
            <label for="iban-name">Názov IBAN</label>
            <input type="text" class="form-control" id="iban-name" name="iban-name">
          </div>
          <input type="hidden" id="iban-id" name="iban-id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušiť</button>
        <button type="button" class="btn btn-primary" id="saveButton">Uložiť zmeny</button>
      </div>
    </div>
  </div>
</div>

<?php
// Funkcia na zobrazenie IBAN s medzerami po 4 znakoch
function formatIBAN($iban)
{
  $formattedIBAN = chunk_split($iban, 4, ' ');
  // Odstránenie medzery na konci
  return rtrim($formattedIBAN);
}
?>

<!-- Skript pre tabuľku DataTables -->
<script>
  $(document).ready(function() {
      $('#ibanTable').DataTable({
          "pageLength": 10,
          "lengthMenu": [[5, 10, 15, 20], [5, 10, 15, "MAX"]],
          "searching": true,
          "language": {
              "lengthMenu": "Zobraziť _MENU_ záznamov na stránku",
              "zeroRecords": "Nič sa nenašlo - ospravedlňujeme sa",
              "info": "Zobrazenie strany _PAGE_ z _PAGES_",
              "infoEmpty": "Žiadne záznamy k dispozícii",
              "infoFiltered": "(filtrované z celkových _MAX_ záznamov)",
              "search": "Hľadať:",
              "paginate": {
                  "next":       "Ďalší",
                  "previous":   "Predchádzajúci"
              }
          },
          "order": [[3, 'asc']],
          "initComplete": function(settings, json){
            $('#ibanTable').show();
          }
      });
  });
</script>

<!-- Skript pre editáciu IBAN-u -->
<script>
$(document).ready(function() {
  $('.edit-button').click(function() {
    var row = $(this).closest('tr');
    var ibanName = row.find('.iban-name').text();
    var ibanId = $(this).closest('tr').data('id-iban');

    $('#iban-name').val(ibanName);
    $('#iban-id').val(ibanId);

    $('#editModal').modal('show');
  });

  $('.btn-secondary').click(function() {
    $('#editModal').modal('hide');
  });

  $('#saveButton').click(function(e) {
    e.preventDefault();

    var ibanName = $('#iban-name').val();
    var ibanId = $('#iban-id').val();

    $.ajax({
      type: 'POST',
      url: 'config/edit_iban.php',
      data: {
        'iban-name': ibanName,
        'iban-id': ibanId
      },
      dataType: 'json',
      success: function(data) {
        if (data.success) {
          $("#editModal").modal('hide');
          location.reload();
        } else {
          // Ak došlo k chybe, zobrazíme chybovú správu
          alert("Došlo k chybe pri ukladaní zmien: " + data.error);
        }
      },
      error: function() {
        alert("Došlo k chybe pri komunikácii so serverom.");
      }
    });
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
