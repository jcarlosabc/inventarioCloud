<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $crear_caja_link = 'crear_caja.php';  /// actualizar mas delante los links
}else{
  $crear_caja_link = 'crear_caja.php?link='.$link;       
}

  if(isset($_GET['txtID'])){    
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("DELETE FROM bodega WHERE producto_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();    
  }

  $sentencia_productos=$conexion->prepare("SELECT * FROM bodega");
  $sentencia_productos->execute();
  $producto_bodega=$sentencia_productos->fetchAll(PDO::FETCH_ASSOC);

?>
<br>
<div class="card card-primary">
        <div class="card-header" style="background: #493a3be0">
          <h2 class="card-title textTabla">PRODUCTOS DE LA BODEGA &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_producto_link;?>" role="button">Crear producto</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="producto_bodega" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
                <th>#</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Marca </th> 
                <th>Modelo</th> 
                <th>Stock </th>
                <th>Precio Compra </th> 
                <th>Precio al Detal</th> 
                <th>Precio al Mayor</th> 
               
                <th>Opciones</th> 
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($producto_bodega as $registro) {?>
                <tr>
                <td scope="row"><?php $count++; echo $count; ?></td>
                <td><?php echo $registro['producto_codigo']; ?></td>
                <td><?php echo $registro['producto_nombre']; ?></td>
                    <td><?php echo $registro['producto_marca']; ?></td>
                <td><?php echo $registro['producto_modelo']; ?></td>
                <td><?php echo $registro['producto_stock_total']; ?></td>
                <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_compra'], 0, '.', ','); ?></td>
                <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_venta'], 0, '.', ','); ?></td>
                <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_venta_xmayor'], 0, '.', ','); ?></td>
                <td class="text-center">
                  <a class="btn btn-purple btn-sm" style="background: #6f42c1; color: white;" href="ingresar_stock_bodega.php?link=sudo_bodega&txtID=<?php echo $registro['producto_id'];?><?php echo $link ?>" role="button" title="Ingresar Stock">
                    <i class="fa fa-plus-circle"></i>
                  </a>
                  <a class="btn btn-info btn-sm" href="editar_producto_bodega.php?link=sudo_bodega&txtID=<?php echo $registro['producto_id']; ?>"role="button"title="Editar">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a class="btn btn-primary btn-sm" href="trasladar_producto_bodega.php?link=sudo_bodega&txtID=<?php echo $registro['producto_id']; ?>"role="button"title="Enviar">
                    <i class="fa fa-share"></i>
                  </a>
                  <a class="btn btn-danger btn-sm"href="producto_bodega.php?link=sudo_bodega&txtID=<?php echo $registro['producto_id']; ?>" role="button"title="Eliminar">
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