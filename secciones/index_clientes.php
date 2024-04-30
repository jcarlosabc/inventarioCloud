<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
  $crear_cliente_link  = "crear_cliente.php";
  $editar_clientes  = "editar_clientes.php?txtID";
  $lista_cliente_link  = "index_clientes.php?txtID";


}else{
  $crear_cliente_link  = "crear_cliente.php?link=".$link;
  $editar_clientes  = "editar_clientes.php?link=".$link."&txtID";
  $lista_cliente_link  = "index_clientes.php?link=".$link."&txtID";
}

if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}
$responsable = $_SESSION['usuario_id'];
if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("DELETE FROM cliente WHERE cliente_id=:cliente_id");
    $sentencia->bindParam(":cliente_id",$txtID);
    $sentencia->execute();
  }
  if($responsable == 1){
    $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM cliente c LEFT JOIN empresa e ON c.link = e.link WHERE c.cliente_id > 0");
  }else if ($link != "sudo_bodega" && $link != "sudo_admin") {
    $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM cliente c JOIN empresa e ON c.link = e.link WHERE c.cliente_id > 0 AND c.link = :link");
    $sentencia->bindParam(":link", $link);
  }else {
    $sentencia=$conexion->prepare("SELECT c.*, b.bodega_nombre as empresa_nombre FROM cliente c JOIN empresa_bodega b ON c.link = b.link WHERE c.cliente_id > 0 AND c.link = :link");
    $sentencia->bindParam(":link", $link);
  }
  $sentencia->execute();
  $lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);

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
              <th>Empresa</th>
              <th>Nit</th>
              <th>Nombres / Apellidos</th>
              <th>Ciudad</th>
              <th>Dirección</th>                                    
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Negocio</th>
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_cliente as $registro) {?>
                <tr>
                  <td scope="row"><?php $count++;  echo $count; ?></td>
                  <td>
                    <?php echo $registro['cliente_empresa'] ? $registro['cliente_empresa'] : "N/A" ?>
                  </td>
                  <td><?php echo $registro['cliente_nit']; ?></td>
                  <td><?php echo $registro['cliente_nombre']; ?> <?php echo $registro['cliente_apellido']; ?></td>
                  <td><?php echo $registro['cliente_ciudad']; ?></td>                
                  <td><?php echo $registro['cliente_direccion']; ?></td>
                  <td><?php echo $registro['cliente_telefono']; ?></td>
                  <td><?php echo $registro['cliente_email']; ?></td>
                  <td><?php if ($registro['link'] == "sudo_admin" ) {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
                  <td>
                    <a class="btn btn-info btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $editar_clientes;?>=<?php echo $registro['cliente_id']; ?>"role="button" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a class="btn btn-danger btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $lista_cliente_link;?>=<?php echo $registro['cliente_id']; ?>" role="button" title="Eliminar">
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