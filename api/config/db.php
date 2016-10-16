<?php
error_reporting(E_ALL | E_STRICT);
include dirname(__FILE__) . "/../NotORM.php";

//$connection = new PDO("mysql:dbname=robust", "ODBC");
$connection = new PDO("mysql:dbname=complaints;host=localhost","root","");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
$complaint = new NotORM($connection);
?>
