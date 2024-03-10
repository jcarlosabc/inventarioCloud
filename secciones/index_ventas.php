<?php include("../templates/header.php") ?>
<?php 
//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM venta WHERE venta_id=:venta_id");
  $sentencia->bindParam(":venta_id",$txtID);
  $sentencia->execute();
  
}

$sentencia=$conexion->prepare("SELECT venta.*, usuario.*,cliente.* 
FROM venta 
INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id; ");

$sentencia->execute();
$lista_ventas=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

      <div class="card card-success">
        <div class="card-header">
          <h2 class="card-title textTabla">HISTORIAL DE VENTAS &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/crear_venta.php">Crear Venta</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="historialVentas" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>Codigo</th>
              <th>Fecha / Hora </th>
              <th>Total</th>
              <th>Pagado</th>
              <th>Cambio</th>
              <th>Cliente</th>
              <th>responsable</th>              
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_ventas as $registro) {?>
                <tr class="">
                  <td scope="row"><?php echo $registro['venta_codigo']; ?></td>
                  <td><?php echo $registro['venta_fecha']; ?> / <?php echo $registro['venta_hora']; ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['venta_total'], 0, '.', ','); ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['venta_pagado'], 0, '.', ','); ?></td> 
                  <td class="tdColor"><?php echo '$' . number_format($registro['venta_cambio'], 0, '.', ','); ?></td>
                  <td><a  <?php if($registro['cliente_id'] != 0 ){ ?> href="../editar_clientes.php?txtID=<?php echo $registro['cliente_id']; ?>" <?php }?>    ><?php echo $registro['cliente_nombre']; ?></a></td>
                  <td><?php echo  $registro['usuario_nombre']; ?></td> 
                  <td>
                    <a class="btn btn-primary btn-sm" href="<?php $url_base ?>detalles.php?txtID=<?php echo $registro['venta_id']; ?>" role="button" title="Detalles">
                      <i class="fas fa-eye"></i> Ver
                    </a>
                    <a class="btn btn-warning btn-sm" href="devolucion_venta.php?txtID=<?php echo $registro['venta_id']; ?>" role="button" title="Devolucion">
                      <i class="fas fa-retweet"></i> Devolución
                    </a>
                    <?php if ($_SESSION['rolEmpleado']) { ?>
                      <a class="btn btn-danger btn-sm" href="index_ventas.php?txtID=<?php echo $registro['venta_id']; ?>" role="button" title="Eliminar">
                        <i class="fas fa-trash-alt"></i> Eliminar 
                      </a>
                    <?php } ?>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>