<?php

define('ROOTDIR', $_SERVER['DOCUMENT_ROOT'].'/auto-hfc');
define('PROJECT_DIR', '/auto-hfc/');

define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'hfc');

$conn = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_DATABASE);


?>
