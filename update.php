<?php
if (isset($_POST['asset_id']) and is_numeric($_POST['asset_id'])) {

// ID des Assets als Int speichern

$asset_id = intval($_POST['asset_id']);

} else {

$error .= "Keine ID übergeben.<br />";

}



// User ID als Int speichern

$user_id = intval($_SESSION('userid'));



// TODO - Serverseitige Prüfung der übrigen Angaben



// kein Fehler vorhanden?

if (empty($error)) {



// Query vorbereiten

$query = "UPDATE assets SET title = ?, description = ? WHERE id = ? and fk_id_users = ?";

$stmt = $mysqli->prepare($query);

if ($stmt === false) {

    $error .= 'prepare() failed ' . $mysqli->error . '<br />';

}



// Parameter an Query binden

if (!$stmt->bind_param("ssii", $title, $description, $asset_id, $user_id) {

    $error .= 'bind_param() failed ' . $mysqli->error . '<br />';

}



// Query ausführen

if (!$stmt->execute()) {

    $error .= 'execute() failed ' . $mysqli->error . '<br />';

} else {

    $message .= 'Datensatz erfolgreich geändert.';

}

}
?>