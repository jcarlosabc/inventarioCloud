<?php include("../templates/header.php") ?>
<?php 
error_reporting(E_ERROR | E_PARSE);$caja_id = $_SESSION['caja_id'];
// die(print_r($caja_id));
include("../../db.php");
//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM carrito WHERE id=:id");
  $sentencia->bindParam(":id",$txtID);
  $sentencia->execute();

  // Mostrar el carrito
  $sentencia=$conexion->prepare("SELECT id, producto_codigo, cantidad, producto_id, producto, precio, marca, modelo FROM carrito WHERE estado=0");
  $sentencia->execute();
  $lista_carrito=$sentencia->fetchAll(PDO::FETCH_ASSOC);
}

// Mostrar datos de la tabla 
$sentencia=$conexion->prepare("SELECT * FROM `producto`");
$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia=$conexion->prepare("SELECT * FROM `categoria`");
$sentencia->execute();
$lista_categoria=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia=$conexion->prepare("SELECT * FROM `cliente` WHERE cliente_id > 0");
$sentencia->execute();
$lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);
// ===========================================================

// Guardar productos escogidos al carrito
if(isset($_POST['producto_seleccionado'])) {
    $producto_seleccionado_encoded = $_POST['producto_seleccionado'];
    $producto_seleccionado = unserialize(base64_decode($producto_seleccionado_encoded));
    $producto_id = $producto_seleccionado['producto_id'];
    $producto_codigo = $producto_seleccionado['producto_codigo'];
    $producto_nombre = $producto_seleccionado['producto_nombre'];
    $producto_precio_venta = $producto_seleccionado['producto_precio_venta'];
    $producto_marca = $producto_seleccionado['producto_marca'];
    $producto_modelo = $producto_seleccionado['producto_modelo']; 

    $sql = "INSERT INTO carrito (producto_codigo, producto_id, producto, precio, marca, modelo) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
        $params = array(
        $producto_codigo, 
        $producto_id, 
        $producto_nombre, 
        $producto_precio_venta,
        $producto_marca, 
        $producto_modelo
    );
    $sentencia->execute($params);

    // Mostrar el carrito
    $sentencia=$conexion->prepare("SELECT * FROM carrito WHERE estado = 0");
    $sentencia->execute();
    $lista_carrito=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    $total = 0; 
    foreach ($lista_carrito as $item) {
        $precio = $item['precio'];
        $total += $precio;
    }
}

if(isset($_POST['productos_vendidos'])) {
    $cantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : array();
    $totales = isset($_POST['total']) ? $_POST['total'] : array();
    $totales = str_replace(array('$','.', ','), '', $totales);
    $codigo_factura = isset($_POST['codigo_factura']) ? $_POST['codigo_factura'] : $_POST['codigo_factura'];
    $total_dinero = isset($_POST['total_dinero']) ? $_POST['total_dinero'] : $_POST['total_dinero'];
    $total_dinero = str_replace(array('$','.', ','), '', $total_dinero);
    $recibe_dinero = isset($_POST['recibe_dinero']) ? $_POST['recibe_dinero'] : $_POST['recibe_dinero'];
    $recibe_dinero = str_replace(array('$','.', ','), '', $recibe_dinero);
    $cambio_dinero = isset($_POST['cambio_dinero']) ? $_POST['cambio_dinero'] : $_POST['cambio_dinero'];
    $cambio_dinero = str_replace(array('$','.', ','), '', $cambio_dinero);
    $cliente_id = isset($_POST['cliente_id']) ? $_POST['cliente_id'] : $_POST['cliente_id'];
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : $_POST['metodo_pago'];
    $estado_ventas = 0;
    if ($metodo_pago == 0 || $metodo_pago == 1) {
        $estado_ventas = 1;
    }
    $partes = isset($_POST['partes']) ? $_POST['partes'] : $_POST['partes'];
   
    $user_id = $_SESSION['usuario_id'];
    $venta_realizada = false;

    $sql = "INSERT INTO venta (venta_codigo, venta_fecha, venta_hora, venta_total, venta_pagado, venta_cambio, 
                        venta_metodo_pago, partes, cliente_id, caja_id, responsable, estado_venta) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
            $codigo_factura, 
            $fechaActual, 
            $horaActual, 
            $total_dinero,
            $recibe_dinero, 
            $cambio_dinero, 
            $metodo_pago,
            $partes,
            $cliente_id,
            $caja_id,
            $user_id,
            $estado_ventas
    );
    $sentencia->execute($params);   
    // Obtener el ID de la última fila afectada
    $ultimo_id_insertado = $conexion->lastInsertId();

    foreach ($cantidades as $id => $cantidad) {
        $total = $totales[$id] ?? 0;

        // Agregando al carrito los productos
        $sql = "UPDATE carrito SET cantidad = ?, total = ?, estado = ?, responsable = ? WHERE id = ?";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $cantidad, 
                    $total, 
                    1,  
                    $user_id,
                    $id  
                );
               $sentencia->execute($params);

        // Obteniendo la cantidad y id producto vendidos
        $sentencia_carrito = $conexion->prepare("SELECT id, cantidad, producto_id, producto FROM carrito WHERE id= $id");
        $sentencia_carrito->execute();
        $row_carrito = $sentencia_carrito->fetch(PDO::FETCH_ASSOC);
        $producto_id = $row_carrito['producto_id'];
        $cantidad_vendida = $row_carrito['cantidad'];
        $producto = $row_carrito['producto'];
        $id_carrito = $row_carrito['id'];

        // Buscandolos en la tabla productos para restar stock
        $sentencia_producto = $conexion->prepare("SELECT producto_stock_total, producto_precio_compra, producto_precio_venta FROM producto WHERE producto_id = :producto_id");
        $sentencia_producto->bindParam(":producto_id", $producto_id);
        $sentencia_producto->execute();

        $row_producto = $sentencia_producto->fetch(PDO::FETCH_ASSOC);
        $producto_stock_total = $row_producto['producto_stock_total'];
        $producto_precio_compra = $row_producto['producto_precio_compra'];
        $producto_precio_venta = $row_producto['producto_precio_venta'];
        $total_stock = $producto_stock_total - $cantidad_vendida;

        // Actualizando stock en el inventario 
        $sql = "UPDATE producto SET producto_stock_total = ? WHERE producto_id = ?";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $total_stock, 
                    $producto_id
                );
                $sentencia->execute($params);
              

        // Insertando en ventas detalles
        $sql = "UPDATE producto SET producto_stock_total = ? WHERE producto_id = ?";
        $sentencia = $conexion->prepare($sql);
        $params = array(
            $total_stock, 
            $producto_id
        );
       $sentencia->execute($params);
       
    //    Guardando el detalle de la venta
       $sql = "INSERT INTO venta_detalle (venta_detalle_cantidad,
                venta_detalle_precio_compra, venta_detalle_precio_venta, venta_detalle_total, venta_detalle_metodo_pago,
                venta_detalle_descripcion, venta_codigo, producto_id, responsable) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
            $cantidad_vendida, 
            $producto_precio_compra, 
            $producto_precio_venta, 
            $total,
            $metodo_pago, 
            $producto, 
            $codigo_factura,
            $producto_id,
            $user_id
            );
            $sentencia->execute($params);

        // Borrar los datos de carrito una ves se haya realizado el proceso anterior
            $sql = "DELETE FROM carrito WHERE estado = ? AND responsable= ?";
            $sentencia = $conexion->prepare($sql);
            $params = array(
                1, 
                $user_id
            );
           $sentencia->execute($params);


    }
   
    // Validando que todo el proceso fue exitoso
    $venta_realizada = true;
  

    // Redireccionar a la vista de detalle de ventas para generar factura
    if ($venta_realizada) {
        if ($metodo_pago == 0) {
            echo '<script>
            Swal.fire({
                title: "¡Venta Realizada Exitosamente!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href="'.$url_base.'secciones/detalles.php?txtID='.$ultimo_id_insertado.'";
                }
            })
            </script>';
        } else if($metodo_pago == 1){
            echo '<script>
            Swal.fire({
                title: "¡Venta Exitosa: recuerda que el dinero lo tendras en la cuenta del local!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href="'.$url_base.'secciones/detalles.php?txtID='.$ultimo_id_insertado.'";
                }
            })
            </script>'; 
        } else {
            echo '<script>
            Swal.fire({
                title: "¡Venta Exitosa a Credito en: ' . $partes .' partes!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href="'.$url_base.'secciones/detalles.php?txtID='.$ultimo_id_insertado.'";
                }
            })
            </script>'; 
        }
    } else {
        echo '<script>
        Swal.fire({
            title: "Error en la Venta",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    
  
    }
}

?>
<br>
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title textTabla" >REALIZAR VENTA</h3>
        </div>
        <div class="card-body ">
            <div class="card card-info">
                <div class="card-body">
                    <table id="vBuscar" class="table table-bordered table-striped" style="text-align:center">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Existencias</th>
                                <th>Precio</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Escoger</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php error_reporting(E_ERROR | E_PARSE); foreach ($lista_producto as $registro) {?>
                                <tr>
                                    <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                                    <td><?php echo $registro['producto_nombre']; ?></td>
                                    <td>
                                        <?php if ($registro['producto_stock_total'] == 2) {?>
                                            <span class="text-danger"> El producto está por agotar existencias</span>
                                            <br>
                                            <span class="text-info">Comuníquese con su proveedor, quedan:  </span>
                                        <?php }else if($registro['producto_stock_total'] == 0) { ?>
                                            <span class="text-danger"> Producto Agotado, quedan:  </span>
                                        <?php } ?>
                                        <?php  echo $registro['producto_stock_total']; ?></td>
                                    <td class="tdColor"><?php echo '$ ' . number_format($registro['producto_precio_venta'], 0, '.', ','); ?></td>
                                    <td><?php echo $registro['producto_marca']; ?></td>
                                    <td><?php echo $registro['producto_modelo']; ?></td>
                                    <td>
                                        <form action="crear_venta.php" method="POST">
                                            <input type="hidden" name="producto_id" value="<?php echo $registro['producto_id']; ?>">
                                            <input type="hidden" name="producto_codigo" value="<?php echo $registro['producto_codigo']; ?>">
                                            <?php if($registro['producto_stock_total'] == 0) { ?>
                                                <button type="button" class="btn btn-warning">Agotado</button>
                                            <?php }else { ?>
                                                <button type="submit" name="producto_seleccionado" value="<?php echo base64_encode(serialize($registro)); ?>" class="btn btn-primary">Escoger <i class="fas fa-chevron-right"></i></button>
                                            <?php } ?>
                                        </form>
                                    </td>
                                </tr>  
                            <?php } ?>
                        </tbody>                  
                    </table>
                </div>
            </div>
            <br>
            <!-- formulario -->
            <form method="post" action="">
                <div class="row">
                    <div class="col-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title textTabla">PRODUCTOS A COMPRAR</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped" style="text-align:center">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Cantidad</th>
                                            <th>X</th>
                                            <th>Precio</th>
                                            <th>=</th>
                                            <th>Total</th>
                                            <th>Remover</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lista_carrito as $registro) {?>
                                            <tr>
                                                <input type="hidden" value="<?php echo $registro['id']; ?>">
                                                <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                                                <td><?php echo $registro['producto']; ?></td>
                                                <td><?php echo $registro['marca']; ?></td>
                                                <td><?php echo $registro['modelo']; ?></td>
                                                <td><input style="width: 63px" type="number" class="cantidad-input" name="cantidad[<?php echo $registro['id']; ?>]" value="<?php echo $registro['cantidad']; ?>"></td>
                                                <td>X</td>
                                                <td style="font-weight: 800;"><?php echo number_format($registro['precio'], 0, '.', ','); ?></td>
                                                <td>=</td>
                                                <td class="total-column" style="color:#14af37;font-weight: 800;"></td>
                                                <td><input type="hidden" class="total-input" name="total[<?php echo $registro['id']; ?>]" value="">
                                                    <div class="btn-group">
                                                        <a class="btn btn-danger btn-sm" href="crear_venta.php?txtID=<?php echo $registro['id']; ?>" role="button"><i class="far fa-trash-alt"></i></a>                    
                                                    </div>
                                                </td>
                                            </tr>  
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <br>    
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title textTabla">DETALLES</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="textLabel">Métodos de Pago</label> 
                                            <div class="form-group">
                                                <select class="form-control camposTabla" id="metodoPago" name="metodo_pago" onchange="mostrarOcultarPartes()">                                    
                                                    <option value="0" style="color:green">Efectivo</option> 
                                                    <option value="1" style="color:blue">Transferencia</option> 
                                                    <option value="2" style="color:#fb7e35">A Crédito</option> 
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <style>
                                        #partes {
                                            display: none;
                                        }
                                        span.select2-selection.select2-selection--single{
                                            height: 38px;
                                        }
                                        </style>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <span id="partes"> Número de Cuotas: <br><input type="number" required name="partes" value="0"></span>
                                            <label class="textLabel">Cliente</label> 
                                            <select class="form-control select2" name="cliente_id" style="height: 20px">
                                                <option value="0">Público General </option> 
                                                <?php foreach ($lista_cliente as $registro) {?>   
                                                    <option value="<?php echo $registro['cliente_id']; ?>"><?php echo $registro['cliente_nombre']; echo " "; echo $registro['cliente_numero_documento']; ?></option> 
                                                <?php } ?>                                    
                                            </select>  
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                        <label class="textLabel">Fecha de Venta</label> 
                                            <input type="text" class="form-control" style="text-align:center;font-weight:600" readonly value="<?php echo $fechaActual ?>">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="row">
                                    <div class="col-4">
                                        <label class="textLabel">Total</label>
                                        <input type="text" class="form-control camposTabla_dinero campo-total-global" name="total_dinero" readonly>
                                    </div>
                                    <div class="col-4">
                                        <label for="recibido" class="textLabel">Recibido</label>
                                        <input type="text" class="form-control camposTabla_dinero" name="recibe_dinero" id="recibido" required>
                                    </div>
                                    <div class="col-4">
                                        <label class="textLabel">Se devuelve</label>
                                        <input type="text" class="form-control camposTabla_dinero se_devuelve" name="cambio_dinero" readonly>
                                    </div>
                                    <input type="hidden" id="generador_codigo_factura" name="codigo_factura">
                                </div>
                                <br>
                                <div class="row" style="justify-content:center">
                                    <div class="col-4">
                                        <button type="submit" name="productos_vendidos" class="btn btn-success btn-lg">Realizar Veta <i class="fa fa-shopping-cart" aria-hidden="true"></i> </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php include("../templates/footer.php") ?>