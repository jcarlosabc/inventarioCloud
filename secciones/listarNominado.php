<?php
include("../db.php");
// if(isset($_GET['link'])){ 
//     $linkeo=(isset($_GET['link']))?$_GET['link']:"";
// }
// Inicializa el array para almacenar los resultados
$respuesta = array();
try {
    $sentencia=$conexion->prepare("SELECT u.*, n.nomina_prestamo, n.nomina_estado
        FROM usuario u 
        LEFT JOIN nomina n ON n.nomina_usuario_id = u.usuario_id 
        WHERE u.usuario_id > 1 AND u.rol != 1 AND u.rol != 3
        ");
        // GROUP BY u.usuario_id;
    $sentencia->execute();
    $lista_empleados=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

    $respuesta['success'] = true;
    $respuesta['data'] = $lista_empleados;
} catch (PDOException $e) {
    $respuesta['success'] = false;
    $respuesta['message'] = "Error al ejecutar la consulta: " . $e->getMessage();
}
header('Content-Type: application/json');
echo json_encode($respuesta);
?> 
