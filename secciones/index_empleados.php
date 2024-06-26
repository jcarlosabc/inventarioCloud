<?php include("../templates/header.php") ?>
<?php 

if ($_SESSION['valSudoAdmin']) {
  $editar_empleados_link = "editar_empleados.php?txtID";
  $index_empleados_link = "index_empleados.php?txtID";
  $crear_nomina_link = "crear_nomina.php?txtID";
  
}else{
  $editar_empleados_link = "editar_empleados.php?link=".$link."&txtID";
  $index_empleados_link = "index_empleados.php?link=".$link."&txtID";
}

if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}
//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM usuario WHERE usuario_id=:usuario_id");
  $sentencia->bindParam(":usuario_id",$txtID);
  $sentencia->execute();
}
  $responsable = $_SESSION['usuario_id'];
  if ($responsable == 1) {
    $sentencia=$conexion->prepare("SELECT u.*, c.caja_nombre, e.empresa_nombre, eb.bodega_nombre
    FROM usuario u
    LEFT JOIN caja c ON u.caja_id = c.caja_id
    LEFT JOIN empresa e ON u.link = e.link
    LEFT JOIN empresa_bodega eb ON u.link = eb.link
    WHERE u.usuario_id > 1 AND u.rol != 1 AND u.rol != 3");
  }else{
    $sentencia=$conexion->prepare("SELECT u.*, c.caja_nombre, e.empresa_nombre FROM usuario u JOIN caja c ON u.caja_id = c.caja_id JOIN empresa e ON u.link = e.link WHERE u.usuario_id > 1 AND u.link=:link AND u.rol = 2");
    $sentencia->bindParam(":link",$link);
   }
  $sentencia->execute();
  $lista_empleados=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>
      <br>
      <div class="card card-primary ">
        <div class="card-header text-center ">
          <h2 class="card-title textTabla">LISTA DE EMPLEADOS  &nbsp; <a href="<?php echo $url_base;?>secciones/<?php echo $crear_empleado_link;?>" class="btn btn-warning" style="color:black"> Nuevo Empleado </a>
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
              <th>Cédula</th>
              <th>Correo</th>
              <th>Usuario</th>
              <th>Caja de usuario</th>
              <th>Rol</th>
              <th>Negocio</th>
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
               foreach ($lista_empleados as $registro) {?>
                <tr>
                  <td scope="row"><?php $count++; echo $count;  ?> </td>
                  <td><?php echo $registro['usuario_nombre']; ?> <?php echo $registro['usuario_apellido']; ?></td>
                  <td><?php echo $registro['usuario_telefono']; ?></td>                
                  <td><?php echo $registro['usuario_cedula']; ?></td>                
                  <td><?php echo $registro['usuario_email']; ?></td>                
                  <td><?php echo $registro['usuario_usuario']; ?></td>
                  <td><?php echo $registro['caja_nombre']; ?></td>
                  <td><?php if ($registro['rol'] == 1) { echo "Administrador de Local" ;}else { echo "Empleado" ;} ?></td>
                  <td><?php echo $registro['empresa_nombre'] . " " . $registro['bodega_nombre'] ?></td>
                  <td>
                    <!-- php if ($_SESSION['valSudoAdmin']) { ?> 
                      <a class="btn btn-success" href="<php echo $url_base;?>secciones/<php echo $crear_nomina_link . '=' . $registro['usuario_id']; ?>" role="button" title="Nomina">
                      <i class="fa fa-list-alt nav-icon"></i> Nómina
                      </a>
                  php } ?>  -->
                    <a class="btn btn-info btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $editar_empleados_link . '=' . $registro['usuario_id']; ?>"role="button" title="Editar">
                        <i class="fas fa-edit"></i> 
                    </a>
                    <a class="btn btn-danger btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $index_empleados_link . '=' . $registro['usuario_id']; ?>" role="button" title="Eliminar">
                        <i class="fas fa-trash-alt"></i> 
                    </a>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>