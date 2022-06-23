<?php

// Sessionhandling starten
session_start();

// Datenbankverbindung
include('include/dbconnector.inc.php');

$error = '';
$message = '';
$username = $password = '';


// Formular wurde gesendet und Besucher ist noch nicht angemeldet.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// username
	if (isset($_POST['username'])) {
		//trim
		$username = trim($_POST['username']);

		// Prüfung username
		if (empty($username) || !preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,30}/", $username)) {
			$error .= "Der Benutzername entspricht nicht dem geforderten Format.<br />";
		}
	} else {
		$error .= "Geben Sie bitte den Benutzername an.<br />";
	}
	// password
	if (isset($_POST['password'])) {
		//trim
		$password = trim($_POST['password']);
		// passwort gültig?
		if (empty($password) || !preg_match("/(?=^.{8,255}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
			$error .= "Das Passwort entspricht nicht dem geforderten Format.<br />";
		}
	} else {
		$error .= "Geben Sie bitte das Passwort an.<br />";
	}

	// kein Fehler
	if (empty($error)) {
		// Query erstellen
		$query = "SELECT id, username, password, admin from users where username = ?";

		// Query vorbereiten
		$stmt = $mysqli->prepare($query);
		if ($stmt === false) {
			$error .= 'prepare() failed ' . $mysqli->error . '<br />';
		}
		// Parameter an Query binden
		if (!$stmt->bind_param("s", $username)) {
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
			if (password_verify($password, $row['password'])) {

				// Session personifizieren
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = $row['username'];
				$_SESSION['userid'] = $row['id'];
				$_SESSION['admin'] = $row['admin'];

				// Session ID regenerieren
				session_regenerate_id(true);

				// weiterleiten auf admin.php
				header("location: /151_projektarbeit/admin.php");
				// Script beenden
				exit();
			} else {
				$error .= "Benutzername oder Passwort sind falsch";
			}
		} else {
			$error .= "Benutzername oder Passwort sind falsch";
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
	<title>Login</title>

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
					header('Location: admin.php');
				}
				?>
			</ul>
		</div>
	</nav>
	<div class="container">
		<h1>Login</h1>
		<p>
			Bitte melden Sie sich mit Benutzernamen und Passwort an.
		</p>
		<?php
		// fehlermeldung oder nachricht ausgeben
		if (!empty($message)) {
			echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
		} else if (!empty($error)) {
			echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
		}



		?>
		<form method="POST">
			<div class="form-group">
				<label for="username">Benutzername *</label>
				<input type="text" name="username" class="form-control" id="username" value="" placeholder="Gross- und Keinbuchstaben, min 6 Zeichen." pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}" title="Gross- und Keinbuchstaben, min 6 Zeichen." maxlength="30" required>
			</div>
			<!-- password -->
			<div class="form-group">
				<label for="password">Password *</label>
				<input type="password" name="password" class="form-control" id="password" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute" pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 8 Zeichen lang,keine Umlaute." maxlength="255" required>
			</div>
			<button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
			<input type="button" value="Zurück" onclick="history.back() " class="btn btn-warning">
		</form>
	</div>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>