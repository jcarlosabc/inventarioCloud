<?php include("../templates/header.php") ?>
<?php

$id = $_POST['id'];
$cantidadProducto = $_POST['cantidadProducto'];
$cantidadPrecioMenor = $_POST['cantidadPrecioMenor'];
$cantidadPrecioMenor = str_replace(array('$','.', ','), '', $cantidadPrecioMenor);
$cantidadPrecioMayor = $_POST['cantidadPrecioMayor'];
$cantidadPrecioMayor = str_replace(array('$','.', ','), '', $cantidadPrecioMayor);
$cantidadProducto = $_POST['cantidadProducto'];
$sql = "UPDATE carrito SET cantidad = ?, precio = ?, precio_venta_mayor = ? WHERE id = ?";
$sentencia = $conexion->prepare($sql);
$params = array(
    $cantidadProducto,
    $cantidadPrecioMenor,
    $cantidadPrecioMayor,
    $id  
);
$sentencia->execute($params);

?>
