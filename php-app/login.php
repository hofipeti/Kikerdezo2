<?php
require_once __DIR__ . '/inc/bootstrap.php';
session_start();
include_once 'inc/config.php'; // mysqli kapcsolat
include_once 'inc/functions.php'; // segédfüggvények


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lekérdezés prepared statementtel

    $user = getUserByLogin($username, $password);


    if ($user !== null) {
        // Sikeres bejelentkezés
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        // Hibás bejelentkezés
        $_SESSION['error'] = "Hibás felhasználónév vagy jelszó!";
        header("Location: login.html");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow" style="width: 22rem;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Bejelentkezés</h4>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Felhasználónév</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Jelszó</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Belépés</button>
            </form>
        </div>
    </div>
</body>

</html>