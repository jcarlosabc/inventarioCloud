<?php include("../templates/header.php") ?>
<?php 

if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM gastos WHERE gasto_id=:gasto_id");
  $sentencia->bindParam(":gasto_id",$txtID);
  $sentencia->execute();
  
}
$responsable = $_SESSION['usuario_id'];
if ($responsable == 1) {
  $sentencia = $conexion->prepare("SELECT g.*, e.empresa_nombre FROM gastos g JOIN empresa e ON e.link = g.link");

}else { 
  $sentencia = $conexion->prepare("SELECT g.*, e.empresa_nombre FROM gastos g JOIN empresa e ON e.link = g.link WHERE g.link =:link");
  $sentencia->bindParam(":link",$link);
}

$sentencia->execute();
$lista_gastos=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE GASTOS &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_gasto_link;?>">Registrar Gastos</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="listaProductos" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Producto Adquirido</th>
              <th>Motivo de la Compra</th>
              <th>Precio</th>
              <th>Negocio</th>
              <?php if ($_SESSION['rolEmpleado']) { ?>
              <th>Opciones</th>
              <?php } ?>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_gastos as $registro) {?>
                <tr class="">
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['gasto_producto']; ?></td>
                  <td><?php echo $registro['gasto_motivo']; ?></td>
                  <td class="tdColor"> <?php echo '$' . number_format($registro['gasto_precio'], 0, '.', ','); ?></td>
                  <td><?php if ($registro['link'] == "sudo_admin") {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td> 
                  <?php if ($_SESSION['rolEmpleado']) { ?>
                  <td>
                    <a class="btn btn-danger"href="index_gastos.php?txtID=<?php echo $registro['gasto_id']; ?>" role="button" title="Eliminar">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </a>
                  </td>
                  <?php }?>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>