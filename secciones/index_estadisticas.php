<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $lista_cliente_link  = "index_clientes.php";
  $crear_productos_link  = "crear_productos.php";
  $lista_productos_link  = "index_productos.php";
  $index_cajas_link  = "index_cajas.php";
  $crear_ventas_link  = "crear_venta.php";
  $index_ventas_link  = "index_ventas.php";
  $lista_categorias_link  = "lista_categorias.php";
  $index_pendientes_link ="index_pendientes.php";

}else{
  $lista_cliente_link  = "index_clientes.php?link=".$link;
  $crear_productos_link  = "crear_productos.php?link=".$link;
  $lista_productos_link  = "index_productos.php?link=".$link;
  $index_cajas_link  = "index_cajas.php?link=".$link;
  $crear_ventas_link  = "crear_venta.php?link=".$link;
  $index_ventas_link  = "index_ventas.php?link=".$link;
  $lista_categorias_link  = "lista_categorias.php?link=".$link;
  $index_pendientes_link = "index_pendientes.php?link=".$link;
}

$usuario_sesion = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id']  : 0;  
if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";}

//Total de Clientes Registrados
if ($usuario_sesion == 1) {
  $sentencia_clientes=$conexion->prepare("SELECT COUNT(*) as total_clientes FROM cliente WHERE cliente_id > 0");
}else {
  $sentencia_clientes=$conexion->prepare("SELECT COUNT(*) as total_clientes FROM cliente WHERE cliente_id > 0 AND link=:link");
  $sentencia_clientes->bindParam(":link", $linkeo);
}
$sentencia_clientes->execute();
$contador_usuario=$sentencia_clientes->fetchAll(PDO::FETCH_ASSOC);

//Total Productos en Stock
if ($usuario_sesion == 1) {
  $sentencia_stock=$conexion->prepare("SELECT COUNT(producto_stock_total) as total_stock FROM producto");
}else {
  $sentencia_stock=$conexion->prepare("SELECT COUNT(producto_stock_total) as total_stock FROM producto WHERE producto_id > 0 AND link = :link");
  $sentencia_stock->bindParam(":link", $linkeo);
}
$sentencia_stock->execute();
$contador_producto=$sentencia_stock->fetchAll(PDO::FETCH_ASSOC); 

//Producto mas vendido 
// if ($usuario_sesion == 1) {
//   $sentencia_stock=$conexion->prepare("SELECT COUNT(producto_stock_total) as total_stock FROM venta");
// }else {
//   $sentencia_stock=$conexion->prepare("SELECT COUNT(producto_stock_total) as total_stock FROM producto WHERE producto_id > 0 AND link = :link");
//   $sentencia_stock->bindParam(":link", $linkeo);
// }
// $sentencia_stock->execute();
// $contador_producto=$sentencia_stock->fetchAll(PDO::FETCH_ASSOC); 

//Total Ventas
if ($usuario_sesion == 1) {
  $sentencia_ventas=$conexion->prepare("SELECT COUNT(*) as total_ventas FROM venta WHERE venta_fecha = :fechaActual");
}else {
  $sentencia_ventas=$conexion->prepare("SELECT COUNT(*) as total_ventas FROM venta WHERE venta_fecha = :fechaActual AND link = :link");
  $sentencia_ventas->bindParam(":link", $linkeo);
}
$sentencia_ventas->bindParam(":fechaActual", $fechaActual);
$sentencia_ventas->execute();
$contador_ventas =$sentencia_ventas->fetchAll(PDO::FETCH_ASSOC); 

// consulta para el dinero de la caja que inicio session 
if ($usuario_sesion == 1) {

  $sentencia_dinero=$conexion->prepare("SELECT * FROM caja;");

} else if($_SESSION['roladminlocal']) {

  $sentencia_dinero=$conexion->prepare("SELECT * FROM caja WHERE link = :link;");
  $sentencia_dinero->bindParam(":link", $linkeo);
  $sentencia_dinero->execute();
  $total_dinero_caja =$sentencia_dinero->fetchAll(PDO::FETCH_ASSOC);
  $dinero_efectivo = 0;
  if ($total_dinero_caja) {
    foreach ($total_dinero_caja as $item) {
      $dinero_efectivo += $item['caja_efectivo'];
    }
  }

} else if($_SESSION['rolUserEmpleado']) {

  $sentencia_dinero=$conexion->prepare("SELECT c.* FROM caja c JOIN usuario u ON c.caja_id = u.caja_id WHERE c.link = :link AND u.usuario_id =:id;");
  $sentencia_dinero->bindParam(":link", $linkeo);
  $sentencia_dinero->bindParam(":id", $usuario_sesion);

}
 
$sentencia_dinero->execute();
$total_dinero_caja =$sentencia_dinero->fetchAll(PDO::FETCH_ASSOC);


//total de devoluciones - no se esta usando en la vista
// if ($usuario_sesion == 1) {
//   $sentencia_devoluciones=$conexion->prepare("SELECT COUNT(*) as total_devolucion FROM devolucion");

// }else {
//   $sentencia_devoluciones=$conexion->prepare("SELECT COUNT(link) as total_devolucion FROM devolucion WHERE  link = :link"); 
//   $sentencia_devoluciones->bindParam(":link", $linkeo);
//   print_r($linkeo);

// }
// $sentencia_devoluciones->execute();
// $contardor_devoluciones=$sentencia_devoluciones->fetchAll(PDO::FETCH_ASSOC);

//cuentas pendientes
if ($usuario_sesion == 1) {
  $sentencia_pendientes=$conexion->prepare("SELECT COUNT(*) as total_cuentas_pendiente_clientes FROM venta WHERE venta_metodo_pago = 2 and estado_venta = 0;");
}else {
  $sentencia_pendientes=$conexion->prepare("SELECT COUNT(*) as total_cuentas_pendiente_clientes FROM venta WHERE venta_metodo_pago = 2 and estado_venta = 0 AND link = :link;");
  $sentencia_pendientes->bindParam(":link", $linkeo);

}
$sentencia_pendientes->execute();
$lista_clientes_cuentas_pendientes=$sentencia_pendientes->fetchAll(PDO::FETCH_ASSOC); 

$total_cuentas_p_clientes = $lista_clientes_cuentas_pendientes[0]['total_cuentas_pendiente_clientes'];

//devoluciones del mes
if ($usuario_sesion == 1) {
$numero_devoluciones = $conexion->prepare("SELECT COUNT(*) AS total_devoluciones FROM devolucion WHERE MONTH(STR_TO_DATE(devolucion_fecha, '%d/%m/%Y')) = MONTH(NOW())");

}else {
$numero_devoluciones = $conexion->prepare("SELECT COUNT(*) AS total_devoluciones FROM devolucion WHERE MONTH(STR_TO_DATE(devolucion_fecha, '%d/%m/%Y')) = MONTH(NOW()) AND link = :link");
$numero_devoluciones->bindParam(":link", $linkeo);

}
$numero_devoluciones->execute();
$devoluciones_mes = $numero_devoluciones->fetch(PDO::FETCH_ASSOC);


if ($_POST) {
  
  $rad_factura = isset($_POST['rad_factura']) ? $_POST['rad_factura'] : "";
  
  if ($_SESSION['rolSudoAdmin']) {

   $sentencia=$conexion->prepare("SELECT * FROM venta WHERE venta_codigo = :venta_codigo");
  $sentencia->bindParam(":venta_codigo", $rad_factura);
  $sentencia->execute();
  $rad_factura_list =$sentencia->fetchAll(PDO::FETCH_ASSOC);

  $dato = $rad_factura_list[0]['venta_id'];
  echo $dato;
  if (isset($dato)) {
    echo '<script>
         window.location.href="'.$url_base.'secciones/detalles.php?txtID='.$dato.'";
    </script>';
  }else{
    header("Location:".$url_base);
  }

  }else {

    if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";

   $sentencia=$conexion->prepare("SELECT * FROM venta WHERE venta_codigo = :venta_codigo AND link = :link");
   $sentencia->bindParam(":venta_codigo", $rad_factura);
   $sentencia->bindParam(":link", $linkeo);
   $sentencia->execute();
    $rad_factura_list =$sentencia->fetchAll(PDO::FETCH_ASSOC);

  $dato = $rad_factura_list[0]['venta_id'];
  echo $dato;
  if (isset($dato)) {
    echo '<script>
         window.location.href="'.$url_base.'secciones/detalles.php?link='.$linkeo .'&txtID='.$dato.'";
    </script>';
  }else{
    header("Location:".$url_base);
  }


  }
}

}
?>
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
          <img class="animation__shake" src="../dist/img/logos/logo_nube.png" alt="AdminLTELogo" height="60" width="80">
        </div>
        <br>
        <div class="row">
          <!-- Cantidad de productos -->
          <?php if (!$_SESSION['rolUserEmpleado']) { ?>
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo (isset( $contador_producto[0]['total_stock']))?$contador_producto[0]['total_stock']: 0;?></h3>
                  <p>Productos</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="<?php echo $url_base;?>secciones/<?php echo $lista_productos_link;?>" class="small-box-footer">Ver Productos<i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          <?php } ?>
          <!-- Clientes registrados -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner" style="color: white !important">
                <h3 ><?php echo (isset($contador_usuario[0]['total_clientes']))?$contador_usuario[0]['total_clientes']: 0 ;?></h3>
                <p>Clientes Registrados</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a style="color: white !important" href="<?php echo $url_base;?>secciones/<?php echo $lista_cliente_link;?>" class="small-box-footer">Ver Clientes <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- Ventas del dia -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo (isset( $contador_ventas[0]['total_ventas']))?$contador_ventas[0]['total_ventas']: 0;?></h3>
              <?php if($_SESSION['rolUserEmpleado']) { ?> <p>Ventas del día hechas por mi</p> <?php } else { echo "Total Ventas del día"; } ?>

              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="<?php echo $url_base;?>secciones/<?php echo $index_ventas_link;?>" class="small-box-footer">Ver Ventas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- Cuentas Pendientes -->
          <div class="col-lg-3 col-6">
            <div class="small-box" style="background:#c53fbb !important">
              <div class="inner" style="color: white !important">
                <h3 ><?php echo isset($total_cuentas_p_clientes) ? $total_cuentas_p_clientes : 0 ?></h3>
                <p>Cuentas Pendientes</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a style="color: white !important" href="<?php echo $url_base;?>secciones/<?php echo $index_pendientes_link;?>" class="small-box-footer">Ver Cuentas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- Dinero en Caja -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
              <h3>
                <?php if($_SESSION['roladminlocal']) { echo '$' . number_format($dinero_efectivo, 0, '.', ','); } else { echo '$' . number_format($total_dinero_caja[0]['caja_efectivo'], 0, '.', ',') ;}?>
              </h3>
              <?php if($_SESSION['rolUserEmpleado']) { ?> <p>Efectivo en la caja</p> <?php } else { echo "Total Efectivo en las cajas"; } ?>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>
          <!-- Cantidad Devoluciones -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
              <h3><?php echo $devoluciones_mes['total_devoluciones']; ?></h3>
                <p>Devoluciones Realizadas por Mes  </p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>
          <!-- Buscar Factura -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h5 style="text-align:center">Buscar Factura</h5>
                  <form action="" method="post" class="text-center">
                    <div class="row" style="justify-content:center">
                      <div class="col-6">
                        <input type="text" class="form-control " name="rad_factura" >
                      </div>
                    </div>
                    <button  class="btn btn-secondary text-center mt-2" type="submit">Ir a la factura</button>  
                  </form>
                </div>
              </div>
          </div>
        </div>
        <div class="row">
          <section class="col-lg-5 connectedSortable">      
            <!-- TO DO List -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ion ion-clipboard mr-1" style="font-size: 40px;"></i>
                  <h2 style="font-size: 26px;">Atajos </h2>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list">
                  <li>
                    <i class="fa fa-cart-plus" aria-hidden="true" style="font-size: 24px;"></i>
                    <?php if (!$_SESSION['rolSudoAdmin']) { ?>
                      <a href="<?php echo $url_base;?>secciones/<?php echo $crear_ventas_link;?>"><h4 class="text">Vender</h4></a>&nbsp;
                      ||
                    <?php } ?>
                    <a href="<?php echo $url_base;?>secciones/<?php echo $index_ventas_link;?>"><h4 class="text">Hitorial de Ventas</h4></a>&nbsp;
                  </li>
                  <li>
                    <i class="fa fa-shopping-basket" aria-hidden="true" style="font-size: 24px;"></i>
                    <?php if ($_SESSION['roladminlocal'] || $_SESSION['rolSudoAdmin']) { ?>
                      <a href="<?php echo $url_base;?>secciones/<?php echo $lista_productos_link;?>"><h4 class="text">Crear Producto</h4></a>&nbsp;
                      ||
                    <?php } ?>
                    <a href="<?php echo $url_base;?>secciones/<?php echo $lista_productos_link;?>"><h4 class="text">Lista de Productos</h4></a>&nbsp;
                  </li>
                  <li>
                    <i class="fas fa-cash-register" aria-hidden="true" style="font-size: 24px;"></i>
                    <a href="<?php echo $url_base;?>secciones/<?php echo $crear_cliente_link;?>"><h4 class="text">Registrar Cliente</h4></a>&nbsp;
                    ||
                    <a href="<?php echo $url_base;?>secciones/<?php echo $lista_cliente_link;?>"><h4 class="text">Lista de Clientes</h4></a>&nbsp;
                  </li>
                  <li>
                    <i class="fa fa-retweet" aria-hidden="true" style="font-size: 24px;"></i>
                    <a href="<?php echo $url_base;?>secciones/<?php echo $crear_categoria_link;?>"><h4 class="text">Crear Categoría</h4></a>&nbsp;
                    ||
                    <a href="<?php echo $url_base;?>secciones/<?php echo $lista_categoria_link;?>"><h4 class="text">Lista de Categorías</h4></a>&nbsp;
                  </li>
                </ul>
              </div>
            </div>
          </section>
      
<?php include("../templates/footer.php") ?>