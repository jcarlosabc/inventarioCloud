<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM usuario WHERE usuario_id=:usuario_id");
  $sentencia->bindParam(":usuario_id",$txtID);
  $sentencia->execute();
  
}
    $sentencia=$conexion->prepare("SELECT * FROM usuario");
    $sentencia->execute();
    $lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>

      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title">LISTA DE USUARIO</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_usuario" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Apellidos</th>
              <th>Correo</th>
              <th>Tipo de usuario</th>
              <!-- <th>Caja de usuario</th> -->
              <th>Opciones</th>

            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_producto as $registro) {?>
                <tr class="">
                  <td scope="row"><?php echo $registro['usuario_id']; ?></td>
                  <td><?php echo $registro['usuario_nombre']; ?></td>
                  <td><?php echo $registro['usuario_apellido']; ?></td>
                  <td><?php echo $registro['usuario_email']; ?></td>                
                  <td><?php echo $registro['usuario_usuario']; ?></td>
                  <!-- <td><?php echo $registro['caja_nombre']; ?></td> -->

                  <td>
                    <div class="btn-group">
                        <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['usuario_id']; ?>"role="button"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger"href="index.php?txtID=<?php echo $registro['usuario_id']; ?>" role="button"><i class="far fa-trash-alt"></i></a>                    
                      </div>
                    </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>

<?php include("../../templates/footer_content.php") ?>