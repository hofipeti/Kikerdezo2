<?php
require_once 'model/User.php';
require_once 'inc/functions.php';
session_start();
if (!isset($_SESSION['user']) || !$_SESSION['user'] instanceof User) {
    header("Location: login.html");
    exit;
}
$userId = $_SESSION['user']->UserId;
// Példa adatok (helyettük jöhetnek adatbázisból is)
$eredmenyek = getEredmenyek($userId);
foreach ($eredmenyek as &$sor) {

    $hours = floor($sor['diff_seconds'] / 3600);
    $minutes = floor(($sor['diff_seconds'] % 3600) / 60);
    $seconds = $sor['diff_seconds'] % 60;

    // formázás HH:MM:SS alakban
    $sor['diff_formatted'] = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}
unset($sor);
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>Eredmények</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container my-5">
        <h1 class="mb-4 text-center">Eredmények</h1>

        <a href="index.php" class="btn btn-primary mt-3">Vissza a kezdő oldalra</a>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-striped table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Kezdete</th>
                            <th>Hossza</th>
                            <th>Kérdések száma</th>
                            <th>Próbálkozások</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eredmenyek as $sor): ?>
                            <tr>
                                <td><?= htmlspecialchars($sor["start_at"]) ?></td>
                                <td><?= htmlspecialchars($sor["diff_formatted"]) ?></td>
                                <td><?= htmlspecialchars($sor["sSikeres"]) ?></td>
                                <td><?= htmlspecialchars($sor["sProbalkozas"]) ?></td>
                            </tr>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Gratulálok, elvégezted a feladatot!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bezárás"></button>
                </div>
                <div class="modal-body">
                    <p>Kérdések száma: <?php echo $eredmenyek[0]["sSikeres"] ?></p>
                    <p>Próbálkozások száma: <?php echo $eredmenyek[0]["sProbalkozas"] ?></p>
                    <p>Időtartam: <?php echo $eredmenyek[0]["diff_formatted"] ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                </div>
            </div>
        </div>
    </div>
    <canvas id="fireworksCanvas"></canvas>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onload = function () {
            // URL paraméterek beolvasása
            const params = new URLSearchParams(window.location.search);
            if (params.get("showlast") === "true") {
                var myModal = new bootstrap.Modal(document.getElementById('infoModal'));
                myModal.show();
            }
        };

const canvas = document.getElementById('fireworksCanvas');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];
let animationId;

function random(min, max) {
  return Math.random() * (max - min) + min;
}

function createFirework() {
  const x = random(100, canvas.width - 100);
  const y = random(100, canvas.height / 2);
  const count = 100; // több részecske
  for (let i = 0; i < count; i++) {
    particles.push({
      x: x,
      y: y,
      vx: random(-5, 5),
      vy: random(-5, 5),
      alpha: 1,
      size: random(2, 4),
      color: `hsl(${random(0,360)},100%,${random(40,70)}%)`
    });
  }
}

function animate() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particles.forEach((p, i) => {
    p.x += p.vx;
    p.y += p.vy;
    p.alpha -= 0.015;
    ctx.fillStyle = p.color;
    ctx.globalAlpha = p.alpha;
    ctx.beginPath();
    ctx.arc(p.x, p.y, p.size, 0, Math.PI*2);
    ctx.fill();
    if (p.alpha <= 0) particles.splice(i,1);
  });
  // gyakrabban indítunk új robbanást
  if (Math.random() < 0.1) createFirework();
  animationId = requestAnimationFrame(animate);
}

// Modal események
const modalEl = document.getElementById('infoModal');
modalEl.addEventListener('shown.bs.modal', () => {
  animate(); // indul a tűzijáték
});
modalEl.addEventListener('hidden.bs.modal', () => {
  cancelAnimationFrame(animationId); // leállítjuk
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particles = [];
});

      
       

    </script>

    <style>
        #fireworksCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            /* ne zavarja a kattintást */
            z-index: 1055;
            /* modal fölött legyen */
        }
    </style>
</body>

</html>