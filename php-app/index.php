<?php
require_once 'model/User.php';
session_start();




if (!isset($_SESSION['user'])) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <title>Főoldal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h3>Üdvözöllek, <?php echo htmlspecialchars($_SESSION['user']->Nev ?? 'ismeretlen', ENT_QUOTES, 'UTF-8'); ?>!</h3>
<?php if (isset($_SESSION['info'])): ?>
            <div class="info alert-info" role="info">
                <?php echo htmlspecialchars($_SESSION['info'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['info']); ?>
            </div>
        <?php endif; ?>
  <a href="uj_szotar.html" class="btn btn-primary mt-3">Új szótár hozzáadása</a>
  <a href="logout.php" class="btn btn-danger mt-3">Kijelentkezés</a>

</body>
</html>
