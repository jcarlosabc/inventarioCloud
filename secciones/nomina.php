<?php include("../templates/header.php") ?>
<?php 
//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $sentencia=$conexion->prepare("DELETE FROM usuario WHERE usuario_id=:usuario_id");
  $sentencia->bindParam(":usuario_id",$txtID);
  $sentencia->execute();
}
$crear_nomina  = "crear_nomina.php";

$sentencia=$conexion->prepare("SELECT n.*, u.*, e.empresa_nombre
FROM nomina n JOIN usuario u ON n.nomina_usuario_id = u.usuario_id JOIN empresa e ON n.link = e.link");
$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
        <h2 class="card-title textTabla">LISTA DE NÓMINA &nbsp;</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_categoria" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Cédula</th>
              <th>Nombres / Apellidos</th>
              <th>Teléfono</th> 
              <th>Monto</th>
              <th>Adelanto</th> 
              <th>Negocio</th>
              <th>Estado</th>
              <th>Fecha</th>              
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_producto as $registro) {?>
                <tr>
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['usuario_cedula']; ?></td>
                  <td><?php echo $registro['usuario_nombre']; ?> <?php echo $registro['usuario_apellido']; ?></td>                               
                  <td><?php echo $registro['usuario_telefono']; ?></td>                  
                  <td class="tdColor"><?php echo '$ ' . number_format($registro['nomina_cantidad'], 0, '.', ',') ?></td>    
                  <td class="tdColor text-warning"><?php echo '$ ' . number_format($registro['nomina_prestamo'], 0, '.', ',') ?></td>                
                  <td><?php if ($registro['link'] == "sudo_admin") {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
                  <td>
                  <?php
                     if ($registro['nomina_estado']==2) {
                        echo '<span class="badge bg-warning" style="font-size: 15px;">Prestamo</span>';    
                     }else if ($registro['nomina_estado']==1) {
                        echo '<span class="badge bg-success" style="font-size: 15px;">Pagado</span>';    
                    }else{
                        echo '<span class="badge bg-danger" style="font-size: 15px;">No Pagado</span>';
                    }
                    ?>
                 </td>
                 <td><?php echo $registro['nomina_fecha'] . "/ " . $registro['nomina_hora']; ?></td>                               
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
<?php include("../templates/footer.php") ?>