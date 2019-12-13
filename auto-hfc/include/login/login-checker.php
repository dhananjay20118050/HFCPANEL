<?php
require_once '../import.php';
if(isset($_SESSION['userId'])){
	$responce['emailId'] = $_SESSION['emailId'];
	$responce['userId'] = $_SESSION['userId'];
	$responce['fullName'] = $_SESSION['fullName'];
	$responce['status'] = 'FINE';
}else{
	$responce['status'] = 'ERR';
}
echo json_encode($responce);
?>