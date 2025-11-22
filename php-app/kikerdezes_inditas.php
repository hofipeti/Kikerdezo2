<?php

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/model/User.php';
session_start();

$szotarok = getSzotarByUser($_SESSION['user']->UserId);




$szotar_id = null;

if (isset($_GET['korabbiLezaras']) && $_GET['korabbiLezaras'] == 'true') {
    // Korábbi feladat lezárása
    feladatLezarasa($_SESSION['user']->UserId);
    header('Location: kikerdezes_inditas.php');
    exit;
}

if (isset($_GET['szotar_id'])) {
    $szotar_id = $_GET['szotar_id'];
    $o = ['szotar' => $szotarok, 'szotar_id' => $szotar_id];
    /* TODO jogosultság ellenőrzés!
    $matches = array_filter($szotarok, function ($obj) use ($o) {

        return $obj['szotar']['szotar_id'] === $obj['szotar_id'];
    });

    if (count($matches) === 0) {
        $_SESSION['error'] = "Nincs jogosultságod a kiválasztott szótárhoz.";
        header('Location: kikerdezes_inditas.php');
        exit;
    }
        */
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_szotar_ids = isset($_POST['szotar_id']) ? $_POST['szotar_id'] : [];
    $tipus = isset($_POST['tipus']) ? $_POST['tipus'] : '1';
    $szoszam_tipus = isset($_POST['szoszam_tipus']) ? $_POST['szoszam_tipus'] : null;
    $szam = isset($_POST['szam']) ? (int) $_POST['szam'] : null;

    if (empty($selected_szotar_ids)) {
        $_SESSION['error'] = "Legalább egy szótárt ki kell választani.";
        header('Location: kikerdezes_inditas.php');
        exit;
    }
    if ($szoszam_tipus === 'N' && ($szam === null || $szam <= 0)) {
        $_SESSION['error'] = "Kérlek, adj meg egy érvényes számot a szavak számához.";
        header('Location: kikerdezes_inditas.php');
        exit;
    }

    if ($szoszam_tipus === null) {
        $_SESSION['error'] = "Kérlek, válassz mennyiséget!";
        header('Location: kikerdezes_inditas.php');
        exit;
    }

    $res = createFeladat($_SESSION['user']->UserId, $selected_szotar_ids, $tipus, $szoszam_tipus, $szam);

    if ($res === false) {

        $_SESSION['error'] = "Hiba történt a kikérdezés indításakor.";
        header('Location: kikerdezes_inditas.php');
        exit;
    } else {
        $_SESSION['info'] = "A kikérdezés sikeresen elindítva.";
        header('Location: feladat.php');
        exit;
    }



} else if (hasAktivFeladat($_SESSION['user']->UserId)) {
    $kerdes = true;
    
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
    <a href="index.php" class="btn btn-secondary mb-3">&larr; Vissza a főoldalra</a>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
            unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="kikerdezes_inditas.php" method="post" class="mt-4">
            <fieldset>
                <label for="szotar_id" class="form-label">Szótár(ak):</label>
                <select id="szotar_id" name="szotar_id[]" class="form-select" multiple required>
                    <?php

                    foreach ($szotarok as $szotar):
                        ?>
                        <option value="<?php echo (int) $szotar['szotar_id']; ?>" <?php if ($szotar['szotar_id'] == $szotar_id)
                                echo 'selected'; ?>>
                            <?php echo htmlspecialchars($szotar['megnevezes'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <fieldset>

                    <div class="mb-3">
                        <label for="tipus" class="form-label">Kikérdezés típusa:</label>
                        <select id="tipus" name="tipus" class="form-select" required>
                            <option value="1" selected>Gyakorlás</option>
                            <option value="2">Vizsga</option>
                        </select>
                    </div>
                </fieldset>
                <fieldset>
                    <label class="form-label">Szavak száma:</label>
                    <label>
                        <input type="radio" name="szoszam_tipus" value="M" checked>Mindegyik</label>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="szoszam_tipus" value="N">Néhány</label>
                    </label>
                    <div id="szammezo">
                        <label for="szoszam">Adj meg egy számot:</label>
                        <input type="number" id="szoszam" name="szam" min="1" max="100">
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-primary">Kikérdezés indítása <span
                        class="ms-2">&rarr;</span></button>
        </form>

        <?php if (isset($kerdes) && $kerdes): ?>
            <!-- Modal -->
            <div class="modal fade" id="optionModal" tabindex="-1" aria-labelledby="optionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="optionModalLabel">Már folyamatban van egy feladat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                        </div>
                        <div class="modal-body">
                            <p>Folytatod a korábban elkezdett feladatot?</p>
                        </div>
                        <div class="modal-footer">
                            <!-- Gomb 1 -->
                            <a href="feladat.php" class="btn btn-success">Igen</a>
                            <!-- Gomb 2 -->
                            <a href="kikerdezes_inditas.php?korabbiLezaras=true" class="btn btn-info">Nem, újat kezdek</a>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <script>
            const radios = document.querySelectorAll('input[name="szoszam_tipus"]');
            const szammezo = document.getElementById('szammezo');

            radios.forEach(radio => {
                radio.addEventListener('change', () => {
                    if (radio.value === 'N' && radio.checked) {
                        szammezo.style.display = 'block';
                    } else if (radio.value === 'M' && radio.checked) {
                        szammezo.style.display = 'none';
                    }
                });
            });
        </script>
        <?php if (isset($kerdes) && $kerdes): ?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

            <script>
                // Automatikus megnyitás, ha a PHP változó engedélyezte
                window.onload = function () {
                    var myModal = new bootstrap.Modal(document.getElementById('optionModal'));
                    myModal.show();
                };
            </script>
        <?php endif; ?>

</body>

</html>