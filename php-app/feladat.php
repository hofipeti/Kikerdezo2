<?php
require_once 'model/User.php';
require_once 'inc/functions.php';
session_start();
if (!isset($_SESSION['user']) || !$_SESSION['user'] instanceof User) {
    header("Location: login.html");
    exit;
}
$userId = $_SESSION['user']->UserId;
$hasAktivFeladat = hasAktivFeladat($userId);
if (!$hasAktivFeladat) {
    $_SESSION['error'] = "Nincs aktív feladatod. Kérlek, indíts egy új kikérdezést!";
    header("Location: kikerdezes_inditas.php");
    exit;
}
if (!isset($_SESSION["state"])) {
    $_SESSION["state"] = "kerdezes";
}
switch ($_SESSION["state"]) {
    case "kerdezes":
        $k = getKerdes($userId);
        if (empty($k)) {
            $vanMegKerdes = createKerdes($userId);
            if (!$vanMegKerdes) {
                header("Location: eredmeny.php?showlast=true");
                exit;
            }
        }
        $kerdes = empty($k) ? getKerdes($userId)["szo"] : $k["szo"];
        $_SESSION["state"] = "valasz";

        unset($_SESSION["valasz"]);
        break;
    case "valasz":
        // Válasz kiértékelve, új kérdés kérése
        // Válasz feldolgozása
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valasz = isset($_POST['valasz']) ? trim($_POST['valasz']) : '';



            if (empty($valasz)) {
                $_SESSION['error'] = "A válasz megadása kötelező.";
                header("Location: feladat.php");
                exit;
            }
            $_SESSION["kerdes"] = $_POST["kerdes"];
            $_SESSION["valasz"] = $valasz;
            $result = createValasz($userId, $valasz);

            if ($result["success"]) {
                $_SESSION["success"] = true;
                $_SESSION["helyes"] = $result['helyes'];
                $_SESSION["state"] = "eredmeny";
                header("Location: feladat.php");
                exit;
            } else {
                $_SESSION['error'] = "Hiba történt a válasz mentésekor.";
                header("Location: feladat.php");
                exit;
            }
        } else {

            $_SESSION['state'] = "kerdezes";
            header("Location: feladat.php");
            exit;
        }

        break;
    case "eredmeny":
        $kerdes = $_SESSION["kerdes"];
        $valasz = $_SESSION["valasz"];
        $helyes = $_SESSION["helyes"];
        $_SESSION['state'] = "kerdezes";

        //header("Location: feladat.php");
        // exit;
        break;
    default:
        die("Érvénytelen állapot: " . htmlspecialchars($_SESSION["state"], ENT_QUOTES, 'UTF-8'));

}




?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Középre rendezett panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .center-panel {
            width: 80vw;
            height: 80vh;
            max-width: 1200px;
            /* opcionális felső korlát */
        }

        .helyes {
            background-color: lightgreen;
        }

        .helytelen {
            background-color: #ffcccb;
        }

        .form-control {
            text-align: center;
        }

        .moving {
            position: absolute;
            width: 180px;
            height: 180px;
            font-size: 180px;
            /* ikon esetén */
            line-height: 1;
        }

        .moving img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow center-panel">

            <div class="card-body d-flex flex-column <?php if (isset($helyes)) {
                echo $helyes === 1 ? 'helyes' : 'helytelen';
            } ?>">
                <h1 class="h4 mb-4 text-center">Feladat megoldás</h1>
                <?php echo $_SESSION["state"]; ?>
                <form class="needs-validation mt-2" method="POST" action="feladat.php" novalidate>
                    <!-- Readonly mező -->
                    <div class="mb-3">
                        <label for="readonlyInput" class="form-label">Kérdés:</label>
                        <input type="text" id="readonlyInput" class="form-control" readonly name="kerdes"
                            value="<?php echo htmlspecialchars($kerdes ?? '', ENT_QUOTES, 'UTF-8'); ?>"></input>
                    </div>

                    <!-- Kötelező mező -->
                    <div class="mb-4">
                        <label for="valasz" class="form-label">Kötelező mező</label>
                        <input type="text" name="valasz" id="valasz" autocomplete="off" class="form-control" placeholder="Válasz..." <?php echo true || isset($valasz) ? "required" : "readonly" ?> autofocus
                            value="<?php echo htmlspecialchars($valasz ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                        <div class="invalid-feedback">Ide írd a választ!</div>
                    </div>

                    <div class="mt-auto d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $_SESSION["state"] === "valasz" ? 'Válasz' : 'Következő' ?>

                        </button>
                    </div>
                </form>
                <?php
                $ks = getKerdesekSzama($userId);
                $hv = getHatralevokSzama($userId);
                ?>
                <input id="volume" type="range" min="0" max="<?php echo $ks; ?>" value="<?php echo $ks - $hv; ?>"
                    step="1" readonly>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS (opcionális, a kliens oldali validációhoz hasznos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap kliens oldali validáció
        (() => {
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>

    <?php if (isset($helyes)): ?>
        <div id="movingObj" class="moving">
            <?php if ($helyes === 1): ?>
                <!-- helyes válasz esetén kép -->
                <img src="unicorn.png" alt="helyes válasz">
            <?php else: ?>
                <!-- helytelen válasz esetén ikon -->
                💩
            <?php endif; ?>
        </div>

        <script>
            const obj = document.getElementById('movingObj');
            const w = window.innerWidth;
            const h = window.innerHeight;

            // indulási oldal: bal vagy jobb
            const startSide = Math.random() < 0.5 ? "left" : "right";

            let x, y, vx, vy;

            // függőleges pattogás sebessége
            vy = (Math.random() * 4 + 2) * (Math.random() < 0.5 ? 1 : -1);

            // vízszintes sebesség: kb. 3 másodperc alatt érjen át
            const travelTime = 4000; // ms
            vx = w / travelTime * 16; // kb. képkockánkénti lépés (60fps ~16ms)
            if (startSide === "right") vx *= -1;

            // kezdőpozíció
            if (startSide === "left") {
                x = 0;
            } else {
                x = w - 180;
            }
            y = Math.random() * (h - 180);

            if (startSide === "left") {
                x = 0;
                obj.style.transform = "scaleX(-1)"; // tükrözés balról induláskor
            } else {
                x = w - 180;
                obj.style.transform = "scaleX(1)";
            }

            function animate() {
              x += vx;
  y += vy;

  // függőleges pattogás
  if (y <= 0 || y + 180 >= h) {
    vy *= -1;
  }

  // vízszintes pattogás + tükrözés
  if (x <= 0 || x + 180 >= w) {
    vx *= -1;
    if (vx > 0) {
      obj.style.transform = "scaleX(-1)";
    } else {
      obj.style.transform = "scaleX(1)";
    }
  }

  obj.style.left = x + "px";
  obj.style.top = y + "px";

  requestAnimationFrame(animate);
            }
            animate();
        </script>
    <?php endif; ?>


</body>

</html>