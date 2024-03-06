<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM categoria WHERE categoria_id=:categoria_id");
  $sentencia->bindParam(":categoria_id",$txtID);
  $sentencia->execute();
  
}
$sentencia=$conexion->prepare("SELECT * FROM `categoria`");

$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE CATEGORIAS &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/productos/crear.php">Crear Producto</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Codigo</th>
              <th>Nombre</th>
              <th>Fecha de creacion</th> 
              <?php if ($_SESSION['rolEmpleado']) { ?>             
              <th>Editar</th>
              <?php } ?>  
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_producto as $registro) {?>
                <tr class="">
                  <td scope="row"><?php echo $registro['categoria_id']; ?></td>
                  <td><?php echo $registro['categoria_nombre']; ?></td>                               
                  <td><?php echo $registro['categoria_fecha_creacion']; ?></td>                  
                  <?php if ($_SESSION['rolEmpleado']) { ?>
                  <td class="text-center">
                    <div class="btn-group">
                        <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['categoria_id']; ?>"role="button" title="Editar"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger"href="lista_categoria.php?txtID=<?php echo $registro['categoria_id']; ?>" role="button" title="Eliminar"><i class="far fa-trash-alt"></i></a>  
                        <?php } ?>                  
                    </div>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>


<?php include("../../templates/footer_content.php") ?>