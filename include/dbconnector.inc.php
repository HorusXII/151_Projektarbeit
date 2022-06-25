<?php


// mit Datenbank verbinden
$mysqli = new mysqli('localhost', 'DbUser', 'password1234', '151_projektarbeit');

// Fehlermeldung, falls Verbindung fehl schlägt.
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
