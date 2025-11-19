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

    <div class="mt-4">
    <?php
    require_once __DIR__.'/inc/config.php';
    require_once __DIR__.'/inc/functions.php';

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
              <div class="text-muted small">ID: <?php echo (int)$szotar['szotar_id']; ?></div>
            </div>
            <div class="btn-group" role="group" aria-label="Actions">
              <a href="szotar.php?id=<?php echo (int)$szotar['szotar_id']; ?>" class="btn btn-sm btn-outline-primary">Szavak szerkesztése</a>
              <form action="szavak_szerkesztese.php" method="post" style="display:inline;margin:0;">
                <input type="hidden" name="id" value="<?php echo (int)$szotar['szotar_id']; ?>">
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Biztosan törlöd a szótárat?');">Törlés</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    </div>
</body>
</html>
