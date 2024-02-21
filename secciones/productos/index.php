<?php 
include("../../db.php");

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM producto WHERE producto_id=:producto_id");
  $sentencia->bindParam(":producto_id",$txtID);
  $sentencia->execute();
  header("Location:index.php");
}


$sentencia=$conexion->prepare("SELECT producto.*, categoria.*
FROM producto
INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id");

$sentencia->execute();

$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>


<?php include("../../templates/header_content.php") ?>

<div class="card">
              <div class="card-header">
                <h2 class="card-title">LISTA DE PRODUCTOS</h2>
                
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
                <td><?php echo $registro['producto_precio_compra']; ?></td>
                <td><?php echo $registro['producto_precio_venta']; ?></td>                
                <td><?php echo $registro['producto_marca']; ?></td>
                <td><?php echo $registro['producto_modelo']; ?></td>
                <td><?php echo $registro['categoria_nombre']; ?></td>
                <td><?php echo $registro['producto_stock_total']; ?></td>

                <td>
                      <div class="btn-group">
                      <a                    
                    class="btn btn-info"
                    href="editar.php?txtID=<?php echo $registro['producto_id']; ?>"
                    role="button"
                    >Editar</a>
                            
                <a
                    class="btn btn-danger"
                    href="index.php?txtID=<?php echo $registro['producto_id']; ?>"
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