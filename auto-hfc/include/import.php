<?php
//for development
//ini_set('error_reporting', E_ALL);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');

//for production
//ini_set('error_reporting', E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

session_start();
require_once 'constants.php';
require_once 'functions.php';
require_once ROOTDIR.'/lib/Editor-Datatables/php/DataTables.php';
?>