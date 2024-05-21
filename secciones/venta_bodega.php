<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $ventas_link_bodega = "venta_bodega.php";
  $crear_ventas_link_bodega = "crear_venta_bodega.php";
  $devolucion_venta ="devolucion_venta.php?txtID";
  $ventas_detalles_link ="detalles.php?link=sudo_bodega&txtID";

}else{
  $ventas_link_bodega = "venta_bodega.php?link=sudo_bodega";
  $crear_ventas_link_bodega = "crear_venta_bodega.php?link=sudo_bodega";
  $devolucion_venta ="devolucion_venta_bodega.php?link=sudo_bodega";
  $ventas_detalles_link ="detalles.php?link=sudo_bodega&txtID";
}
if($_SESSION['rolSudoAdmin']){
  $linkeo = "sudo_admin";
}else if ($_SESSION['rolBodega']) {
  $linkeo = "sudo_bodega";
}

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM venta WHERE venta_id=:venta_id");
  $sentencia->bindParam(":venta_id",$txtID);
  $sentencia->execute();
  
}
if($_SESSION['rolSudoAdmin']){
  $sentencia=$conexion->prepare("SELECT venta.*, usuario.*, cliente.*
  FROM venta 
  LEFT JOIN usuario ON venta.responsable = usuario.usuario_id 
  LEFT JOIN cliente ON venta.cliente_id = cliente.cliente_id");
}else if($_SESSION['rolBodega']){
  $sentencia=$conexion->prepare("SELECT venta.*, usuario.*, cliente.*
  FROM venta 
  LEFT JOIN usuario ON venta.responsable = usuario.usuario_id 
  LEFT JOIN cliente ON venta.cliente_id = cliente.cliente_id
  WHERE venta.link =:link;");
  $sentencia->bindParam(":link",$linkeo);
}

$sentencia->execute();
$lista_ventas=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

      <div class="card card-success">
        <div class="card-header" style="background: #493a3be0">
          <h2 class="card-title textTabla">HISTORIAL DE VENTAS BODEGA&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_ventas_link_bodega;?>" class="btn btn-warning">Crear Venta</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="historialVentas" class="table table-bordered table-striped" style="text-align:center"> 
            <thead>
            <tr>
              <th>Código</th>
              <th>Fecha / Hora </th>
              <th>Total</th>
              <th>Pagado</th>
              <th>Cambio/Deuda</th>
              <th>Cliente</th>
              <th>responsable</th>
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_ventas as $registro) {?>
                <tr>
                <?php if (!$_SESSION['rolSudoAdmin'] && !$_SESSION['rolBodega'] ) { ?>
                  <input type="hidden" name="codigo_seguridad" value="<?php echo $registro['codigo_seguridad']; ?>">
                <?php } ?>     
                  <td scope="row"><?php echo $registro['venta_codigo']; ?></td>
                  <td><?php echo $registro['venta_fecha']; ?> / <?php echo $registro['venta_hora']; ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['venta_total'], 0, '.', ','); ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['venta_pagado'], 0, '.', ','); ?></td> 
                  <td class="tdColor"><?php echo ($registro['venta_metodo_pago'] == 0 || $registro['venta_metodo_pago'] == 1) ? '$' . number_format($registro['venta_cambio'], 0, '.', ',') : '$' . number_format($registro['venta_cambio'], 0, '.', ',') ; ?></td>
                  <td><a  <?php if($registro['cliente_id'] != 0 ){ ?> href="editar_clientes.php?link=sudo_bodega&txtID=<?php echo $registro['cliente_id']; ?>" <?php }?>    ><?php echo $registro['cliente_nombre']; ?></a></td>
                  <td><?php echo  $registro['usuario_nombre']; ?></td>
                  <td>
                    <a class="btn btn-primary btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $ventas_detalles_link ;?>=<?php echo $registro['venta_id']; ?>" role="button" title="Detalles">
                      <i class="fas fa-eye"></i> 
                    </a>
                    <a class="btn btn-warning btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $devolucion_venta.'&txtID='.$registro['venta_id']; ?>" role="button" title="Devolucion">
                      <i class="fas fa-retweet"></i> 
                    </a>
                    <button class="btn btn-danger btn-sm" onclick="confirmDeleteBodega('<?php echo $registro['venta_id']; ?>')" role="button" title="Eliminar">
                      <i class="fas fa-trash-alt"></i>  
                    </button>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <script src="https://code.jquery.com/jquery-3.7.1.js" ></script>
      <script>
          // Borrar para ventas bodega
          function confirmDeleteBodega(venta_id) {
          Swal.fire({
              title: '¿Estás seguro?',
              text: "¡No podrás revertir esto!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'No, cancelar'
          }).then((result) => {
              if (result.isConfirmed) {
                  window.location.href = "<?php echo $url_base;?>secciones/<?php echo $ventas_link_bodega; ?>&txtID=" + venta_id;
              }
          });
      }
      </script>
      <?php include("../templates/footer.php") ?>
