<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
  
    $sentencia=$conexion->prepare("DELETE FROM caja WHERE caja_id=:caja_id");
    $sentencia->bindParam(":caja_id",$txtID);
    $sentencia->execute();
    header("Location:index.php");
  }

$sentencia=$conexion->prepare("SELECT * FROM `caja`");
$sentencia->execute();
$lista_caja=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
<br>
<div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title">LISTA DE CAJAS &nbsp;&nbsp;<a class="btn btn-warning"  style="color:black" href="crear.php" role="button">Crear Caja</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_cajas" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Código </th>
                <th>Nombre </th>
                <th>Efectivo </th> 
                <th>Opciones</th> 
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_caja as $registro) {?>
                <tr class="">
                <td scope="row"><?php echo $registro['caja_numero']; ?></td>
                <td><?php echo $registro['caja_nombre']; ?></td>
                <td><?php echo '$' . number_format($registro['caja_efectivo'], 0, '.', ','); ?></td>
                <td  style="text-align: center;">
                    <div class="btn-group">
                        <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['caja_id']; ?>"role="button"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger"href="index.php?txtID=<?php echo $registro['caja_id']; ?>" role="button"><i class="far fa-trash-alt"></i></a>                    
                    </div>
                  </td>
                </tr> 
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
<?php include("../../templates/footer_content.php") ?>