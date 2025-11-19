<?php

require __DIR__.'/inc/config.php';
require_once __DIR__.'/inc/functions.php';
require_once __DIR__.'/model/User.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the 'nev' parameter
    if (isset($_POST['nev']) && !empty(trim($_POST['nev']))) {
        $nev = trim($_POST['nev']);

        if (hasSzotar($_SESSION['user']->UserId, $nev)) {
            $_SESSION['error'] = "A szótár már létezik.";
            header("Location: uj_szotar.html");
            exit;
        }

        createSzotar($_SESSION['user']->UserId, $nev);
        $_SESSION['info'] = "A szótár sikeresen létrehozva: " . htmlspecialchars($nev, ENT_QUOTES, 'UTF-8');
        header("Location: index.php");
    } else {
        $_SESSION['error'] = "A név megadása kötelező.";
        header("Location: uj_szotar.html");
        exit;
    }
} else {
    $_SESSION['error'] = "Érvénytelen kérésmód.";
    header("Location: uj_szotar.html");
    exit;
}

$conn->close();
?>