<?php

// TODO - Sessionhandling starten
session_start();
// Datenbankverbindung
include('include/dbconnector.inc.php');
if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin'] or !isset($_GET["id"]) or !is_numeric($_GET["id"]))  {
    header('Location: /151_projektarbeit/overview.php');
}

// Initialisierung
$error = $message =  '';
$firstname = $lastname = $email = $username = $password =  '';


if (empty($error)) {
    $query = "UPDATE user SET firstname = ?, lastname = ?,username = ?, password = ?, WHERE id = ? and creator = ?";
    $stmt = $mysqli->prepare($query);
    
    $stmt->bind_param("ssssii", $firstname, $lastname, $username, $password, $_GET["id"], $_SESSION["userid"]);
    
    if (!$stmt->execute()) {
        $error .= 'execute() failed ' . $mysqli->error . '<br />';
    } else {
        $message .= 'Datensatz erfolgreich geändert.';
    }

//Check if username already exists.
$ckeckquery = "SELECT * FROM `users` WHERE username=?";
$stmt = $mysqli->prepare($ckeckquery);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($row != NULL){
    $error .= "Benutzername bereits vorhanden, bitte wählen sie einen anderen.";
}


  // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank
  if (empty($error)) {
    // Password haschen
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Query erstellen
    $query = "Insert into users (firstname, lastname, username, password, email, admin, creator) values (?,?,?,?,?,?,?)";
    
    // Query vorbereiten
    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
      $error .= 'prepare() failed ' . $mysqli->error . '<br />';
    }

    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']){
      $admin=1;
      $creator=NULL;
    } else {
      $admin=0;
      $creator = $_SESSION['userid'];
    }

    // Parameter an Query binden
    if (!$stmt->bind_param('sssssis', $firstname, $lastname, $username, $password_hash, $email, $admin, $creator)) {
      $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
    }

    // Query ausführen
    if (!$stmt->execute()) {
      $error .= 'execute() failed ' . $mysqli->error . '<br />';
    }

    // kein Fehler!
    
    if (empty($error)) {
      $message .= "Die Daten wurden erfolgreich in die Datenbank geschrieben<br/ >";
      // Felder leeren und Weiterleitung auf anderes Script: z.B. Login!
      $username = $password = $firstname = $lastname = $email =  '';
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
        if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']){
          echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
        } else {
          echo '<li class="nav-item"><a class="nav-link" href="register.php">Neuer Benutzer</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="admin.php">Benutzerliste</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="overview.php">Meine Benutzer</a></li>'; 
          echo '<li class="nav-item"><a class="nav-link" href="./logout.php">Logout</a></li>';
        }
        ?>
      </ul>
    </div>
  </nav>
  <div class="container">
    <?php
    $id = htmlspecialchars($_GET["id"]);
    $query = "SELECT * FROM `users` WHERE id=".$id;
    $result = $mysqli->query($query);
    $User = $result->fetch_assoc();
    $firstname = $User['firstname'];
    $lastname = $User['lastname'];
    $email = $User['email'];
    $username = $User['username'];

    ?>
    <?php
    // Ausgabe der Fehlermeldungen
    if (!empty($error)) {
      echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
    } else if (!empty($message)) {
      echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
    }
    
    ?>
    <form action="" method="post">
      <!-- vorname -->
      <div class="form-group">
        <label for="firstname">Vorname *</label>
        <input type="text" name="firstname" class="form-control" id="firstname" value="<?php echo $firstname ?>" maxlength="30" required="true">
      </div>
      <!-- nachname -->
      <div class="form-group">
        <label for="lastname">Nachname *</label>
        <input type="text" name="lastname" class="form-control" id="lastname" value="<?php echo $lastname ?>" maxlength="30" required="true">
      </div>
      <!-- email -->
      <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" name="email" class="form-control" id="email" value="<?php echo $email ?>" maxlength="100" required="true">
      </div>
      <!-- benutzername -->
      <div class="form-group">
        <label for="username">Benutzername *</label>
        <input type="text" name="username" class="form-control" id="username" value="<?php echo $username ?>" pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}" title="Gross- und Keinbuchstaben, min 6 Zeichen." maxlength="30" required="true">
      </div>
      <!-- password -->
      <div class="form-group">
        <label for="password">Neues passwort *</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute" pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 8 Zeichen lang,keine Umlaute." maxlength="255" required="true">
      </div>
      <!-- Send / Reset -->
      <button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
      <button type="reset" name="button" value="reset" class="btn btn-warning">Löschen</button>
      <!-- Hidden ID -->
      <input type="hidden" name="id" value="<?php echo $id ?>">
    </form>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>