<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
    $ventas_link = "crear_venta_bodega.php";
    $ventas_detalles_link = "detalles.php";
 }else{
    $ventas_link = "crear_venta_bodega.php?link=sudo_bodega";
    $ventas_detalles_link = "detalles.php?link=sudo_bodega";
 }
if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";}
//No Caja Asignada
$sentencia_caja = $conexion->prepare("SELECT caja_id FROM usuario WHERE usuario_id = :usuario_id");
$sentencia_caja->bindParam(":usuario_id", $_SESSION['usuario_id']);
$sentencia_caja->execute();
$caja_usuario = $sentencia_caja->fetch(PDO::FETCH_ASSOC); 
if($caja_usuario['caja_id'] == 0){$noSeller = true ;} else {$caja_id = $caja_usuario['caja_id'] ;}

//Eliminar Elementos
if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM carrito WHERE id = :id AND link = :link");
  $sentencia->bindParam(":id",$txtID);
  $sentencia->bindParam(":link",$link);
  $sentencia->execute();

  // Mostrar el carrito
  $sentencia=$conexion->prepare("SELECT id, producto_codigo, cantidad, producto_id, producto, precio, marca, modelo FROM carrito WHERE estado = 0 AND link = :link");
  $sentencia->bindParam(":link",$link);
  $sentencia->execute();
  $lista_carrito=$sentencia->fetchAll(PDO::FETCH_ASSOC);
}
    // Mostrando los productos del local
    $sentencia=$conexion->prepare("SELECT * FROM bodega WHERE link = :link");
    $sentencia->bindParam(":link", $linkeo);
    $sentencia->execute();
    $lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Mostrando los clientes del local
    $sentencia=$conexion->prepare("SELECT * FROM cliente WHERE cliente_id > 0 AND link = :link");
    $sentencia->bindParam(":link", $linkeo);
    $sentencia->execute();
    $lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);

// ===========================================================

// Guardar productos escogidos, al carrito
if(isset($_POST['producto_seleccionado'])) {
    $linkeo = isset($_POST['link']) ? $_POST['link'] : $_POST['link'];
    $producto_seleccionado_encoded = $_POST['producto_seleccionado'];
    $producto_seleccionado = unserialize(base64_decode($producto_seleccionado_encoded));
    $producto_id = $producto_seleccionado['producto_id'];
    $producto_codigo = $producto_seleccionado['producto_codigo'];
    $producto_nombre = $producto_seleccionado['producto_nombre'];
    $producto_precio_venta = $producto_seleccionado['producto_precio_venta'];
    $producto_marca = $producto_seleccionado['producto_marca'];
    $producto_modelo = $producto_seleccionado['producto_modelo'];
    
    // Guardando en carrito los productos escogidos
    $sql = "INSERT INTO carrito (producto_codigo, producto_id, producto, precio, marca, modelo, link) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
        $params = array(
        $producto_codigo, 
        $producto_id, 
        $producto_nombre, 
        $producto_precio_venta,
        $producto_marca, 
        $producto_modelo,
        $linkeo
    );
    $sentencia->execute($params);

    // Mostrar el carrito para dar a conocer los productos escogidos a vender
    $sentencia=$conexion->prepare("SELECT * FROM carrito WHERE estado = 0 AND link = :link");
    $sentencia->bindParam(":link", $linkeo);
    $sentencia->execute();
    $lista_carrito=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    $total = 0; 
    foreach ($lista_carrito as $item) {
        $precio = $item['precio'];
        $total += $precio;
    }
}

// Realizando venta
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
    $linkeo_venta = isset($_POST['link_venta']) ? $_POST['link_venta'] : $_POST['link_venta'];
    
    $estado_ventas = 0;
    if ($metodo_pago == 0 || $metodo_pago == 1 || $metodo_pago == 3) {
        $estado_ventas = 1;
    }
    if ($metodo_pago == 1) {
        $transferenciaMetodo = isset($_POST['transferenciaMetodo']) ? $_POST['transferenciaMetodo'] : $_POST['transferenciaMetodo'];
    }else {
        $transferenciaMetodo = "";
    }
    $creditoAbono_efectivo = false;
    $metodo_credito = 0;
    if ($metodo_pago == 2) {
        $metodo_credito = 1;
        $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
        if($tipoAbono == 2){$creditoAbono_efectivo == true;}
    }

    $plazo = isset($_POST['plazo']) ? $_POST['plazo'] : $_POST['plazo'];
    $tiempo = isset($_POST['tiempoDiasMeses']) ? $_POST['tiempoDiasMeses'] : $_POST['tiempoDiasMeses'];
   
    $user_id = $_SESSION['usuario_id'];
    $venta_realizada = false;

    // Buscando caja del vendedor actual
    $sentencia=$conexion->prepare("SELECT * FROM caja WHERE caja_id = :caja_id");
    $sentencia->bindParam(":caja_id",$caja_id);
    $sentencia->execute();
    $result_caja=$sentencia->fetch(PDO::FETCH_LAZY);

    if ($result_caja) {
      $result_cajaId = $result_caja['caja_id'];
      // efectivo
      $caja_efectivo = $result_caja['caja_efectivo'];
      $caja_efectivo = $caja_efectivo + $recibe_dinero;
      // davivienda
      $caja_davivienda = $result_caja['davivienda'];
      $caja_davivienda = $caja_davivienda + $recibe_dinero;
      // bancolombia
      $caja_bancolombia = $result_caja['bancolombia'];
      $caja_bancolombia = $caja_bancolombia + $recibe_dinero;
      // nequi
      $caja_nequi = $result_caja['nequi'];
      $caja_nequi = $caja_nequi + $recibe_dinero;
    }

    // Pagando con efectivo sin Credito
    if ($metodo_pago == 0) {
        // Actualizando el dinero de la caja Efectivo
        $sql = "UPDATE caja SET caja_efectivo = ? WHERE caja_id = ? AND link = ? ";
        $sentencia = $conexion->prepare($sql);
        $params = array($caja_efectivo, $result_cajaId, $linkeo );
        $sentencia->execute($params);

    }else // Pagando con transaccion sin Credito
      if($metodo_pago == 1){
        $transferenciaMetodo= isset($_POST['transferenciaMetodo']) ? $_POST['transferenciaMetodo'] : "";

        if ($transferenciaMetodo == 00) {
          // Actualizando el dinero de la caja Davivienda
          $sql = "UPDATE caja SET davivienda = ? WHERE caja_id = ? AND link = ? ";
          $sentencia = $conexion->prepare($sql);
          $params = array($caja_davivienda, $result_cajaId, $linkeo );
          $sentencia->execute($params);
        }else if ($transferenciaMetodo == 01) {

          // Actualizando el dinero de la caja Bancolombia
          $sql = "UPDATE caja SET bancolombia = ? WHERE caja_id = ? AND link = ? ";
          $sentencia = $conexion->prepare($sql);
          $params = array($caja_bancolombia, $result_cajaId, $linkeo );
          $sentencia->execute($params);
        }else if ($transferenciaMetodo == 02) {

          // Actualizando el dinero de la caja Nequi
          $sql = "UPDATE caja SET nequi = ? WHERE caja_id = ? AND link = ? ";
          $sentencia = $conexion->prepare($sql);
          $params = array($caja_nequi, $result_cajaId, $linkeo );
          $sentencia->execute($params);
        }
    }else if($metodo_pago == 2){
        $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
        if ($tipoAbono === 2) {
            $creditoAbono_efectivo = true;
            if($creditoAbono_efectivo){
                $sql = "UPDATE caja SET caja_efectivo = ? WHERE caja_id = ? AND link = ? ";
                $sentencia = $conexion->prepare($sql);
                $params = array($caja_efectivo, $result_cajaId, $linkeo );
                $resultado = $sentencia->execute($params);
            }
        }else if($tipoAbono == 00){
            $sql = "UPDATE caja SET davivienda = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_davivienda, $result_cajaId, $linkeo );
            $sentencia->execute($params);
        }else if($tipoAbono == 01){
            $sql = "UPDATE caja SET bancolombia = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_bancolombia, $result_cajaId, $linkeo );
            $sentencia->execute($params);
        }else if($tipoAbono == 02){
            $sql = "UPDATE caja SET nequi = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_nequi, $result_cajaId, $linkeo );
            $sentencia->execute($params);
        }
    }
    
    
    // Guardando la venta
    $sql = "INSERT INTO venta (venta_codigo, venta_fecha, venta_hora, venta_total, venta_pagado, venta_cambio, 
                        venta_metodo_pago, transferencia_metodo,  plazo, tiempo, cliente_id, caja_id, link, responsable, estado_venta) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
            $codigo_factura, 
            $fechaActual, 
            $horaActual, 
            $total_dinero,
            $recibe_dinero, 
            $cambio_dinero, 
            $metodo_pago,
            $transferenciaMetodo,
            $plazo,
            $tiempo,
            $cliente_id,
            $caja_id,
            $linkeo_venta,
            $user_id,
            $estado_ventas
    );
    //$resulta = $sentencia->execute($params);
    try {
                $resultado = $sentencia->execute($params);
                if ($resultado) {
                    echo "¡La devolución se insertó correctamente en la base de datos!";
                } else {
                    echo "¡Error al insertar la devolución!";
                }
            } catch (PDOException $e) {
                echo "Error de la base de datos: " . $e->getMessage();
            }
    
    
    
    // Obtener el ID de la última fila afectada
   $ultimo_id_insertado = $conexion->lastInsertId();

   // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
   if ($metodo_pago == 2) {
    $sql = "INSERT INTO historial_credito (historial_venta_id, historial_venta_codigo, 
            historial_cliente_id, historial_abono, historial_dinero_pendiente, 
            historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
        $params = array(
        $ultimo_id_insertado, 
        $codigo_factura, 
        $cliente_id, 
        $recibe_dinero,
        $cambio_dinero, 
        $fechaActual, 
        $horaActual,
        $user_id
      );
    $sentencia->execute($params);
}

   date_default_timezone_set('America/Bogota'); 
   $fechaDia = date("d");
   $fechaMes = date("m");
   $fechaYear = date("Y");
//    $fechaDia = 13;
//    $fechaMes = 04;
//    $fechaYear = date("Y");
   
   // GUARDAR SUMA DE DINERO PARA NÓMINA
    $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE link = :link");
    $sentencia->bindParam(":link", $linkeo_venta);
    $sentencia->execute();
    $lista_dinero=$sentencia->fetchAll(PDO::FETCH_ASSOC);
    $valNuevoMes = false ;
    if ($lista_dinero) {
        $existeRegistro = false;
        foreach ($lista_dinero as $fila) {
            if ($fechaDia <= 15 && $fila['mes'] == $fechaMes) {
                if ($fila['dia'] <= 15 && $fila['mes'] == $fechaMes && $fila['anio'] == $fechaYear && $fila['metodo_pago'] == $metodo_pago && $fila['transferencia_metodo'] == $transferenciaMetodo ) {
                    // echo "=============";                    
                    // echo "<br>";                    
                    // echo "...Haciendo Update de la primera quincena...";
                    // echo "<br>";                    
                    // echo "=============";                    

                    $sql = "UPDATE dinero_por_quincena SET dinero = ?, dia = ?, mes = ?, anio = ? WHERE id = ?";
                    $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $fila['dinero'] += $total_dinero,
                        $fechaDia,
                        $fechaMes,
                        $fechaYear,
                        $fila['id']
                    );
                    $sentencia->execute($params);
                    $existeRegistro = true;
                    break; 
                }
            }else {
                $valNuevoMes =true;
            }
        }     
        // Insertando cuando el metodo de pago es diferente a los demas o la negacion de la condicion de arriba
        if (!$existeRegistro && $fechaDia <= 15 && !$valNuevoMes) {
            // echo "==========";
            // echo "<br>";
            // echo "... INSERTANDO NUEVO METODO DE PAGO...";
            // echo "<br>";
            // echo "==========";
            $sql = "INSERT INTO dinero_por_quincena (dinero, link, dia, mes, anio, metodo_pago, transferencia_metodo, estado_credito ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $total_dinero,
                    $linkeo_venta,
                    $fechaDia,
                    $fechaMes,
                    $fechaYear,
                    $metodo_pago,
                    $transferenciaMetodo,
                    $metodo_credito
                    
                );
                $sentencia->execute($params);  
        }
       
        if ($valNuevoMes == true) {
            if($fechaDia <= 15 && $fila['mes'] != $fechaMes) {
                // echo "==========";
                // echo "<br>";
                // echo "...Nueva venta de un Nuevo mes...";
                // echo "<br>";
                // echo "==========";
                $sql = "INSERT INTO dinero_por_quincena (dinero, link, dia, mes, anio, metodo_pago, transferencia_metodo,  estado_credito ) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $total_dinero,
                    $linkeo_venta,
                    $fechaDia,
                    $fechaMes,
                    $fechaYear,
                    $metodo_pago,
                    $transferenciaMetodo,
                    $metodo_credito
                );
                $sentencia->execute($params); 
            }
        }
            // echo "link venta => " . $linkeo_venta;
            // echo "<br>";
            // echo "metodo_pago => " . $metodo_pago;
            // echo "<br>";
            // echo "transferenciaMetodo => " . $transferenciaMetodo;
            // echo "<br>";
        
            // $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE link = :link AND dia <= '15' AND metodo_pago ORDER BY dia DESC limit 1;");
            $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE link = :link AND metodo_pago =:metodo_pago AND transferencia_metodo=:transferenciaMetodo AND mes=:fechaMes ORDER BY id DESC LIMIT 1");
            $sentencia->bindParam(":link", $linkeo_venta);
            $sentencia->bindParam(":metodo_pago", $metodo_pago);
            $sentencia->bindParam(":transferenciaMetodo", $transferenciaMetodo);
            $sentencia->bindParam(":fechaMes", $fechaMes);
            $sentencia->execute();
            $lista_ultimo_update=$sentencia->fetch(PDO::FETCH_LAZY);
            if ($lista_ultimo_update) {
                $id = $lista_ultimo_update['id'];
                $dia_buscado = $lista_ultimo_update['dia'];
                $mes_buscado = $lista_ultimo_update['mes'];
                $anio_buscado = $lista_ultimo_update['anio'];
            }


            // echo "edia buscado carajo => " .$dia_buscado;

            if(!$lista_ultimo_update){
                $sql = "INSERT INTO dinero_por_quincena (dinero, link, dia, mes, anio, metodo_pago, transferencia_metodo, estado_credito) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $total_dinero,
                        $linkeo_venta,
                        $fechaDia,
                        $fechaMes,
                        $fechaYear,
                        $metodo_pago,
                        $transferenciaMetodo,
                        $metodo_credito
                    );
                    $sentencia->execute($params); 
                    // echo "<br>"; 
                    // echo "..INSERTANDO NUEVO METODO DE PAGO===============>  pero de la segunda quincena nene...";
                    // echo "<br>";
            }

            if ($dia_buscado != 16 && $dia_buscado < 16 && $fechaMes == $mes_buscado && $anio_buscado == $fechaYear) {
                if($fechaDia >= 16 && $fechaMes == $mes_buscado && $anio_buscado == $fechaYear) {
                    // echo "...entrando a la validacion de al segunda quincena";
                    // echo "<br>";
                 
                    $sql = "INSERT INTO dinero_por_quincena (dinero, link, dia, mes, anio, metodo_pago, transferencia_metodo, estado_credito) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $total_dinero,
                        $linkeo_venta,
                        $fechaDia,
                        $fechaMes,
                        $fechaYear,
                        $metodo_pago,
                        $transferenciaMetodo,
                        $metodo_credito
                    );
                    $sentencia->execute($params);  
                    // echo "..Guardando la segunda quincena...";
                    // echo "<br>";
                }
            }else if($dia_buscado >= 16){
                // echo "============";
                // echo "<br>";
                // echo "...Update a la segunda quincena";
                // echo "<br>";
                // echo "============";
                $sql = "UPDATE dinero_por_quincena SET dinero = ?, dia = ?, mes = ?, anio = ? WHERE id = ?";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $fila['dinero'] += $total_dinero,
                    $fechaDia,
                    $fechaMes,
                    $fechaYear,
                    $id
                );
                $sentencia->execute($params);
            }

      
    } else {
        // echo "==========";
        // echo "<br>";
        // echo "...Por primera vez haciendo una venta...";
        // echo "<br>";
        // echo "==========";
        $sql = "INSERT INTO dinero_por_quincena (dinero, link, dia, mes, anio, metodo_pago, transferencia_metodo, estado_credito ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
        $params = array(
            $total_dinero,
            $linkeo_venta,
            $fechaDia,
            $fechaMes,
            $fechaYear,
            $metodo_pago,
            $transferenciaMetodo,
            $metodo_credito
        );
        $sentencia->execute($params);  
    }

    // ================================
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
        $sentencia_producto = $conexion->prepare("SELECT producto_stock_total, producto_precio_compra, producto_precio_venta FROM bodega WHERE producto_id = :producto_id");
        $sentencia_producto->bindParam(":producto_id", $producto_id);
        $sentencia_producto->execute();

        $row_producto = $sentencia_producto->fetch(PDO::FETCH_ASSOC);
        $producto_stock_total = $row_producto['producto_stock_total'];
        $producto_precio_compra = $row_producto['producto_precio_compra'];
        $producto_precio_venta = $row_producto['producto_precio_venta'];
        
        if ($cantidad_vendida > $producto_stock_total) {
            echo '<script>
                Swal.fire({
                    title: "Error en la Venta, No hay esa cantidad de productos en Stock",
                    icon: "error",
                    confirmButtonText: "¡Entendido!"
                }).then((result)=>{
                    if(result.isConfirmed){
                        window.location.href = "'.$url_base.'secciones/'.$ventas_link.'";
                    }
                })
            </script>';
            exit;
        }else {
            $total_stock = $producto_stock_total - $cantidad_vendida;
        }

        // Actualizando stock en el inventario 
        $sql = "UPDATE producto SET producto_stock_total = ? WHERE producto_id = ?";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $total_stock, 
                    $producto_id
                );
                $sentencia->execute($params);
        // Guardando el detalle de la venta
       $sql = "INSERT INTO venta_detalle (venta_detalle_cantidad,
                venta_detalle_precio_compra, venta_detalle_precio_venta, venta_detalle_total, venta_detalle_metodo_pago,
                venta_detalle_descripcion, venta_codigo, producto_id, link, responsable) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
            $linkeo_venta,
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

    $tiempo == 0 ? $tiempo = "Dias" : $tiempo = "Meses";

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
                    window.location.href = "'.$url_base.'secciones/'.$ventas_detalles_link.'&txtID='.$ultimo_id_insertado.'";

                }
            })
            </script>';
        } else if($metodo_pago == 1 || $metodo_pago == 3){
            echo '<script>
            Swal.fire({
                title: "¡Venta Exitosa: recuerda que el dinero lo tendras en la cuenta del local!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href = "'.$url_base.'secciones/'.$ventas_detalles_link.'&txtID='.$ultimo_id_insertado.'";
                }
            })
            </script>'; 
        } else {
            echo '<script>
            Swal.fire({
                title: "¡Venta Exitosa a Credito en: '.$plazo. " " .$tiempo.'!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href = "'.$url_base.'secciones/'.$ventas_detalles_link.'&txtID='.$ultimo_id_insertado.'";
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
        <div class="card-header" style="background: #493a3be0">
            <h3 class="card-title textTabla">PRODUCTOS DISPONIBLES</h3>
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
                                        <?php if ($registro['producto_stock_total'] < 5) {?>
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
                                        <form action="<?php echo $ventas_link ?>" method="POST">
                                            <input type="hidden" name="link" value="<?php echo $linkeo; ?>">
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
                            <div class="card-header" style="background: #493a3be0">
                                <h3 class="card-title textTabla">PRODUCTOS ESCOGIDOS</h3>
                            </div>
                            <div class="card-body" style="overflow-x: auto;">
                                <table class="table table-bordered table-striped" style="text-align:center; max-width: 100%;">
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
                                                        <a class="btn btn-danger btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $ventas_link . '&txtID=' . $registro['id']; ?>" role="button"><i class="far fa-trash-alt"></i></a>                    
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
                            <div class="card-header" style="background: #493a3be0">
                                <h3 class="card-title textTabla">DETALLES</h3>
                            </div>
                        <?php if ($noSeller) { ?>
                            <article> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Primero asignar una <strong>caja</strong> para poder realizar una <strong>Venta.</strong></article>
                            <?php } else { ?>
                                <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="textLabel">Métodos de Pago</label> 
                                            <div class="form-group">
                                                <select class="form-control camposTabla" id="metodoPago" name="metodo_pago" onchange="mostrarOcultarPartes(1)">                                    
                                                    <option value="0" style="color:#22c600">Efectivo</option> 
                                                    <option value="1" style="color:#009fc1">Transferencia</option> 
                                                    <option value="3" style="color:#d50000">Datafono</option>  
                                                    <option value="2" style="color:#f4a700">A Crédito</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- A credito --> 
                                    <style>
                                        #partes,#metodo_transferencia{
                                            display: none;
                                        }
                                        span.select2-selection.select2-selection--single{
                                            height: 38px;
                                        }
                                        /* Ocultar las flechas de incremento/decremento en campos numéricos */
                                        input[type=number]::-webkit-inner-spin-button,
                                        input[type=number]::-webkit-outer-spin-button {
                                            -webkit-appearance: none;
                                            margin: 0;
                                        }
                                        input[type=number] {
                                            -moz-appearance: textfield; /* Firefox */
                                        }
                                        </style>

                                    <div class="col-5">
                                        <div class="form-group">
                                            <div class="row" id="partes">
                                                <article style=" padding: 0px 0px 10px;"> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Para Credito es <strong>Obligatorio</strong> que el cliente esté <strong>Registrado</strong>, luego selecciónelo. </article>
                                                Plazo: <input type="number" class="form-control" required name="plazo" value="0">
                                                <br>
                                                <br>
                                                En Dias o Meses: <select class="form-control select2" name="tiempoDiasMeses" style="height: 20px">
                                                    <option value="0">Dias</option> 
                                                    <!-- <option value="1">Mes</option>  -->
                                                </select> 
                                                <br>
                                                <table style="width: 100%">
                                                    <tr>     
                                                        <td ><input checked type="radio" name="tipoAbono" value="1"> No hay abono</td>
                                                        <td><input type="radio" value="2" name="tipoAbono"> Efectivo</td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="radio" value="02" name="tipoAbono"> Nequi</td>
                                                        <td><input type="radio" value="00" name="tipoAbono"> Davivienda</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><input type="radio" value="01" name="tipoAbono"> Bancolombia</td>
                                                    </tr>
                                                </table>
                                                <br>
                                                <article style=" padding: 0px 0px 10px;"> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Escoger  <strong>Cliente</strong>. </article>
                                                 🔻⬇️⬇️🔻
                                            </div>

                                            <div class="row" id="metodo_transferencia">
                                                <div class="form-group">
                                                <label class="textLabel">Eliga Banco</label> 
                                                <select class="form-control camposTabla" id="transferenciaMetodo" name="transferenciaMetodo">                                    
                                                    <option value="00" style="color:#22c600">davivienda</option> 
                                                    <option value="01" style="color:#009fc1">bancolombia</option> 
                                                    <option value="02" style="color:#d50000">nequi</option>
                                                </select>
                                            </div>
                                            </div>

                                            <label class="textLabel">Cliente</label> 
                                            <select class="form-control select2" name="cliente_id" style="height: 20px">
                                                <option value="0">Público General </option> 
                                                <?php foreach ($lista_cliente as $registro) {?>   
                                                    <option value="<?php echo $registro['cliente_id']; ?>"><?php echo $registro['cliente_nombre']; echo " "; echo $registro['cliente_numero_documento']; ?></option> 
                                                <?php } ?>                                    
                                            </select>  
                                        </div>
                                    </div>
                                    <!-- <div class="col-3">
                                        <div class="form-group">
                                        <label class="textLabel">Fecha de Venta</label> 
                                            <input type="text" class="form-control" style="text-align:center;font-weight:600" readonly value="<?php echo $fechaActual ?>">
                                        </div>
                                    </div> -->
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
                                        <label class="textLabel">Cambio</label>
                                        <input type="text" class="form-control camposTabla_dinero se_devuelve" name="cambio_dinero" readonly>
                                    </div>
                                    <input type="hidden" name="link_venta" value="<?php echo $linkeo; ?>">
                                    <input type="hidden" id="generador_codigo_factura" name="codigo_factura">
                                </div>
                                <br>
                                <div class="row" style="justify-content:center">
                                    <div class="col-4">
                                        <button type="submit" name="productos_vendidos" class="btn btn-success btn-lg">Realizar Venta <i class="fa fa-shopping-cart" aria-hidden="true"></i> </button>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>         
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    <?php include("../templates/footer.php") ?>

                            