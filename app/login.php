<?php include 'inc/header.php';?>
<?php include 'config/login_process.php';?>
<?php include 'config/register_process.php';?>
<?php include 'config/database.php';?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center">Login - Prihlásenie</h3>
                        <form action="config/login_process.php" method="POST">
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="loginEmail" name="loginEmail" required>
                                <?php if (isset($_SESSION['error'])) { 
                                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>'; unset($_SESSION['error']);}?>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="loginPassword" name="loginPassword" required>
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center">Registration - Registrácia</h3>
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

<?php include 'inc/footer.php'; ?>
