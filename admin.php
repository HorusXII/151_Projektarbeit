<?php

session_start();
include('include/dbconnector.inc.php');
$error = $message = '';

if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
    header('Location:  login.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Benutzerübersicht</title>

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
        <h1>Benutzer:</h1>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else {
            $query = "SELECT * FROM `users`";
            $result = $mysqli->query($query);
        }
        ?>
        <table class="table">
            <tr>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>E-Mail</th>
            </tr>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['firstname']) . "</td>" . "<td>" . htmlspecialchars($row['lastname']) . "</td>" . "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "</tr>";
            }

            $result->free();
            ?>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>