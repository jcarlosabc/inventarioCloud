<?php

$DB_SERVER= "localhost";
$DB_NAME = "id21553041_ventas";
$DB_USER = "id21553041_ventas_admin";
$DB_PASS = "ventas_Admin1";

try {
    $conexion = new PDO("mysql:host=$DB_SERVER;dbname=$DB_NAME", $DB_USER, $DB_PASS);
} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>