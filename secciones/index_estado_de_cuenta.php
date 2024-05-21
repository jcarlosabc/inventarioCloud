<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
  $factura_estado_cuenta  = "detalles_estado_cuenta.php?txtID";
}else{
  $factura_estado_cuenta  = "detalles_estado_cuenta.php?link=".$link."&txtID";
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

  // Mostrando los clientes con estados de cuentas
  if($responsable == 1){
    $sentencia=$conexion->prepare("SELECT DISTINCT v.cliente_id, v.link, c.*, e.empresa_nombre FROM cliente c
    JOIN empresa e ON c.link = e.link 
    JOIN venta v ON v.cliente_id = c.cliente_id 
    WHERE v.venta_metodo_pago = 2;");
    
  }else if ($link != "sudo_bodega" && $link != "sudo_admin") {

    $sentencia=$conexion->prepare("SELECT DISTINCT v.cliente_id, v.link, c.*, e.empresa_nombre FROM cliente c
    JOIN empresa e ON c.link = e.link 
    JOIN venta v ON v.cliente_id = c.cliente_id 
    WHERE v.link = :link AND v.venta_metodo_pago = 2;");
    $sentencia->bindParam(":link", $link);

  }else {
    $sentencia=$conexion->prepare("SELECT DISTINCT v.cliente_id, v.link, c.*, e.empresa_nombre FROM cliente c
    JOIN empresa e ON c.link = e.link 
    JOIN venta v ON v.cliente_id = c.cliente_id 
    WHERE v.link = :link AND v.venta_metodo_pago = 2;");
    $sentencia->bindParam(":link", $link);
  }
  $sentencia->execute();
  $lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">ESTADO DE CUENTAS</h2>
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
                    <a class="btn btn-success btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $factura_estado_cuenta;?>=<?php echo $registro['cliente_id']; ?>"role="button" title="Editar">
                    <i class="fa fa-book" aria-hidden="true"></i>
                    </a>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>