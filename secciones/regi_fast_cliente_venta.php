<?php include("../templates/header.php") ?>
<?php

$cliente_nombre = $_POST['cliente_nombre'];
$cliente_apellido = $_POST['cliente_apellido'];
$cliente_ciudad = $_POST['cliente_ciudad'];
$cliente_direccion = $_POST['cliente_direccion'];
$cliente_telefono = $_POST['cliente_telefono'];
$cliente_email = $_POST['cliente_email'];
$cliente_empresa = $_POST['cliente_empresa'];
$cliente_nit = $_POST['cliente_nit'];
$link = $_POST['link'];
$responsable = $_SESSION['usuario_id'];
    
    $sentencia = $conexion->prepare("INSERT INTO cliente(
        cliente_id, cliente_nombre, cliente_apellido, cliente_ciudad,
        cliente_direccion, cliente_telefono, cliente_email,
        cliente_empresa, cliente_nit, link, responsable) 
        VALUES (NULL,:cliente_nombre,:cliente_apellido,:cliente_ciudad, 
        :cliente_direccion,:cliente_telefono,:cliente_email,:cliente_empresa, :cliente_nit,:link,:responsable)");
    
    $sentencia->bindParam(":cliente_nombre", $cliente_nombre);
    $sentencia->bindParam(":cliente_apellido", $cliente_apellido);
    $sentencia->bindParam(":cliente_ciudad", $cliente_ciudad);
    $sentencia->bindParam(":cliente_direccion", $cliente_direccion);
    $sentencia->bindParam(":cliente_telefono", $cliente_telefono);
    $sentencia->bindParam(":cliente_email", $cliente_email);
    $sentencia->bindParam(":cliente_empresa", $cliente_empresa);
    $sentencia->bindParam(":cliente_nit", $cliente_nit);
    $sentencia->bindParam(":link", $link);
    $sentencia->bindParam(":responsable",$responsable);
    $result = $sentencia->execute();
        if ($result) {
            echo "Comida insertada correctamente.";
        }else {
            echo "Comida insertada Erronea.";
        }


?>
