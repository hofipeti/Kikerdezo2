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
  <h3>Üdvözöllek, <?php echo $_SESSION['user']->Nev; ?>!</h3>
  <a href="logout.php" class="btn btn-danger mt-3">Kijelentkezés</a>

</body>
</html>
