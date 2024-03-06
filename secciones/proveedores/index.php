<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID = (isset($_GET['txtID']))?$_GET['txtID']:"";
  
  $sentencia=$conexion->prepare("DELETE FROM proveedores WHERE id_proveedores =:id_proveedores");
  $sentencia->bindParam(":id_proveedores",$txtID);
  $sentencia->execute();
  
}
    $sentencia=$conexion->prepare("SELECT * FROM proveedores ");
    $sentencia->execute();
    $lista_proveedores=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>

      <div class="card card-primary ">
        <div class="card-header text-center ">
          <h2 class="card-title textTabla">Lista de Proveedores  &nbsp; <a href="<?=$url_base?>secciones/proveedores/crear.php" class="btn btn-warning" style="color:black"> Registra Proveedor </a> </h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_usuario" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>#</th>
              <th>Nit proveedor</th>
              <th>Nombre proveedor</th>
              <th>Email</th>
              <th>telefono</th>
              <th>direccion </th>
              <th>opciones</th>

            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_proveedores as $registro) {?>
                <tr class="">
                  <td scope="row"><?php echo $registro['id_proveedores']; ?></td>
                  <td><?php echo $registro['nit_proveedores']; ?></td>
                  <td><?php echo $registro['nombre_proveedores']; ?></td>
                  <td><?php echo $registro['email_proveedores']; ?></td>                
                  <td><?php echo $registro['telefono_proveedores']; ?></td>
                  <td><?php echo $registro['direccion_proveedores']; ?></td> 

                  <td class="text-center">
                    <div class="btn-group">
                        <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['id_proveedores']; ?>"role="button" title="Editar"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger"href="index.php?txtID=<?php echo $registro['id_proveedores']; ?>" role="button" title="Eliminar"><i class="far fa-trash-alt"></i></a>                    
                      </div>
                    </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>

<?php include("../../templates/footer_content.php") ?>