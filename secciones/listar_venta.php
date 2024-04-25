<?php
include("../db.php");
if(isset($_GET['link'])){ 
    $linkeo=(isset($_GET['link']))?$_GET['link']:"";
}
// Inicializa el array para almacenar los resultados
$respuesta = array();
try {
    $resultado = $conexion->prepare("SELECT * FROM cliente ");
    $resultado->execute();
    $lista_cliente = $resultado->fetchAll(PDO::FETCH_ASSOC);

    $respuesta['success'] = true;
    $respuesta['data'] = $lista_cliente;
} catch (PDOException $e) {
    $respuesta['success'] = false;
    $respuesta['message'] = "Error al ejecutar la consulta: " . $e->getMessage();
}
header('Content-Type: application/json');
echo json_encode($respuesta);
?>
