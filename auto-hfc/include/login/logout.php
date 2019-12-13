<?php
define('PROJECT_DIR', '/auto-hfc/');
session_start();
unset($_SESSION['userId']);
unset($_SESSION['emailId']);
session_destroy();
$response['target'] = PROJECT_DIR;
echo json_encode($response);
?>
