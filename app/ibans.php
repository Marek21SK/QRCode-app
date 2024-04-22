<?php include 'inc/header.php'; ?>
<?php include 'config/database.php'; ?>

<!-- Formulár na pridanie IBAN -->
<div class="index-custom container d-flex flex-column align-items-center justify-content-center">
    <div class="container" style="margin-bottom: 10px;">
        <h4 class="d-flex justify-content-center align-items-center mb-3">Formulár na pridanie nového IBAN</h4>
        <div class="row">
            <div class="col-md-6 mx-auto d-flex justify-content-center align-items-center"><br>
                <div class="card" style="padding: 10px; margin-bottom: 10px; border-radius: 15px; border-width: medium; border-color: lightgrey;">
                    <div class="card-body">
                        <form action="config/add_iban_process.php" method="POST">
                            <div class="center-container" style="display: flex; justify-content: center;">
                                <div class="row">
                                <div class="mb-3">
                                    <label for="add_iban" class="form-label">Zadajte IBAN:</label>
                                    <input style="padding-right: 25px; margin: 10px auto;" type="text" class="form-control" id="add_iban" name="iban" placeholder="SK88 8888 8888 8888 8888 8888" maxlength="29" required>
                                    <label for="iban_name" class="form-label">Názov IBAN:</label>
                                    <input style="padding-right: 25px; margin: 10px auto;" type="text" class="form-control" id="iban_name" name="iban_name" placeholder="Prosím, uveďte účel použitia tohto IBAN" maxlength="255" required>
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

<!-- Skript, ktorý pri pridávaní IBAN-u sa zobrazí tak, že každé 4 znaky dá medzeru pre lepšie skontrolovanie -->
<script>
  document.getElementById('add_iban').addEventListener('input', function (event) {
    var ibanSpaces = event.target.value.replace(/\s/g, '');
    var ibanFormat = ibanSpaces.replace(/(.{4})/g, '$1 ').trim();
    event.target.value = ibanFormat;
  });
</script>

<!-- Skript na schovanie alertu po určitom čase -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        var alertElement = document.querySelector('.alert');
        if (alertElement) {
        alertElement.style.display = 'none';
        }
    }, 4000);
    });
</script>

<?php include 'inc/footer.php'; ?>
