<?php
// TODO - Session starten
session_start();
// TODO - Session leeren
$_SESSION = array();
session_destroy();
// TODO - Weiterleiten auf login.php
header('Location: /151_projektarbeit/login.php');
?>