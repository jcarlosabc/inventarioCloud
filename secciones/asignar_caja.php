<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $crear_caja_link = 'crear_caja.php';
  $asignar_caja = "asignar_caja.php";
}else{
  $crear_caja_link = 'crear_caja.php?link='.$link;
  $asignar_caja = "asignar_caja.php?link=".$link;
}
if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}
$responsable = $_SESSION['usuario_id'];
  if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("DELETE FROM caja WHERE caja_id=:caja_id");
    $sentencia->bindParam(":caja_id",$txtID);
    $sentencia->execute();    
  }

  if($responsable == 1){
    $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM caja c LEFT JOIN empresa e ON c.link = e.link");
  }else if($link != "sudo_bodega" && $link != "sudo_admin"){
    $sentencia=$conexion->prepare("SELECT c.*, e.empresa_nombre FROM caja c JOIN empresa e ON c.link = e.link WHERE c.link = :link");
    $sentencia->bindParam(":link", $link);
  }else {
    $sentencia=$conexion->prepare("SELECT c.*, b.bodega_nombre as empresa_nombre FROM caja c JOIN empresa_bodega b ON c.link = b.link WHERE c.link = :link");
    $sentencia->bindParam(":link", $link);
  }
  $sentencia->execute();
  $lista_caja=$sentencia->fetchAll(PDO::FETCH_ASSOC);

  if(isset($_GET['AsignarID'])){
    $AsignarID=(isset($_GET['AsignarID']))?$_GET['AsignarID']:"";
    $sentencia_asignar=$conexion->prepare("UPDATE usuario SET caja_id = :AsignarID WHERE usuario_id = :usuario_id");    
    $sentencia_asignar->bindParam(":AsignarID",$AsignarID);
    $sentencia_asignar->bindParam(":usuario_id",$_SESSION['usuario_id']);
    $sentencia_asignar->execute();

    if ($sentencia_asignar) {
        echo '<script>
        Swal.fire({
          title: "Se te asignó la caja Correctamente!",
          icon: "success",
          confirmButtonText: "¡Entendido!"
      }).then((result)=>{
          if(result.isConfirmed){
            window.location.href = "'.$url_base.'secciones/'.$index_cajas_link.'";
          }
      })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            title: "Error al asignar caja",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }

}

?>
<br>
<div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">ASIGNAR CAJA &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_caja_link;?>" role="button">Crear Caja</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_cajas" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
                <th>#</th>
                <th>Código </th>
                <th>Nombre </th>
                <th>Negocio </th>
                <th>Efectivo </th> 
                <th>Opciónes</th> 
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_caja as $registro) {?>
                <tr>
                <td scope="row"><?php $count++; echo $count; ?></td>
                <td><?php echo $registro['caja_numero']; ?></td>
                <td><?php echo $registro['caja_nombre']; ?></td>
                <td><?php if ($registro['link'] == "sudo_admin" ) {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td>                  
                <td class="tdColor"><?php echo '$' . number_format($registro['caja_efectivo'], 0, '.', ','); ?></td>
                <td class="text-center">
                  <a class="btn btn-info" href="<?php echo $url_base;?>secciones/<?php echo $asignar_caja;?>&AsignarID=<?php echo $registro['caja_id']; ?>"role="button"title="Asignar">
                    <i class=""></i>Asignar
                </td>
                </tr> 
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>