<?php
include_once  __DIR__.'/../model/User.php';
include_once __DIR__.'/../inc/config.php';

// Felhasználó lekérdezése bejelentkezés alapján
function getUserByLogin( $username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user WHERE login = ? AND (password = SHA2(?, 256))");
    $stmt->bind_param("ss", $username, $password);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return new User($row['user_id'], $row['login'], $row['nev']);
    }
    return null;
}

