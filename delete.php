
<?php
// Sessionhandling starten
session_start();
// Datenbankverbindung
include('include/dbconnector.inc.php');

if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin'] or !$_SESSION['admin'] == 1) {
    header('Location: overview.php');
}

// Initialisierung
if (isset($_GET['id']) and is_numeric($_GET['id'])) {

    $error = $message =  '';
    $id = intval($_GET["id"]);
    $userid = intval($_SESSION["userid"]);

    $query = "DELETE FROM users WHERE id=? and creator=?";
    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        $error .= 'prepare() failed ' . $mysqli->error . '<br />';
    }

    if (empty($error)) {
        $stmt->bind_param("ii", $id, $userid);
        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        } else {
            // Anzahl betroffener Zeilen, grösser als 0?
            if ($mysqli->affected_rows) {
                $message .= 'Datensatz erfolgreich gelöscht.<br>';
                header('Location: overview.php');
            } else {
                $error .= "Kein Datensatz in der Datenbank gefunden.<br>";
            }
        }
    } else {
        $error .= "Keine Parameter übergeben.<br>";
    }
}
echo $error;
?>