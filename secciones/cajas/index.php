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

<div class="card">
              <div class="card-header">
                <h2 class="card-title">LISTA DE CAJAS</h2>
                
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                   
                    <th>Numero de la Caja</th>
                    <th>Nombre de la Caja</th>
                    <th>Efectivo en Caja</th>                   
                    
                  </tr>
                  </thead>
                  <tbody>

            <?php foreach ($lista_caja as $registro) {?>
                <tr class="">
                <td scope="row"><?php echo $registro['caja_numero']; ?></td>
                <td><?php echo $registro['caja_nombre']; ?></td>
                <td><?php echo $registro['caja_efectivo']; ?></td>
                
                <td>
                      <div class="btn-group">
                      <a                    
                    class="btn btn-info"
                    href="editar.php?txtID=<?php echo $registro['caja_id']; ?>"
                    role="button"
                    >Editar</a>
                            
                <a
                    class="btn btn-danger"
                    href="index.php?txtID=<?php echo $registro['caja_id']; ?>"
                    role="button"
                    >Eliminar</a>                    
         
                      </div>
                    </td>
              </tr>  
            <?php } ?>
                  
                  </tbody>                  
                </table>
              </div>
              <!-- /.card-body -->
            </div>








<?php include("../../templates/footer_content.php") ?>