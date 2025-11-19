<?php
require_once __DIR__.'/../inc/config.php';
require_once  __DIR__.'/../model/User.php';


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

function hasSzotar($userId, $szotarNev) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM szotar WHERE user_fk= ? AND megnevezes = ?");
    $stmt->bind_param("is", $userId, $szotarNev);
    
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['count'] > 0;
}

}

function createSzotar($userId, $szotarNev) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO szotar (user_fk, megnevezes) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $szotarNev);
    
    return $stmt->execute();
}


