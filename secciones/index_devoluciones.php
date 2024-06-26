<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $crear_venta_link  = "crear_venta.php";
  $devolucion_venta_link  = "devolucion_venta.php";
  $index_ventas_link = "index_ventas.php";

}else{
  $crear_venta_link  = "crear_venta.php?link=".$link;
  $devolucion_venta_link  = "devolucion_venta.php?link=".$link;
  $index_ventas_link = "index_ventas.php?link=".$link;
}
//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM venta WHERE venta_id=:venta_id");
  $sentencia->bindParam(":venta_id",$txtID);
  $sentencia->execute();
  
}

// FALTA ********************
// Arreglar ver devoluciones desde el admin del local se repite en sudo admin y en adminlocal
// poner el responsable correcto en la lista (opcional) pero seria genial hacerlo

if($_SESSION['rolSudoAdmin']){
  $sentencia=$conexion->prepare("SELECT venta.*, cliente.*, empresa.empresa_nombre, devolucion.*, usuario.*, producto.*
  FROM venta
  INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id
  INNER JOIN empresa ON venta.link = empresa.link
  INNER JOIN devolucion ON venta.link = devolucion.link
  INNER JOIN usuario ON devolucion.responsable = usuario.usuario_id
  INNER JOIN producto ON devolucion.producto_id = producto.producto_id
  GROUP BY devolucion.id;");
}else if($_SESSION['rolBodega']){
  $link=(isset($_GET['link']))?$_GET['link']:"";
  $sentencia=$conexion->prepare("SELECT venta.*, usuario.*, cliente.*, 
  empresa_bodega.bodega_nombre as empresa_nombre, empresa_bodega.codigo_seguridad, devolucion.*, bodega.*
  FROM venta 
  INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id
  INNER JOIN empresa_bodega ON venta.link = empresa_bodega.link
  INNER JOIN devolucion ON venta.link = devolucion.link
  INNER JOIN usuario ON devolucion.responsable = usuario.usuario_id 
  INNER JOIN bodega ON devolucion.producto_id = bodega.producto_id
  WHERE venta.link = :link GROUP BY devolucion.id");
  $sentencia->bindParam(":link",$link);
}else {
  $link=(isset($_GET['link']))?$_GET['link']:"";
  $sentencia=$conexion->prepare("SELECT venta.*, usuario.*, cliente.*, 
  empresa.empresa_nombre, empresa.codigo_seguridad, devolucion.*, producto.*
  FROM venta 
  INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id
  INNER JOIN empresa ON venta.link = empresa.link
  INNER JOIN devolucion ON venta.link = devolucion.link
  INNER JOIN usuario ON devolucion.responsable = usuario.usuario_id 
  INNER JOIN producto ON devolucion.producto_id = producto.producto_id
  WHERE venta.link = :link GROUP BY devolucion.id");
  $sentencia->bindParam(":link",$link);
}
$sentencia->execute();
$lista_ventas=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
      <div class="card card-success">
        <div class="card-header">
          <h2 class="card-title textTabla">HISTORIAL DEVOLUCIONES </h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="historialVentas" class="table table-bordered table-striped" style="text-align:center"> 
            <thead>
            <tr>
              <th>#</th>
              <th>Código de Venta</th>
              <th>Código del Producto</th>
              <th>Producto</th>
              <th>Tipo Devolución</th>
              <th>Proceso</th>
              <th>Información</th>
              <th>Salida</th>
              <th>Fecha</th>
              <?php if ($_SESSION['rolSudoAdmin']) { ?>
                <th>Negocio</th>
                <?php } ?>            
            <th>Responsable</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0; foreach ($lista_ventas as $registro) {?>
                <tr>
                    <td scope="row"><?php $count++; echo $count; ?></td>
                    <td><?php echo $registro['venta_codigo']; ?></td>
                    <td><?php echo $registro['producto_codigo']; ?></td>
                    <td><?php echo $registro['producto_nombre']; ?></td>
                    <td><?php if ($registro['monto_devolucion'] > 0) { echo "Dinero"; }else { echo "Articulo"; } ?></td>
                    <?php if ($registro['monto_devolucion'] > 0) { ?>
                        <td style="background:#ffdada">➡️</td>
                    <?php } else {  ?>
                        <td style="background:#e1ffda">♻️</td>
                    <?php } ?>
                    <?php if ($registro['monto_devolucion'] > 0) { ?>
                        <td><?php echo $registro['devolucion_motivo'] ?></td>
                    <?php } else {  ?>
                      <td><?php echo "Motivo: " . $registro['devolucion_motivo'] ."<br> Sn/Ref: " . $registro['devolucion_serial']; ?></td>
                    <?php } ?>
                    <td><?php if ($registro['monto_devolucion'] > 0) { echo '$' . number_format($registro['monto_devolucion'], 0, '.', ',');}else { echo "Se hizo entrega por otro Articulo"; } ?></td>
                    <td><?php echo $registro['devolucion_fecha'] . " " . $registro['devolucion_hora'] ?></td>
                    <?php if ($_SESSION['rolSudoAdmin']) { ?> <td><?php echo  $registro['empresa_nombre']; ?></td> <?php }?>
                    <td><?php echo $registro['usuario_nombre'] . " " . $registro['usuario_apellido']; ?></td>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
<?php include("../templates/footer.php") ?>