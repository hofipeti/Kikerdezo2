<?php
session_start();
$_GET['szotar_id'] = isset($_GET['szotar_id']) ? intval($_GET['szotar_id']) : 0;
if ($_GET['szotar_id'] <= 0) {
    $_SESSION['error'] = "Hiányzó vagy érvénytelen szotar_id a kérésben.";
    header('Location: kikerdezes_inditas.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $szotar_id = isset($_POST['szotar_id']) ? intval($_POST['szotar_id']) : 0;
    $tipus = isset($_POST['tipus']) ? intval($_POST['tipus']) : 0;

    if ($szotar_id <= 0 || ($tipus !== 1 && $tipus !== 2)) {
        $_SESSION['error'] = "Érvénytelen adatok a kikérdezés indításához.";
        header('Location: kikerdezes_inditas.php?szotar_id=' . (int)$szotar_id);
        exit;
    }

    // Itt indíthatod el a kikérdezést a megadott szótár és típus alapján.
    // Például átirányíthatsz egy másik oldalra, ahol a kikérdezés zajlik.
    header('Location: kikerdezes.php?szotar_id=' . (int)$szotar_id . '&tipus=' . (int)$tipus);
    exit;
}

?><!DOCTYPE html>
<html lang="hu">    

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kikérdezés indítása</title>  

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Kikérdezés indítása</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role   
="alert">

    <?php endif; ?>
            <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
    <form action="kikerdezes_inditas.php" method="post" class="mt-4">
        <input type="hidden" name="szotar_id" value="<?php echo (int)$_GET['szotar_id']; ?>">
        <div class="mb-3">
            <label for="tipus" class="form-label">Kikérdezés típusa:</label>
            <select id="tipus" name="tipus" class="form-select" required>
                <option value="1" selected>Gyakorlás</option>
                <option value="2">Vizsga</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Kikérdezés indítása <span class="ms-2">&rarr;</span></button>
    </form>
</body>
</html> 