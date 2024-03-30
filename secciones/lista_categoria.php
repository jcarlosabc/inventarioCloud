<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $crear_categoria_link  = "crear_categoria.php";

}else{
  $crear_categoria_link  = "crear_categoria.php?link=".$link;

}
//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  
  $sentencia=$conexion->prepare("DELETE FROM categoria WHERE categoria_id=:categoria_id");
  $sentencia->bindParam(":categoria_id",$txtID);
  $sentencia->execute();
  
}
// Buscando link de la empresa actual
$linkeo = 0;
if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";}

$responsable = $_SESSION['usuario_id'];
$link = "sudo_admin";
if ($responsable == 1) {
  $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM categoria c LEFT JOIN empresa e ON c.link = e.link");
}else {
  $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM categoria c JOIN empresa e ON c.link = e.link WHERE c.link =:link");
  $sentencia->bindParam(":link",$linkeo);
}
$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
        <h2 class="card-title textTabla">LISTA DE CATEGORÍAS &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_categoria_link;?>">Crear Categoría</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_categoria" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Código</th>
              <th>Nombre</th>
              <th>Fecha de creación</th> 
              <th>Negocio</th> 
              <?php if ($_SESSION['rolSudoAdmin']) { ?>             
              <th>Editar</th>
              <?php } ?>  
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_producto as $registro) {?>
                <tr class="">
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['categoria_id']; ?></td>
                  <td><?php echo $registro['categoria_nombre']; ?></td>                               
                  <td><?php echo $registro['categoria_fecha_creacion']; ?></td>                  
                  <td><?php if ($registro['link'] == "sudo_admin") {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
                  <?php if ($_SESSION['rolSudoAdmin']) { ?>
                  <td>
                    <a class="btn btn-danger"href="lista_categoria.php?txtID=<?php echo $registro['categoria_id']; ?>" role="button" title="Eliminar">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </a>
                  </td>
                  <?php } ?>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>