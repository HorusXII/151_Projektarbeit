<?php
// Session starten
session_start();
// Session leeren
$_SESSION = array();
session_destroy();
// Weiterleiten auf login.php
header('Location: login.php');
?>