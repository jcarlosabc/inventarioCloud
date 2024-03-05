<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php"); 
if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  
    $sentencia=$conexion->prepare("DELETE FROM cliente WHERE cliente_id=:cliente_id");
    $sentencia->bindParam(":cliente_id",$txtID);
    $sentencia->execute();
    
  }
  $sentencia=$conexion->prepare("SELECT * FROM `cliente` WHERE cliente_id > 0");

$sentencia->execute();
$lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE CLIENTES &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="crear.php" role="button">Crear Cliente</a></h2>
          
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="listaClientes" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Identificacion</th>
              <th>Nombre</th>
              <th>Apellidos</th>
              <th>Ciudad</th>
              <th>Provincia</th>
              <th>Direccion</th>                                    
              <th>Telefono</th>
              <th>Correo Electronico</th>
              <th>Editar</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_cliente as $registro) {?>
                <tr class="">
                  <td scope="row"><?php echo $registro['cliente_numero_documento']; ?></td>
                  <td><?php echo $registro['cliente_nombre']; ?></td>
                  <td><?php echo $registro['cliente_apellido']; ?></td>
                  <td><?php echo $registro['cliente_ciudad']; ?></td>                
                  <td><?php echo $registro['cliente_provincia']; ?></td>
                  <td><?php echo $registro['cliente_direccion']; ?></td>
                  <td><?php echo $registro['cliente_telefono']; ?></td>
                  <td><?php echo $registro['cliente_email']; ?></td>
                  <td class="text-center">
                    <div class="btn-group">
                        <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['cliente_id']; ?>"role="button" title="Editar"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger"href="index.php?txtID=<?php echo $registro['cliente_id']; ?>" role="button" title="Eliminar"><i class="far fa-trash-alt"></i></a>                    
                      </div>
                    </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>



<?php include("../../templates/footer_content.php") ?>