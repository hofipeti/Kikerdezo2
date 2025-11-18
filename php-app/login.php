<?php
session_start();
include_once 'inc/config.php'; // mysqli kapcsolat
include_once 'inc/functions.php'; // segédfüggvények
$username = $_POST['username'];
$password = $_POST['password'];

// Lekérdezés prepared statementtel

$user = getUserByLogin($username, $password);


if ($user !== null) {
    // Sikeres bejelentkezés
    $_SESSION['user'] = $user;
    header("Location: index.php");
    exit;
} else {
    // Hibás bejelentkezés
    $_SESSION['error'] = "Hibás felhasználónév vagy jelszó!";
    header("Location: login.html");
    exit;
}