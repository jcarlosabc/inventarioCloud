<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM producto WHERE producto_id=:producto_id");
  $sentencia->bindParam(":producto_id",$txtID);
  $sentencia->execute();
  
}
$sentencia=$conexion->prepare("SELECT producto.*, categoria.*
FROM producto
INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id");

$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title">LISTA DE PRODUCTOS &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/productos/crear.php">Crear Producto</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Codigo</th>
              <th>Nombre del Producto</th>
              <th>Precio de compra</th>
              <th>Precio de Venta</th>
              <th>Marca del Producto</th>
              <th>Modelo del producto</th>                                    
              <th>Categoria</th>
              <th>Cantidad en Stock</th>
              <th>Editar</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_producto as $registro) {?>
                <tr class="">
                  <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                  <td><?php echo $registro['producto_nombre']; ?></td>
                  <td><?php echo '$' . number_format($registro['producto_precio_compra'], 0, '.', ','); ?></td>
                  <td><?php echo '$' . number_format($registro['producto_precio_venta'], 0, '.', ','); ?></td>                
                  <td><?php echo $registro['producto_marca']; ?></td>
                  <td><?php echo $registro['producto_modelo']; ?></td>
                  <td><?php echo $registro['categoria_nombre']; ?></td>
                  <td><?php echo $registro['producto_stock_total']; ?></td>
                  <td>
                    <div class="btn-group">
                        <a class="btn btn-purple" style="background: #6f42c1; color: white;" href="ingresar_stock.php?txtID=<?php echo $registro['producto_id']; ?>" role="button"><i class="fa fa-plus-circle"></i></a> 
                        <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['producto_id']; ?>"role="button"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-danger"href="index.php?txtID=<?php echo $registro['producto_id']; ?>" role="button"><i class="far fa-trash-alt"></i></a>                    
                    </div>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
<?php include("../../templates/footer_content.php") ?>