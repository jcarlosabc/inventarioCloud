<?php include("../templates/header.php") ?>
<?php 

$responsable = $_SESSION['usuario_id'];
  if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("DELETE FROM empresa WHERE empresa_id=:empresa_id");
    $sentencia->bindParam(":empresa_id",$txtID);
    $sentencia->execute();    
  }

 
  $sentencia=$conexion->prepare("SELECT * FROM empresa");
  
  $sentencia->execute();
  $lista_caja=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
<br>
<div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE LOCALES &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_caja_link;?>" role="button">Crear Caja</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_cajas" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
                <th>#</th>
                <th>Local </th>
                <th>Telefono </th>
                <th>Direccion </th> 
                <th>Nit</th>
                <th>Codigo de seguridad</th>  
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_caja as $registro) {?>
                <tr>
                <td scope="row"><?php $count++; echo $count; ?></td>
                <td><?php echo $registro['empresa_nombre']; ?></td>
                <td><?php echo $registro['empresa_telefono']; ?></td>
                <td><?php echo $registro['empresa_direccion']; ?></td>
                <td><?php echo $registro['empresa_nit']; ?></td>
                <td><?php echo $registro['codigo_seguridad']; ?></td>
                <td class="text-center">
                  <a class="btn btn-info" href="editar_empresa.php?txtID=<?php echo $registro['empresa_id']; ?>"role="button"title="Editar">
                    <i class="fas fa-edit"></i>Editar
                  </a>
                  <a class="btn btn-danger"href="index_empresas.php?txtID=<?php echo $registro['empresa_id']; ?>" role="button"title="Eliminar">
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