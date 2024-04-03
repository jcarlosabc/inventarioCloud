<?php include("../templates/header.php") ?>
<?php 

if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM producto WHERE producto_id=:producto_id");
  $sentencia->bindParam(":producto_id",$txtID);
  $sentencia->execute();
  
}
$responsable = $_SESSION['usuario_id'];
if ($responsable == 1) {
  $sentencia = $conexion->prepare("SELECT producto.*, categoria.*, e.empresa_nombre FROM producto
      INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id LEFT JOIN ( SELECT p.*, e.empresa_nombre FROM producto p 
      LEFT JOIN empresa e ON p.link = e.link) AS e ON producto.link = e.link GROUP BY producto_id");

}else { 
  $sentencia=$conexion->prepare("SELECT p.*, c.*, e.empresa_nombre
  FROM producto p LEFT JOIN categoria c ON p.categoria_id = c.categoria_id LEFT JOIN empresa e ON p.link = e.link WHERE p.link = :link");
  $sentencia->bindParam(":link",$link);
}

$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">LISTA DE PRODUCTOS &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_producto_link;?>">Crear Producto</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="listaProductos" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Código</th>
              <th>Nombre</th>
              <?php if ($_SESSION['rolSudoAdmin']) { ?> <th>Precio de compra</th> <?php } ?>
              <th>Precio de Venta</th>
              <th>Marca</th>
              <th>Modelo</th>                                    
              <th>Categoría</th>
              <th>Cantidad en Stock</th>
              <th>Garantía</th>
              <th>Empresa</th>
              <th>Editar</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_producto as $registro) {?>
                <tr class="">
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['producto_codigo']; ?></td>
                  <td><?php echo $registro['producto_nombre']; ?></td>
                  <?php if ($_SESSION['rolSudoAdmin']) { ?>
                    <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_compra'], 0, '.', ','); ?></td>
                  <?php } ?>
                  <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_venta'], 0, '.', ','); ?></td>                
                  <td><?php echo $registro['producto_marca']; ?></td>
                  <td><?php echo $registro['producto_modelo']; ?></td>
                  <td>
                    <?php if($registro['categoria_id'] == 0){ ?>
                      <article> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Este producto viene de Bodega debe <strong>Asignarle o Crearle </strong>una <strong>Categoria.</strong></article>
                    <?php } else { echo $registro['categoria_nombre']; }?>
                  </td>
                  <td><?php echo $registro['producto_stock_total']; ?></td>
                  <td><?php echo $registro['producto_fecha_garantia']; ?></td>
                  <td><?php if ($registro['link'] == "sudo_admin") {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td> 
                  <td>
                    <a class="btn btn-purple" style="background: #6f42c1; color: white;" href="ingresar_stock.php?txtID=<?php echo $registro['producto_id'];?><?php echo $link ?>" role="button" title="Añadir Stock">
                      <i class="fa fa-plus-circle"></i> Añadir Stock
                    </a>
                    <?php if ($_SESSION['rolSudoAdmin']) { ?>
                      <a class="btn btn-info" href="<?php echo $url_base;?>secciones/<?php echo $editar_producto_link . '?' . http_build_query(['data-value' => $registro['link']]); ?><?php echo '&txtID=' . $registro['producto_id']; ?>" role="button" title="Editar">
                      <i class="fas fa-edit"></i>Editar
                    <?php } else { ?>
                      <a class="btn btn-info" href="<?php echo $url_base;?>secciones/<?php echo $editar_producto_link . '&txtID=' . $registro['producto_id']; ?>" role="button" title="Editar"> 
                       <i class="fas fa-edit"></i>Editar
                   </a>
                   <?php } ?>
                    <?php if ($_SESSION['rolSudoAdmin']) { ?>
                    <a class="btn btn-danger" href="index_productos.php?txtID=<?php echo $registro['producto_id']; ?>" role="button" title="Eliminar">
                      <i class="far fa-trash-alt"></i>Eliminar 
                    </a>
                    <?php } ?>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>