<?php

// TODO - mit eigener Datenbak verbinden
$host = 'localhost';
$username = 'DbUser';
$password = 'password1234';
$database = '151_projektarbeit';

// mit Datenbank verbinden
$mysqli = new mysqli($host, $username, $password, $database);

// Fehlermeldung, falls Verbindung fehl schlägt.
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}
