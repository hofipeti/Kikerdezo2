<?php
require_once 'model/User.php';
session_start();

require_once 'inc/functions.php';



if (!isset($_SESSION['user'])) {
  header("Location: login.php");
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
      <?php echo htmlspecialchars($_SESSION['info'], ENT_QUOTES, 'UTF-8');
      unset($_SESSION['info']); ?>
    </div>
  <?php endif; ?>
  <div>
    <?php
    $utolso = getUtolsoFeladat($_SESSION['user']->UserId);
    if (!empty($utolso)) {
     

      // Átalakítjuk DateTime objektummá
      $date = new DateTime($utolso["end_at"]);
      $now = new DateTime();

      // Különbség kiszámítása
      $diff = $now->getTimestamp() - $date->getTimestamp();
      echo "Örülök, hogy újra látlak! Utoljára <b>";
      
      if ($diff < 3600) {
        // kevesebb mint 1 óra → percekben
        $minutes = floor($diff / 60);
        echo $minutes . " perce";
      } elseif ($diff < 86400) {
        // kevesebb mint 1 nap → órákban
        $hours = floor($diff / 3600);
        echo $hours . " órája";
      } else {
        // több mint 1 nap → napokban
        $days = floor($diff / 86400);
        echo $days . " napja";
      }
      echo " </b>végeztél feladatot.";
    }
    ?>
  </div>
  <a href="uj_szotar.php" class="btn btn-primary mt-3">Új szótár hozzáadása</a>
  <a href="eredmeny.php" class="btn btn-primary mt-3">Eredmények</a>
  <a href="logout.php" class="btn btn-danger mt-3">Kijelentkezés</a>

  <div class="mt-4">
    <h2>Szótáraid:</h2>
    <?php
    require_once __DIR__ . '/inc/config.php';
    require_once __DIR__ . '/inc/functions.php';

    $szotarok = getSzotarByUser($_SESSION['user']->UserId);
    if (count($szotarok) === 0):
      ?>
      <div class="alert alert-secondary">Nincsenek szótáraid.</div>
    <?php else: ?>
      <div class="list-group">
        
        <?php foreach ($szotarok as $szotar): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?php echo htmlspecialchars($szotar['megnevezes'], ENT_QUOTES, 'UTF-8'); ?></strong>
              <div class="text-muted small">ID: <?php echo (int) $szotar['szotar_id'];?> | Szavak száma: <?php echo (int) $szotar['szoszam']; ?></div>
            </div>
            <div class="btn-group" role="group" aria-label="Actions">
              <a  href="szavak_szerkesztese.php?szotar_id=<?php echo (int) $szotar['szotar_id']; ?>"
                class="btn  btn-primary">Szavak szerkesztése</a>
              <a href="kikerdezes_inditas.php?szotar_id=<?php echo (int) $szotar['szotar_id']; ?>"
                class="btn btn-sm btn-outline-primary fall-button">Indítás <span class="ms-2">&rarr;</span></a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <canvas id="fireworksCanvas"></canvas>
  <style>
    .fall-button {
      display: inline-block;
      padding: 12px 24px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: transform 1s ease-in, opacity 1s ease-in;
      position: relative;
    }

    .fall-button.falling {
      transform: translateY(200vh) rotate(720deg);
      opacity: 0;
    }


    }
  </style>
  <script>
    const buttons = document.querySelectorAll('.fall-button');

    buttons.forEach(button => {
      button.addEventListener('click', (event) => {
        event.preventDefault(); // ne ugorjon azonnal a linkre

        // animáció indítása
        button.classList.add('falling');

        // szülő <a> href attribútumának lekérése
        const parentLink = button.getAttribute('href');

        // várunk az animáció végéig, majd átirányítunk
        setTimeout(() => {
          window.location.href = parentLink;
        }, 1200);
      });
    });



  </script>
</body>

</html>