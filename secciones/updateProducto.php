<?php include("../templates/header.php") ?>
<?php

$id = $_POST['id'];
$cantidadProducto = $_POST['cantidadProducto'];
$sql = "UPDATE carrito SET cantidad = ? WHERE id = ?";
$sentencia = $conexion->prepare($sql);
$params = array(
    $cantidadProducto, 
    $id  
);
$sentencia->execute($params);

?>
