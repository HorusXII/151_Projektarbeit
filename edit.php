<?php

// Sessionhandling starten
session_start();
// Datenbankverbindung
include('include/dbconnector.inc.php');
if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin'] or !isset($_GET["id"]) or !is_numeric($_GET["id"])) {
  header('Location: overview.php');
}

// Initialisierung

if (isset($_POST['id']) and is_numeric($_POST['id'])) {
  $error = $message =  '';
  $id = intval($_POST["id"]);
  $userid = intval($_SESSION["userid"]);

  // Vorname ausgefüllt?
  if (isset($_POST['firstname'])) {
    //trim and sanitize
    $firstname = trim($_POST['firstname']);

    //mindestens 1 Zeichen und maximal 30 Zeichen lang
    if (empty($firstname) || strlen($firstname) > 30) {
      $error .= "Geben Sie bitte einen korrekten Vornamen ein.<br />";
    }
  } else {
    $error .= "Geben Sie bitte einen Vornamen ein.<br />";
  }

  // Nachname ausgefüllt?
  if (isset($_POST['lastname'])) {
    //trim and sanitize
    $lastname = trim($_POST['lastname']);

    //mindestens 1 Zeichen und maximal 30 Zeichen lang
    if (empty($lastname) || strlen($lastname) > 30) {
      $error .= "Geben Sie bitte einen korrekten Nachname ein.<br />";
    }
  } else {
    $error .= "Geben Sie bitte einen Nachname ein.<br />";
  }

  // Email ausgefüllt?
  if (isset($_POST['email'])) {
    //trim an sanitize
    $email = trim($_POST['email']);

    //mindestens 1 Zeichen und maximal 100 Zeichen lang, gültige Emailadresse
    if (empty($email) || strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
      $error .= "Geben Sie bitte eine korrekten Emailadresse ein.<br />";
    }
  } else {
    $error .= "Geben Sie bitte eine Emailadresse ein.<br />";
  }

  // Username ausgefüllt?
  if (isset($_POST['username'])) {
    //trim and sanitize
    $username = trim($_POST['username']);

    //mindestens 1 Zeichen , entsprich RegEX
    if (empty($username) || !preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,30}/", $username)) {
      $error .= "Geben Sie bitte einen korrekten Usernamen ein." . htmlspecialchars($username) . "<br />";
    }
  } else {
    $error .= "Geben Sie bitte einen Username ein.<br />";
  }
  //Check if username already exists.
  $ckeckquery = "SELECT * FROM `users` WHERE username=?";
  $stmt = $mysqli->prepare($ckeckquery);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  // check if returned username is not the edited user
  if ($row != NULL and $row['id'] == $_POST['id']) {
  } elseif ($row != NULL) {
    $error .= "Benutzername bereits vorhanden, bitte wählen sie einen anderen.";
  }


  if (empty($error)) {
    $query = "UPDATE users SET firstname = ?, lastname = ?,username = ? WHERE id = ? and creator = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt === false) {
      $error .= 'prepare() failed ' . $mysqli->error . '<br />';
    }

    $stmt->bind_param("sssii", $firstname, $lastname, $username, $id, $userid);

    if (!$stmt->execute()) {
      $error .= 'execute() failed ' . $mysqli->error . '<br />';
    } else {
      $message .= 'Datensatz erfolgreich geändert.';
    }
    // kein Fehler!

    if (empty($error)) {
      $message .= "Die Daten wurden erfolgreich in die Datenbank geschrieben<br/ >";
      // Felder leeren und Weiterleitung auf anderes Script: z.B. Login!
      $username = $firstname = $lastname = $email =  '';
      // Verbindung schliessen
      $mysqli->close();
      // Weiterleiten auf login.php
      header('Location: /151_projektarbeit/login.php');
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
  <title>Bearbeiten</title>

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
          echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
        } if (!$_SESSION['admin'] == 1) {
          header('Location: admin.php');
        } else {
          echo '<li class="nav-item"><a class="nav-link" href="register.php">Neuer Benutzer</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="admin.php">Benutzerliste</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="overview.php">Meine Benutzer</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="password.php">Passwort ändern</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="./logout.php">Logout</a></li>';
        }
        ?>
      </ul>
    </div>
  </nav>
  <div class="container">
    <?php
    $id = htmlspecialchars($_GET["id"]);
    $query = "SELECT * FROM `users` WHERE id=" . $id . " AND creator=" . $_SESSION['userid'];
    $result = $mysqli->query($query);
    $User = $result->fetch_assoc();
    if (!isset($User['username'])) {
      header('Location: /151_projektarbeit/overview.php');
    }


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
      <!-- vorname -->
      <div class="form-group">
        <label for="firstname">Vorname *</label>
        <input type="text" name="firstname" class="form-control" id="firstname" value="<?php echo htmlspecialchars($User['firstname']) ?>" maxlength="30" required>
      </div>
      <!-- nachname -->
      <div class="form-group">
        <label for="lastname">Nachname *</label>
        <input type="text" name="lastname" class="form-control" id="lastname" value="<?php echo htmlspecialchars($User['lastname']) ?>" maxlength="30" required>
      </div>
      <!-- email -->
      <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars($User['email']) ?>" maxlength="100" required>
      </div>
      <!-- benutzername -->
      <div class="form-group">
        <label for="username">Benutzername *</label>
        <input type="text" name="username" class="form-control" id="username" value="<?php echo htmlspecialchars($User['username']) ?>" pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}" title="Gross- und Keinbuchstaben, min 6 Zeichen." maxlength="30" required>
      </div>
      <!-- Send / Reset -->
      <button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
      <input type="button" value="Zurück" onclick="history.back() " class="btn btn-warning">
      <!-- Hidden ID -->
      <input type="hidden" name="id" value="<?php echo $id ?>">
    </form>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>