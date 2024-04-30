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
  // $sentencia = $conexion->prepare("SELECT producto.*, categoria.*, e.empresa_nombre FROM producto
  //     INNER JOIN categoria ON producto.categoria_id = categoria.categoria_id LEFT JOIN ( SELECT p.*, e.empresa_nombre FROM producto p 
  //     LEFT JOIN empresa e ON p.link = e.link) AS e ON producto.link = e.link GROUP BY producto_id");
  $sentencia=$conexion->prepare("SELECT p.*, c.*, e.empresa_nombre
  FROM producto p LEFT JOIN categoria c ON p.categoria_id = c.categoria_id LEFT JOIN empresa e ON p.link = e.link");
  // $sentencia->bindParam(":link",$link);

}else if($link != "sudo_bodega"  || $link != "sudo_admin" ) { 
  $sentencia=$conexion->prepare("SELECT p.*, c.*, e.empresa_nombre
  FROM producto p LEFT JOIN categoria c ON p.categoria_id = c.categoria_id LEFT JOIN empresa e ON p.link = e.link WHERE p.link = :link");
  $sentencia->bindParam(":link",$link);
}else {
  $sentencia=$conexion->prepare("SELECT p.*, c.*, b.bodega_nombre as empresa_nombre
  FROM producto p LEFT JOIN categoria c ON p.categoria_id = c.categoria_id LEFT JOIN empresa_bodega b ON p.link = b.link WHERE p.link = :link");
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
              <th>Marca</th>
              <th>Modelo</th>
              <th>Precio de compra</th>
              <th>Precio al Detal</th>
              <th>Precio al por mayor</th>
              <th>Categoría</th>
              <th>Cantidad en Stock</th>
              <th>Garantía</th>
              <th>Empresa</th>
              <?php if (!$_SESSION['rolUserEmpleado']) { ?> <th>Opciones</th> <?php } ?>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_producto as $registro) {?>
                <tr class="">
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['producto_codigo']; ?></td>
                  <td><?php echo $registro['producto_nombre']; ?></td>
                    <td><?php echo $registro['producto_marca']; ?></td>
                  <td><?php echo $registro['producto_modelo']; ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_compra'], 0, '.', ','); ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_venta'], 0, '.', ','); ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['producto_precio_venta_xmayor'], 0, '.', ','); ?></td>                  
                  <td>
                 <!-- corregir mostrar de donde llega el producto -->   <?php if($registro['categoria_id'] == 0){ ?> 
                  <article> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Este producto viene de Bodega debe <strong>Asignarle o Crearle </strong>una <strong>Categoria.</strong></article>
                  <?php } else { echo $registro['categoria_nombre']; }?>
                  </td>
                  <td><?php echo $registro['producto_stock_total']; ?></td>
                  <td><?php echo $registro['producto_fecha_garantia']; ?></td>
                  <td><?php if ($registro['link'] == "sudo_admin") {echo "Bodega";} else { echo $registro['empresa_nombre']; } ?></td> 
     
                   
                  <?php if (!$_SESSION['rolUserEmpleado']) { ?>
                  <td>
                    <a class="btn btn-purple btn-sm" style="background: #6f42c1; color: white;" href="ingresar_stock.php?txtID=<?php echo $registro['producto_id'];?><?php echo $link ?>" role="button" title="Añadir Stock">
                      <i class="fa fa-plus-circle"></i> 
                    </a>
                    <?php } ?>
                    <?php if ($_SESSION['rolSudoAdmin']) { ?>
                      <a class="btn btn-info btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $editar_producto_link . '?' . http_build_query(['data-value' => $registro['link']]); ?><?php echo '&txtID=' . $registro['producto_id']; ?>" role="button" title="Editar">
                      <i class="fas fa-edit"></i>
                    <?php } else if($_SESSION['roladminlocal']) { ?>
                      <a class="btn btn-info btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $editar_producto_link . '&txtID=' . $registro['producto_id']; ?>" role="button" title="Editar"> 
                       <i class="fas fa-edit"></i>
                   </a>
                   <?php } ?>
                   </a>
                   <?php if (!$_SESSION['rolSudoAdmin']) { ?>
                    <a class="btn btn-primary btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $trasladar_producto_local_link . '&txtID=' . $registro['producto_id']; ?>"role="button"title="Enviar">
                      <i class="fa fa-share"></i>
                    </a>
                    <?php } ?>
                    <?php if ($_SESSION['rolSudoAdmin']) { ?>
                    <a class="btn btn-danger btn-sm" href="index_productos.php?txtID=<?php echo $registro['producto_id']; ?>" role="button" title="Eliminar">
                      <i class="far fa-trash-alt"></i> 
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