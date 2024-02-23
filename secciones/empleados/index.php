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
    $sentencia=$conexion->prepare("SELECT usuario.*, caja.*
    FROM usuario
    INNER JOIN caja ON usuario.caja_id = caja.caja_id ");
    $sentencia->execute();
    $lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>

      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title">LISTA DE USUARIO</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Usuario</th>
              <th>Correo</th>
              <th>Tipo de usuario</th>
              <th>Caja de usuario</th>

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
                  <td><?php echo $registro['caja_nombre']; ?></td>

                  <td>
                    <div class="btn-group">
                        <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['usuario_id']; ?>"role="button">Editar</a>
                        <a class="btn btn-danger"href="index.php?txtID=<?php echo $registro['usuario_id']; ?>" role="button">Eliminar</a>                    
                      </div>
                    </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>

<?php include("../../templates/footer_content.php") ?>