<?php

// Sessionhandling starten
session_start();
// Datenbankverbindung
include('include/dbconnector.inc.php');

$error = '';
$message = '';


if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
    header('Location: overview.php');
}

// Initialisierung

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['oldpassword'])) {
        //trim
        $oldpassword = trim($_POST['oldpassword']);
        // passwort gültig?
        if (empty($oldpassword) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $oldpassword)) {
            $error .= "Das Passwort entspricht nicht dem geforderten Format.<br />";
        }
    } else {
        $error .= "Geben Sie bitte das alte Passwort an.<br />";
    }
    if (isset($_POST['newpassword'])) {
        //trim
        $newpassword = trim($_POST['newpassword']);
        // passwort gültig?
        if (empty($newpassword) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $newpassword)) {
            $error .= "Das neue Passwort entspricht nicht dem geforderten Format.<br />";
        }
    } else {
        $error .= "Geben Sie bitte ein neues Passwort an.<br />";
    }
    if (isset($_POST['repeatnewpasswort'])) {
        //trim
        $repeatnewpasswort = trim($_POST['repeatnewpasswort']);
        // passwort gültig?
        if (empty($repeatnewpasswort) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $repeatnewpasswort)) {
            $error .= "Das wiederholte, neue Passwort entspricht nicht dem geforderten Format.<br />";
        }
    } else {
        $error .= "Bitte wiederhole das neue Passwort<br />";
    }

    // kein Fehler
    if (empty($error)) {
        // Query erstellen
        $query = "SELECT password from users where id = ?";

        // Query vorbereiten
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $error .= 'prepare() failed ' . $mysqli->error . '<br />';
        }
        // Parameter an Query binden
        if (!$stmt->bind_param("i", $_SESSION['userid'])) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        // Query ausführen
        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        }
        // Daten auslesen
        $result = $stmt->get_result();

        // Userdaten lesen
        if ($row = $result->fetch_assoc()) {

            // Passwort ok?
            if (password_verify($oldpassword, $row['password'])) {
                if (strcmp($newpassword, $repeatnewpasswort) !== 0) {
                    $error .= "Passwort stimmt nicht überein";
                }
            } else {
                $error .= "Falsches Passwort";
            }
        } else {
            $error .= "Benutzername oder Passwort sind falsch";
        }
    }




    if (empty($error)) {
        $query = "UPDATE users SET password = ? WHERE id = ? ";
        $newpassword_hash = password_hash($newpassword, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare($query);

        if ($stmt === false) {
            $error .= 'prepare() failed ' . $mysqli->error . '<br />';
        }

        $stmt->bind_param("si", $newpassword_hash, $_SESSION['userid']);

        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        } else {
            $message .= 'Datensatz erfolgreich geändert.';
        }
        // kein Fehler!
        if (empty($error)) {
            $message .= "Die Daten wurden erfolgreich in die Datenbank geschrieben<br/ >";
            // Felder leeren und Weiterleitung auf anderes Script: z.B. Login!
            $oldpassword = $newpassword = $repeatnewpasswort = '';
            // Verbindung schliessen
            $mysqli->close();
            // Weiterleiten auf login.php
            header('Location: /151_projektarbeit/admin.php');
            // beenden des Scriptes
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passwort ändern</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/aa92474866.js" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand">SAPv2</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
                    echo '<li class="nav-item"><a class="nav-link" href="register.php">Registrierung</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                } else {
                    if ($_SESSION['admin'] == 1) {
                        echo '<li class="nav-item"><a class="nav-link" href="register.php">Neuer Benutzer</a></li>';
                    }
                    echo '<li class="nav-item"><a class="nav-link" href="admin.php">Benutzerliste</a></li>';
                    echo '</ul></div>';
                    echo '<div class="dropdown ms-auto">';
                    echo '<button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown" style="padding: 0px">';
                    echo '<img src="https://www.innovaxn.eu/wp-content/uploads/blank-profile-picture-973460_1280.png" alt="Profilepicture placeholder" class="img-responsive img-rounded " style="max-height: 40px; max-width: 40px;">';
                    echo '<span class="caret"></span>';
                    echo '</button>';
                    echo '<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="menu1">';
                    if (!$_SESSION['admin'] == 1) {
                        echo '<li class="nav-item"><a class="dropdown-item" href="password.php">Passwort ändern</a></li>';
                    } else {
                        echo '<li class="nav-item"><a class="dropdown-item" href="overview.php">Meine Benutzer</a></li>';
                        echo '<li class="nav-item"><a class="dropdown-item" href="password.php">Passwort ändern</a></li>';
                    }
                    echo '<li class="nav-item"><a class="dropdown-item" href="logout.php">Logout</a></li>';
                }
                ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1>Ändern Sie ihr Passwort:</h1>
        <?php
        $id = $_SESSION["userid"];
        $query = "SELECT password FROM `users` WHERE id=" . $_SESSION['userid'];
        $result = $mysqli->query($query);
        $User = $result->fetch_assoc();

        ?>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)) {
            echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
        ?>
        <form method="post">
            <!-- password -->
            <div class="form-group">
                <label for="oldpassword">Altes Passwort: *</label>
                <input type="password" name="oldpassword" class="form-control" id="oldpassword" placeholder="Altes Passwort" pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 8 Zeichen lang,keine Umlaute." maxlength="255" required>
            </div>
            <!-- password -->
            <div class="form-group">
                <label for="newpassword">Neues Passwort: *</label>
                <input type="password" name="newpassword" class="form-control" id="newpassword" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, max. 30 Zeichen, keine Umlaute" pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 8 Zeichen lang,keine Umlaute." maxlength="255" required>
            </div>
            <!-- password -->
            <div class="form-group">
                <label for="repeatnewpasswort">Wiederholen sie das neue Passwort: *</label>
                <input type="password" name="repeatnewpasswort" class="form-control" id="repeatnewpasswort" placeholder="Bitte wiederholen sie das vorher angegebene Passwort" pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 8 Zeichen lang,keine Umlaute." maxlength="255" required>
            </div>
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <button type="submit" name="submit" value="submit" class="btn btn-info">Ändern</button>
            <input type="button" value="Zurück" onclick="history.back() " class="btn btn-warning">
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>