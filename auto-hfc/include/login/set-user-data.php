<?php

require_once('../import.php');

$_SESSION['userId'] = $_POST['userId'];
$_SESSION['emailId'] = $_POST['emailId'];
$_SESSION['fullName'] = $_POST['fullName'];
$responce['userId'] = $_POST['userId'];
$responce['fullName'] = $_POST['fullName'];
$responce['emailId'] = $_POST['emailId'];

echo json_encode($responce);


?>
