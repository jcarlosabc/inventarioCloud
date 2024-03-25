<?php include("../templates/header.php") ?>
<?php 
//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  $sentencia=$conexion->prepare("DELETE FROM usuario WHERE usuario_id=:usuario_id");
  $sentencia->bindParam(":usuario_id",$txtID);
  $sentencia->execute();
}

$sentencia=$conexion->prepare("SELECT u.*, e.empresa_nombre, n.nomina_estado 
FROM usuario u 
LEFT JOIN empresa e ON u.link = e.link
LEFT JOIN nomina n ON u.usuario_id = n.nomina_usuario_id
GROUP BY u.usuario_id;
");
$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
        <h2 class="card-title textTabla">LISTA DE NÓMINA &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_categoria_link;?>">Crear Categoría</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_categoria" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Código de Usuario</th>
              <th>Nombre</th>
              <th>Telefono</th> 
              <th>Negocio</th>
              <th>estado de nomina</th>              
              <th>Nomina</th>       
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_producto as $registro) {?>
                <tr class="">
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['usuario_id']; ?></td>
                  <td><?php echo $registro['usuario_nombre']; ?> <?php echo $registro['usuario_apellido']; ?></td>                               
                  <td><?php echo $registro['usuario_telefono']; ?></td>                  
                  <td><?php if ($registro['link'] == "sudo_admin") {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
                  <td>
                  <?php
                    if ($registro['nomina_estado']==1) {
                        echo '<span class="badge bg-success" style="font-size: 1.2em;">Pago Realizado</span>';    
                    }else{
                        echo '<span class="badge bg-danger" style="font-size: 1.2em;">No Pagado</span>';
                    }
                    ?>
                 </td>
                  <?php if ($_SESSION['rolEmpleado']) { ?>
                    <td>
                    <a class="btn btn-success"href="nomina_pago.php?txtID=<?php echo $registro['usuario_id']; ?>" role="button" title="Nomina">
                        <i class="fa fa-list-alt"> </i> Nomina
                    </a>
                  </td>
                  <?php } ?>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
<?php include("../templates/footer.php") ?>