<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $crear_proveedor_link  = "crear_proveedor.php";

}else{
  $crear_proveedor_link  = "crear_proveedor.php?link=".$link;
}
//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID = (isset($_GET['txtID']))?$_GET['txtID']:"";
  
  $sentencia=$conexion->prepare("DELETE FROM proveedores WHERE id_proveedores =:id_proveedores");
  $sentencia->bindParam(":id_proveedores",$txtID);
  $sentencia->execute();
  
}
$linkeo = 0;
if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";}

  $responsable = $_SESSION['usuario_id'];
  if ($responsable == 1) {
    $sentencia=$conexion->prepare("SELECT p.*, e.empresa_nombre FROM proveedores p LEFT JOIN empresa e ON p.link = e.link");

  }else {
    $sentencia=$conexion->prepare("SELECT p.*, e.empresa_nombre FROM proveedores p JOIN empresa e ON p.link = e.link WHERE p.link =:link");
  $sentencia->bindParam(":link",$linkeo);

    
  }
    $sentencia->execute();
    $lista_proveedores=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>
      <br>
      <div class="card card-primary ">
        <div class="card-header text-center ">
        <h2 class="card-title textTabla">LISTA DE PROVEEDORES  &nbsp; <a href="<?php echo $url_base;?>secciones/<?php echo $crear_proveedor_link;?>" class="btn btn-warning" style="color:black"> Registrar Proveedor </a> </h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_usuario" class="table table-bordered table-striped" style="text-align:center ">
            <thead>
            <tr>
              <th>#</th>
              <th>Nit</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Dirección </th>
              <th>Negocio </th>
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_proveedores as $registro) {?>
                <tr>
                  <td scope="row"><?php echo $registro['id_proveedores']; ?></td>
                  <td><?php echo $registro['nit_proveedores']; ?></td>
                  <td><?php echo $registro['nombre_proveedores']; ?></td>
                  <td><?php echo $registro['telefono_proveedores']; ?></td>
                  <td><?php echo $registro['email_proveedores']; ?></td>                
                  <td><?php echo $registro['direccion_proveedores']; ?></td> 
                  <td><?php if ($registro['link'] == "sudo_admin") {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
                  <td>
                    <a class="btn btn-info btn-sm" href="editar_proveedores.php?txtID=<?php echo $registro['id_proveedores']; ?>"role="button" title="Editar">
                      <i class="fas fa-edit"></i>Editar
                    </a>
                    <a class="btn btn-danger btn-sm" href="index_proveedores.php?txtID=<?php echo $registro['id_proveedores']; ?>" role="button" title="Eliminar">
                      <i class="fas fa-trash"></i>Eliminar 
                    </a>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>