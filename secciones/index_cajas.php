<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $crear_caja_link = 'crear_caja.php';
}else{
  $crear_caja_link = 'crear_caja.php?link='.$link;       
}
if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}
$responsable = $_SESSION['usuario_id'];
  if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("DELETE FROM caja WHERE caja_id=:caja_id");
    $sentencia->bindParam(":caja_id",$txtID);
    $sentencia->execute();    
  }

  if($responsable == 1){
    $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM caja c LEFT JOIN empresa e ON c.link = e.link");
  }else{
    $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM caja c JOIN empresa e ON c.link = e.link WHERE c.link = :link");
    $sentencia->bindParam(":link", $link);
  }
  $sentencia->execute();
  $lista_caja=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
<br>
<div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE CAJAS &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_caja_link;?>" role="button">Crear Caja</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_cajas" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
                <th>#</th>
                <th>Código </th>
                <th>Nombre </th>
                <th>Negocio </th>
                <th>Efectivo </th> 
                <th>Opciónes</th> 
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_caja as $registro) {?>
                <tr>
                <td scope="row"><?php $count++; echo $count; ?></td>
                <td><?php echo $registro['caja_numero']; ?></td>
                <td><?php echo $registro['caja_nombre']; ?></td>
                <td><?php if ($registro['link'] == "sudo_admin" ) {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
                <td class="tdColor"><?php echo '$' . number_format($registro['caja_efectivo'], 0, '.', ','); ?></td>
                <td class="text-center">
                  <a class="btn btn-info" href="editar_cajas.php?txtID=<?php echo $registro['caja_id']; ?>"role="button"title="Editar">
                    <i class="fas fa-edit"></i>Editar
                  </a>
                  <a class="btn btn-danger"href="index_cajas.php?txtID=<?php echo $registro['caja_id']; ?>" role="button"title="Eliminar">
                      <i class="fas fa-trash-alt"></i>Eliminar
                  </a>
                </td>
                </tr> 
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>