<?php include("../templates/header.php") ?>
<?php

$id = $_POST['id'];
$sentencia=$conexion->prepare("DELETE FROM carrito WHERE id=:id");
$sentencia->bindParam(":id",$id);
$sentencia->execute();    

?>
