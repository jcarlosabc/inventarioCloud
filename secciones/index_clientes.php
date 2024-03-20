<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
  $crear_cliente_link  = "crear_cliente.php";

}else{
  $crear_cliente_link  = "crear_cliente.php?link=".$link;
}

if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}

if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("DELETE FROM cliente WHERE cliente_id=:cliente_id");
    $sentencia->bindParam(":cliente_id",$txtID);
    $sentencia->execute();
  }
  if($link != ""){
    $sentencia=$conexion->prepare("SELECT * FROM `cliente` WHERE cliente_id > 0  AND link = :link");
    $sentencia->bindParam(":link", $link);
    $sentencia->execute();
    $lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);
  }else{
    $sentencia=$conexion->prepare("SELECT * FROM cliente LEFT JOIN empresa ON cliente.link = empresa.link WHERE cliente.cliente_id > 0");
    $sentencia->execute();
    $lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);
  }


?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE CLIENTES &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_cliente_link;?>" role="button">Crear Cliente</a></h2>
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
              <?php if($_SESSION['rolEmpleado']) { ?>
              <th>Negocio</th>
             <?php }?>
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
                  <td><?php if ($registro['link'] == "sudo_admin" || $registro['link'] == "" ) {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
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