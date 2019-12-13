<?php if (!defined('DATATABLES')) exit(); 

/* Development */
$sql_details = array(
    "type" => "Mysql",   // Database type: "Mysql", "Postgres", "Sqlserver", "Sqlite" or "Oracle"
    "user" => "dbuser",        // Database user name
    "pass" => "vara123",        // Database password
    "host" => "localhost",        // Database host
    "port" => "",        // Database connection port (can be left empty for default)
    "db"   => "hfc",        // Database name
    "dsn"  => "",        // PHP DSN extra information. Set as `charset=utf8` if you are using MySQL
    "pdoAttr" => array() // PHP PDO attributes array. See the PHP documentation for all options
);