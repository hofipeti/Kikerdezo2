<?php
// Adatbázis konfiguráció
$host     = "mysql";      // vagy pl. "127.0.0.1"
$username = "svc-kikerdezo"; // adatbázis felhasználónév
$password = "1DwQZx_ionUM([/*";      // adatbázis jelszó
$dbname   = "kikerdezo";  // adatbázis neve

// Kapcsolat létrehozása
$conn = mysqli_connect($host, $username, $password, $dbname);

?>