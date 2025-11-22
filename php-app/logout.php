<?php
require_once __DIR__ . '/inc/bootstrap.php';
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
