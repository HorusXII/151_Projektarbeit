<?php
include('include/dbconnector.inc.php');

$ckeckquery = "SELECT * FROM `users` WHERE username=?";
$stmt = $mysqli->prepare($ckeckquery);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
if($row == NULL){
    echo("Empty");
}else{
    Print_r($row);
}
?>
