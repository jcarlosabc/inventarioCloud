<?php

$DB_SERVER= "localhost";
$DB_NAME = "vtechno1_piventas";
$DB_USER = "vtechno1_piventasdb";
$DB_PASS = "Tgn466WDq[S^";

try {
    $conexion = new PDO("mysql:host=$DB_SERVER;dbname=$DB_NAME", $DB_USER, $DB_PASS);
} catch (Exception $ex) {
    echo $ex->getMessage();
}

?>