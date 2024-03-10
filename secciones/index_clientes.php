<?php include("../templates/header.php") ?>
<?php
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
      <br>
      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE CLIENTES &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="crear_cliente.php" role="button">Crear Cliente</a></h2>
        </div>
        <div class="card-body">
          <table id="listaClientes" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Cedula</th>
              <th>Nombres / Apellidos</th>
              <th>Ciudad</th>
              <th>Dirección</th>                                    
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Editar</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_cliente as $registro) {?>
                <tr>
                  <td scope="row"><?php $count++;  echo $count; ?></td>
                  <td><?php echo $registro['cliente_numero_documento']; ?></td>
                  <td><?php echo $registro['cliente_nombre']; ?> <?php echo $registro['cliente_apellido']; ?></td>
                  <td><?php echo $registro['cliente_ciudad']; ?></td>                
                  <td><?php echo $registro['cliente_direccion']; ?></td>
                  <td><?php echo $registro['cliente_telefono']; ?></td>
                  <td><?php echo $registro['cliente_email']; ?></td>
                  <td>
                    <a class="btn btn-info" href="editar_clientes.php?txtID=<?php echo $registro['cliente_id']; ?>"role="button" title="Editar">
                        <i class="fas fa-edit"></i>Editar
                    </a>
                    <a class="btn btn-danger"href="index_clientes.php?txtID=<?php echo $registro['cliente_id']; ?>" role="button" title="Eliminar">
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