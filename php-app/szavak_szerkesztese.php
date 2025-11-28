<?php
// szavak_szerkesztese.php
// Kiolvassa a szotar_id-t az URL-ből, lekéri a szótárat getSzotarByID-vel, hibajelzést mutat ha kell.
// Szükséges include-okat igazítsd a projekted struktúrájához.

session_start();

$error = null;
$szotar = null;

// GET param
$szotar_id = isset($_GET['szotar_id']) ? intval($_GET['szotar_id']) : null;
if (!$szotar_id) {
    $error = "Hiányzó vagy érvénytelen szotar_id a kérésben.";
} else {
    // Próbáld meg a megfelelő fájlokat betölteni — módosítsd az útvonalat, ha máshol vannak.
    $inc1 = __DIR__ . '/inc/config.php';
    $inc2 = __DIR__ . '/inc/functions.php';
    if (file_exists($inc1))
        include_once $inc1;
    if (file_exists($inc2))
        include_once $inc2;

    $szotar = getSzotarByID($szotar_id);

}
// Handle POST submission early, before sending any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['szo1'], $_POST['szo2'])) {
    $szo1_val = trim($_POST['szo1']);
    $szo2_val = trim($_POST['szo2']);

    if (!isset($szo1_val) || !isset($szo2_val)) {
        $_SESSION['error'] = 'Mindkét szó megadása kötelező.';
        header('Location: szavak_szerkesztese.php?szotar_id=' . (int) $szotar_id);
        exit;
    } else
        if ($szo1_val !== '' && $szo2_val !== '') {

            if (isset($_SESSION["szo_id"])) {
                $szo_id = base64_decode($_SESSION["szo_id"]);
                $res = updateSzavak($szo_id, $szotar_id, $szo1_val, $szo2_val);
                unset($_SESSION["szo_id"]);
                if ($res === false) {
                    // updateSzavak sets $_SESSION['error'] on failure
                } else {
                    $_SESSION['info'] = 'Szavak sikeresen frissítve.';
                    header('Location: szavak_szerkesztese.php?szotar_id=' . (int) $szotar_id);
                    exit;
                }
                return;
            } else {

                $res = createSzavak($szotar_id, $szo1_val, $szo2_val);
                if ($res === false) {
                    // createSzavak sets $_SESSION['error'] on failure
                } else {
                    $_SESSION['info'] = 'Szavak sikeresen mentve.';
                    header('Location: szavak_szerkesztese.php?szotar_id=' . (int) $szotar_id);
                    exit;
                }

            }
        } else {
            $_SESSION['error'] = 'Mindkét szó megadása kötelező.';
        }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['szo_id'])) {
    $_SESSION["szo_id"] = $_GET['szo_id'];
    $szoList = getSzo(base64_decode(urldecode($_GET['szo_id'])));

}

?>
<!doctype html>
<html lang="hu">

<head>
    <meta charset="utf-8">
    <title>Szavak szerkesztése</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="szavak_szerkesztese.css" />
</head>

<body>
    <main>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8');
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <h1>
            <?php
            // Szótár neve (ha van)
            if ($szotar && is_array($szotar)) {
                // ha a getSzotarByID asszociatív tömböt ad vissza és 'nev' a mező neve
                echo htmlspecialchars($szotar['megnevezes'] ?? 'Ismeretlen szótár');
            } elseif ($szotar && is_object($szotar)) {
                // ha objektumot ad vissza
                // Ha osztálynév namespace miatt nincs betöltve, ott is hibát jelezünk korábban.
                echo htmlspecialchars($szotar->megnevezes ?? 'Ismeretlen szótár');
            } else {
                echo 'Szótár';
            }
            ?>
        </h1>
        <a href="index.php" class="btn btn-secondary mb-3">&larr; Vissza a főoldalra</a>
        <!-- Két egymás melletti egysoros mező, felettük címke, mellettük gomb -->
        <form method="post" action="szavak_szerkesztese.php?szotar_id=<?php echo (int) $szotar_id; ?>">
            <div class="row">
                <div class="col">
                    <label
                        for="szo1"><?php echo htmlspecialchars($szotar['nyelv1'] ?? 'Nyelv 1', ENT_QUOTES, 'UTF-8'); ?></label>
                    <div class="input-with-btn">
                        <input autofocus type="text" id="szo1" name="szo1" class="form-control" value="<?php
                        if (isset($szoList)) {


                            // Szűrés array_filter-rel, külső paraméter használatával
                        
                            $eredmeny = array_filter($szoList, function ($obj) use ($szotar) {

                                return $obj["nyelv_fk"] == $szotar['nyelv1_id'];
                            });

                            echo htmlspecialchars(array_values($eredmeny)[0]["szo"] ?? '', ENT_QUOTES, 'UTF-8');
                        }
                        ?>">
                    </div>
                </div>

                <div class="col">
                    <label
                        for="szo2"><?php echo htmlspecialchars($szotar['nyelv2'] ?? 'Nyelv 2', ENT_QUOTES, 'UTF-8'); ?></label>
                    <div class="input-with-btn">
                        <input type="text" id="szo2" name="szo2" class="form-control" value="<?php
                        if (isset($szoList)) {


                            // Szűrés array_filter-rel, külső paraméter használatával
                        
                            $eredmeny = array_filter($szoList, function ($obj) use ($szotar) {

                                return $obj["nyelv_fk"] == $szotar['nyelv2_id'];
                            });

                            echo htmlspecialchars(array_values($eredmeny)[0]["szo"] ?? '', ENT_QUOTES, 'UTF-8');
                        }
                        ?>">
                        <button type="submit" class="btn btn-primary">Mentés</button>
                    </div>
                </div>

            </div>
        </form>

        <?php
        // Fetch words for this szotar and display them in a two-column table
        $szavak = [];
        if (function_exists('getSzavakBySzotar')) {
            $szavak = getSzavakBySzotar($szotar_id);

        }

        ?>

        <h2 class="mt-4">Szavak</h2>
        <?php if (empty($szavak)): ?>
            <div class="alert alert-secondary">Nincsenek szavak a szótárban.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo htmlspecialchars($szotar['nyelv1'] ?? 'Nyelv 1', ENT_QUOTES, 'UTF-8'); ?></th>
                            <th><?php echo htmlspecialchars($szotar['nyelv2'] ?? 'Nyelv 2', ENT_QUOTES, 'UTF-8'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($szavak as $row):
                            // Heuristics to find left/right word values in returned row
                            $left = $row['szo1'] ?? $row['s1_szo'] ?? $row['s1'] ?? $row['szo_left'] ?? $row['szo'] ?? null;
                            $right = $row['szo2'] ?? $row['s2_szo'] ?? $row['s2'] ?? $row['szo_right'] ?? null;
                            // If both are null, try to detect two distinct columns by keys
                            if ($left === null && $right === null) {
                                // take first two string-like values
                                $vals = [];
                                foreach ($row as $v) {
                                    if (is_string($v) && trim($v) !== '')
                                        $vals[] = $v;
                                    if (count($vals) >= 2)
                                        break;
                                }
                                $left = $vals[0] ?? '';
                                $right = $vals[1] ?? '';
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($left ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($right ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="szavak_szerkesztese.php?szotar_id=<?php echo (int) $szotar_id; ?>&szo_id=<?php echo base64_encode($row['szo_fk']); ?>"
                                        class="btn btn-sm btn-warning">Szerkesztés</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Egyszerű példa gombokhoz — módosítsd a szükséges műveletre (pl. ajax vagy form submit)
        function onAction1() {
            const v = document.getElementById('mezo1').value;
            alert('Gomb 1: ' + v);
        }
        function onAction2() {
            const v = document.getElementById('mezo2').value;
            alert('Gomb 2: ' + v);
        }
    </script>
</body>

</html>