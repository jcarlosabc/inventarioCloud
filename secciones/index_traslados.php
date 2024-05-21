<?php include("../templates/header.php") ?>
<?php 

if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}

if ($_SESSION['valSudoAdmin']) {
    $detalles_traslado_local = "detalles_traslado_local.php?txtID";
 }else{
    $detalles_traslado_local = "detalles_traslado_local.php?link=".$link.'&txtID';
 }
 $_SESSION['link_remitente_array'] = array();

//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

//   $sentencia=$conexion->prepare("DELETE FROM producto WHERE producto_id=:producto_id");
//   $sentencia->bindParam(":producto_id",$txtID);
//   $sentencia->execute();
  
}
$responsable = $_SESSION['usuario_id'];
if ($responsable == 1) {

  $sentencia_historial_traslados = $conexion->prepare("SELECT DISTINCT ht.*, ht.traslado as trasladoCode, p.*, e.empresa_nombre 
    FROM historial_traslados ht 
    JOIN bodega p ON ht.producto_id = p.producto_id JOIN empresa e ON ht.link_destino = e.link 
    -- WHERE ht.link_remitente = :link
    GROUP BY ht.traslado");
  // $sentencia_historial_traslados->bindParam(":link", $link);

}else if($link == "sudo_bodega") { 
  $sentencia_historial_traslados = $conexion->prepare("SELECT DISTINCT ht.*, ht.traslado as trasladoCode, p.*, e.empresa_nombre 
    FROM historial_traslados ht 
    JOIN bodega p ON ht.producto_id = p.producto_id JOIN empresa e ON ht.link_destino = e.link 
    WHERE ht.link_remitente = :link
    GROUP BY ht.traslado");
  $sentencia_historial_traslados->bindParam(":link", $link);
  
}else {
  $sentencia_historial_traslados = $conexion->prepare("SELECT DISTINCT ht.*, ht.traslado as trasladoCode, p.*, e.empresa_nombre 
    FROM historial_traslados ht 
    JOIN producto p ON ht.producto_id = p.producto_id JOIN empresa e ON ht.link_destino = e.link 
    WHERE ht.link_remitente = :link
    GROUP BY ht.traslado");
  $sentencia_historial_traslados->bindParam(":link", $link);

}
$sentencia_historial_traslados->execute();
$detalle_traslado = $sentencia_historial_traslados->fetchAll(PDO::FETCH_ASSOC)

// -- WHERE ht.link_remitente =:link AND ht.traslado=:txtID");
?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">HISTORIAL DE TRASLADOS</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="listaProductos" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Remision</th>
              <th>Referencia de Traslado</th>
              <th>Destino</th>
              <th>Fecha de Traslado</th>
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0; $precioComptaTotal =0;
              foreach ($detalle_traslado as $registro) {
                $_SESSION['link_remitente_array'][] = $registro['link_remitente'];
                ?>
                <tr>
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['remision_traslado']; ?></td>
                  <td><?php echo $registro['trasladoCode']; ?></td>
                  <td><?php echo $registro['empresa_nombre']; ?></td>
                  <td><?php echo $registro['fecha_traslado']; ?></td>
                  <td>
                    <a class="btn btn-primary btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $detalles_traslado_local ;?>=<?php echo $registro['trasladoCode']; ?>" role="button" title="Detalles">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>