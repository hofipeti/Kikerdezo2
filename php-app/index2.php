<?php
include_once "inc/config.php";

if (!$conn) {
    die("Kapcsolódási hiba: " . mysqli_connect_error());
}
echo "ok";
?>