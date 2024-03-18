<?php include 'inc/header.php';?>
<?php include 'config/login_process.php';?>
<?php include 'config/register_process.php';?>
<?php include 'config/database.php';?>
<style><?php include 'styles/style.css';?></style>

<!-- Skript na schovanie alertu po určitom čase -->
<script>
  setTimeout(function(){
    document.querySelector('.alert').style.display = 'none';
  }, 4000);
</script>

<!-- Prihlásenie -->
<div class="container container-custom mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Login - Prihlásenie</h3>
                    <form action="config/login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" name="loginEmail" required>
                            <div class="d-grid"><br>
                            <?php if (isset($_SESSION['error'])) { 
                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>'; unset($_SESSION['error']);}?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="loginPassword" required>
                        </div>
                        <div class="d-grid">
                            <?php if (isset($_SESSION['error2'])) { 
                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error2'] . '</div>'; unset($_SESSION['error2']);}?>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>                   
        <!-- Registrácia - Zobrazenie v modálnom one -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title text-center">Registration - Registrácia</h3>
                    <!-- Odkaz na zobrazenie modálneho okna -->
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#registrationModal" style="margin-top: 10px;">
                        Registrovať sa
                    </button>
                    <div class="mt-3 text-center">
                        <h7>Nemáte účet? Zaregistrujte sa a získajte úplný prístup k funkciám aplikácie.</h7>
                    </div>
                </div>
                <div class="d-grid">
                    <?php if (isset($_SESSION['error3'])) { 
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error3'] . '</div>'; unset($_SESSION['error3']);}
                        if (isset($_SESSION['success'])){
                        echo '<div class="alert alert-info" role="alert">' . $_SESSION['success'] . '</div>'; unset($_SESSION['success']);}?>
                </div>
            </div>
        </div>

        <!-- Modálne okno s registráciou -->
        <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registrationModalLabel">Registration - Registrácia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="config/register_process.php" method="POST">
                            <div class="mb-3">
                                <label for="registerNickname" class="form-label">Zadajte svoju prezývku</label>
                                <input type="text" class="form-control" id="registerNickname" name="registerNickname" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="registerEmail" name="registerEmail" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="registerPassword" name="registerPassword" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Registration</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'inc/footer.php'; ?>
