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
          $sql = "SELECT iban, iban_name FROM iban WHERE iban_id = ?";
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
          "order": [[2, 'asc']],
          "initComplete": function(settings, json){
            $('#ibanTable').show();
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
