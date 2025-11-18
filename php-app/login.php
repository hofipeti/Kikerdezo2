<?php
session_start();
include 'config.php'; // mysqli kapcsolat

$username = $_POST['username'];
$password = $_POST['password'];

// Lekérdezés prepared statementtel
$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Ellenőrzés password_verify-vel
    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: index.php");
        exit;
    } else {
        echo "Hibás jelszó!";
    }
} else {
    echo "Nincs ilyen felhasználó!";
}
