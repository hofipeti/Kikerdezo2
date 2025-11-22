<?php

require __DIR__.'/inc/config.php';
require_once __DIR__.'/inc/functions.php';
require_once __DIR__.'/model/User.php';
session_start();
$nyelvek = getNyelvek();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the 'nev' parameter
    if (isset($_POST['nev']) && !empty(trim($_POST['nev']))) {
        $nev = trim($_POST['nev']);

        // Nyelv ellenőrzés: legyenek meglévők és különbözők
        $nyelv1 = isset($_POST['nyelv1']) ? trim($_POST['nyelv1']) : '';
        $nyelv2 = isset($_POST['nyelv2']) ? trim($_POST['nyelv2']) : '';
        if ($nyelv1 === '' || $nyelv2 === '') {
            $_SESSION['error'] = "Mindkét nyelvet ki kell választani.";
            header("Location: uj_szotar.php");
            exit;
        }
        if ($nyelv1 === $nyelv2) {
            $_SESSION['error'] = "A két kiválasztott nyelv nem lehet ugyanaz.";
            header("Location: uj_szotar.php");
            exit;
        }

        if (hasSzotar($_SESSION['user']->UserId, $nev   )) {
            $_SESSION['error'] = "A szótár már létezik.";
            header("Location: uj_szotar.php");
            exit;
        }

        createSzotar($_SESSION['user']->UserId, $nev, $nyelv1, $nyelv2);
        $_SESSION['info'] = "A szótár sikeresen létrehozva: " . htmlspecialchars($nev, ENT_QUOTES, 'UTF-8');
        header("Location: index.php");
    } else {
        $_SESSION['error'] = "A név megadása kötelező.";
        header("Location: uj_szotar.php");
        exit;
    }
}
/*
 else {
    $_SESSION['error'] = "Érvénytelen kérésmód.";
    header("Location: uj_szotar.php");
    exit;
}*/

$conn->close();
?>

<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Új szótár - feltöltés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Új szótár feltöltése</h1>

        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="uj_szotar.php" method="post" novalidate>
            <div class="mb-3">
                <label for="nev" class="form-label">Szótár neve</label>
                <input id="nev" name="nev" type="text" class="form-control" placeholder="Add meg a szótár nevét..." required>
                <div class="form-text">Adj meg egy rövid, egyedi nevet a szótárhoz.</div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label for="nyelv1" class="form-label">Nyelv 1</label>
                    <select id="nyelv1" name="nyelv1" class="form-select" required>
                        <option value="">-- Válassz --</option>
                        <?php foreach ($nyelvek as $n): ?>
                            <option value="<?php echo htmlspecialchars($n['nyelv_id'] ?? $n['kod'] ?? $n['megnevezes'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($n['megnevezes'] ?? ($n['kod'] ?? '---'), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="nyelv2" class="form-label">Nyelv 2</label>
                    <select id="nyelv2" name="nyelv2" class="form-select" required>
                        <option value="">-- Válassz --</option>
                        <?php foreach ($nyelvek as $n): ?>
                            <option value="<?php echo htmlspecialchars($n['nyelv_id'] ?? $n['kod'] ?? $n['megnevezes'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($n['megnevezes'] ?? ($n['kod'] ?? '---'), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Feltöltés</button>
                <button type="reset" class="btn btn-outline-secondary">Törlés</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>