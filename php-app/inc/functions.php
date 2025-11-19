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

function createSzotar($userId, $szotarNev, $nyelvId1, $nyelvId2) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO szotar (user_fk, megnevezes, nyelv1_fk, nyelv2_fk) VALUES (?, ?, ?, ? )");
    $stmt->bind_param("isii", $userId, $szotarNev, $nyelvId1, $nyelvId2);
    
    return $stmt->execute();
}

function getSzotarByUser($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM szotar WHERE user_fk = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $szotarok = [];
    while ($row = $result->fetch_assoc()) {
        $szotarok[] = $row;
    }
    return $szotarok;
}

function getNyelvek() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM nyelv ORDER BY megnevezes ASC");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $nyelvek = [];
    while ($row = $result->fetch_assoc()) {
        $nyelvek[] = $row;
    }
    return $nyelvek;
}