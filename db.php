<?php

$DB_SERVER= "localhost";
$DB_NAME = "ventas";
$DB_USER = "root";
$DB_PASS = "";

try {
    $conexion = new PDO("mysql:host=$DB_SERVER;dbname=$DB_NAME", $DB_USER, $DB_PASS);
} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>