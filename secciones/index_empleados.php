<?php include("../templates/header.php") ?>
<?php 
//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM usuario WHERE usuario_id=:usuario_id");
  $sentencia->bindParam(":usuario_id",$txtID);
  $sentencia->execute();
}
  $sentencia=$conexion->prepare("SELECT u.*, c.caja_nombre FROM usuario u JOIN caja c ON u.caja_id = c.caja_id WHERE u.usuario_id > 1 ");
  $sentencia->execute();
  $lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>
      <br>
      <div class="card card-primary ">
        <div class="card-header text-center ">
          <h2 class="card-title textTabla">LISTA DE EMPLEADOS  &nbsp; <a href="<?=$url_base?>secciones/crear_empleado.php" class="btn btn-warning" style="color:black"> Nuevo Empleado </a>
          </h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_usuario" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Nombres / Apellidos</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Usuario</th>
              <th>Caja de usuario</th>
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
               foreach ($lista_producto as $registro) {?>
                <tr>
                  <td scope="row"><?php $count++; echo $count;  ?> </td>
                  <td><?php echo $registro['usuario_nombre']; ?> <?php echo $registro['usuario_apellido']; ?></td>
                  <td><?php echo $registro['usuario_telefono']; ?></td>                
                  <td><?php echo $registro['usuario_email']; ?></td>                
                  <td><?php echo $registro['usuario_usuario']; ?></td>
                  <td><?php echo $registro['caja_nombre']; ?></td>
                  <td>
                    <a class="btn btn-info" href="editar_empleados.php?txtID=<?php echo $registro['usuario_id']; ?>"role="button" title="Editar">
                        <i class="fas fa-edit"></i>Editar
                    </a>
                    <a class="btn btn-danger"href="index_empleados.php?txtID=<?php echo $registro['usuario_id']; ?>" role="button" title="Eliminar">
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