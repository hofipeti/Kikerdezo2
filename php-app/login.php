<?php
session_start();
include_once 'inc/config.php'; // mysqli kapcsolat

$username = $_POST['username'];
$password = $_POST['password'];

// Lekérdezés prepared statementtel
$stmt = $conn->prepare("SELECT * FROM user WHERE login = ? AND (password = SHA2(?, 256))");
$stmt->bind_param("ss", $username, $password);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Sikeres bejelentkezés
    $row = $result->fetch_assoc();
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['login'] = $row['login'];
    $_SESSION['nev'] = $row['nev'];
    header("Location: index.php");
    exit;
} else {
    // Hibás bejelentkezés
    $_SESSION['error'] = "Hibás felhasználónév vagy jelszó!";
    header("Location: login.html");
    exit;
}