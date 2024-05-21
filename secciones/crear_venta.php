<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
    $ventas_link = "crear_venta.php";
    $ventas_detalles_link = "detalles.php";
    $detalles_traslado_local = "detalles_traslado_local.php?txtID=";
 }else{
    $ventas_link = "crear_venta.php?link=".$link;
    $ventas_detalles_link = "detalles.php?link=".$link;
    $detalles_traslado_local = "detalles_traslado_local.php?link=".$link.'&txtID=';
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
  $sentencia=$conexion->prepare("SELECT id, producto_codigo, cantidad, producto_id, producto, precio, precio_venta_mayor, marca, modelo FROM carrito WHERE estado = 0 AND link = :link");
  $sentencia->bindParam(":link",$link);
  $sentencia->execute();
  $lista_carrito=$sentencia->fetchAll(PDO::FETCH_ASSOC);
}
    // Mostrando los productos del local
    $sentencia=$conexion->prepare("SELECT * FROM `producto` WHERE link = :link");
    $sentencia->bindParam(":link", $linkeo);
    $sentencia->execute();
    $lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Mostrando los clientes del local
    $sentencia=$conexion->prepare("SELECT * FROM `cliente` WHERE cliente_id > 0 AND link = :link");
    $sentencia->bindParam(":link", $linkeo);
    $sentencia->execute();
    $lista_cliente=$sentencia->fetchAll(PDO::FETCH_ASSOC);

// ===========================================================

//Lista de Locales    
$sentencia_empresas = $conexion->prepare("SELECT empresa_id, empresa_nombre, link FROM empresa WHERE link != :link");
$sentencia_empresas->bindParam(":link",$linkeo);
$sentencia_empresas->execute();
$lista_empresas = $sentencia_empresas->fetchAll(PDO::FETCH_ASSOC);

// Modal de Crear Producto
if(isset($_POST['producto_modal'])) {    
    $producto_codigo = isset($_POST['producto_codigo']) ? $_POST['producto_codigo'] : "";
    $fechaGarantia =  isset($_POST['fechaGarantia']) ? $_POST['fechaGarantia'] : "";
    $producto_nombre = isset($_POST['producto_nombre']) ? $_POST['producto_nombre'] : "";
    $producto_stock_total = isset($_POST['producto_stock_total']) ? $_POST['producto_stock_total'] : "";
    $producto_precio_compra = isset($_POST['producto_precio_compra']) ? $_POST['producto_precio_compra'] : "";
    $producto_precio_venta = isset($_POST['producto_precio_venta']) ? $_POST['producto_precio_venta'] : "";
    $producto_precio_venta_xmayor = isset($_POST['producto_precio_venta_xmayor']) ? $_POST['producto_precio_venta_xmayor'] : "";
    $producto_marca = isset($_POST['producto_marca']) ? $_POST['producto_marca'] : "";
    $producto_modelo = isset($_POST['producto_modelo']) ? $_POST['producto_modelo'] : "";
    $categoria_id = isset($_POST['categoria_id']) ? $_POST['categoria_id'] : "";  
    $proveedor_id = isset($_POST['proveedor_id']) ? $_POST['proveedor_id'] : "";
    $link = isset($_POST['link']) ? $_POST['link'] : $_POST['link'];
    $idResponsable = $_SESSION['usuario_id'];

    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $producto_precio_compra = str_replace(array('$','.', ','), '', $producto_precio_compra);
    $producto_precio_venta = str_replace(array('$','.', ','), '', $producto_precio_venta);
    $producto_precio_venta_xmayor = str_replace(array('$','.', ','), '', $producto_precio_venta_xmayor);

    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d-m-Y"); 

        $sql = "INSERT INTO producto (producto_codigo, producto_fecha_creacion,
            producto_fecha_garantia,producto_nombre, producto_stock_total,producto_precio_compra,producto_precio_venta,producto_precio_venta_xmayor,producto_marca,producto_modelo,
        categoria_id,proveedor_id,link,responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $sentencia = $conexion->prepare($sql);
        $params = array(
            $producto_codigo, 
            $fechaActual, 
            $fechaGarantia, 
            $producto_nombre,
            $producto_stock_total, 
            $producto_precio_compra,
            $producto_precio_venta,
            $producto_precio_venta_xmayor, 
            $producto_marca, 
            $producto_modelo,
            $categoria_id,
            $proveedor_id,
            $link,
            $idResponsable 
        );
        $resultado = $sentencia->execute($params);
        if ($resultado) {
            echo '<script>
            Swal.fire({
                title: "¡Producto creado Exitosamente!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href = "'.$url_base.'secciones/'.$ventas_link.'";
                }
            })
            </script>';
        }else {
            echo '<script>
            Swal.fire({
                title: "Error al Crear Producto",
                icon: "error",
                confirmButtonText: "¡Entendido!"
            });
            </script>';
        
    }
}

// Guardar productos escogidos, al carrito
if(isset($_POST['producto_seleccionado'])) {
    $linkeo = isset($_POST['link']) ? $_POST['link'] : $_POST['link'];
    $producto_seleccionado_encoded = $_POST['producto_seleccionado'];
    $producto_seleccionado = unserialize(base64_decode($producto_seleccionado_encoded));
    $producto_id = $producto_seleccionado['producto_id'];
    $producto_codigo = $producto_seleccionado['producto_codigo'];
    $producto_nombre = $producto_seleccionado['producto_nombre'];
    $producto_precio_compra = $producto_seleccionado['producto_precio_compra'];
    $producto_precio_venta = $producto_seleccionado['producto_precio_venta'];
    $precio_venta_mayor = $producto_seleccionado['producto_precio_venta_xmayor'];
    $producto_marca = $producto_seleccionado['producto_marca'];
    $producto_modelo = $producto_seleccionado['producto_modelo'];
    $responsable_carrito = $_SESSION['usuario_id'];
    
    // Guardando en carrito los productos escogidos
    $sql = "INSERT INTO carrito (producto_codigo, producto_id, producto, precio, costo, precio_venta_mayor, marca, modelo, link, responsable) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sentencia = $conexion->prepare($sql);
        $params = array(
        $producto_codigo, 
        $producto_id, 
        $producto_nombre,
        $producto_precio_venta,
        $producto_precio_compra,
        $precio_venta_mayor,
        $producto_marca, 
        $producto_modelo,
        $linkeo,
        $responsable_carrito
    );
    $sentencia->execute($params);

    // Mostrar el carrito para dar a conocer los productos escogidos a vender
    $sentencia=$conexion->prepare("SELECT * FROM carrito WHERE estado = 0 AND link = :link");
    $sentencia->bindParam(":link", $linkeo);
    $sentencia->execute();
    $lista_carrito=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    $total = 0; 
    $total_precio_venta_mayor = 0; 
    foreach ($lista_carrito as $item) {
        $precio = $item['precio'];
        $total += $precio;
        $precio_venta_mayor = $item['precio_venta_mayor'];
        $total_precio_venta_mayor += $precio_venta_mayor;
    }
}

// Realizando venta
if(isset($_POST['productos_vendidos'])) {
    $cantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : array();
    $totales = isset($_POST['total']) ? $_POST['total'] : array();
    $totales = str_replace(array('$','.', ','), '', $totales);
    $precio_menor = isset($_POST['precio']) ? $_POST['precio'] : array();
    $precio_menor = str_replace(array('$','.', ','), '', $precio_menor);
    $precio_mayor = isset($_POST['precio_venta_xmayor']) ? $_POST['precio_venta_xmayor'] : array();
    $precio_mayor = str_replace(array('$','.', ','), '', $precio_mayor);
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
    $tipo_precio = isset($_POST['tipo-precio']) ? $_POST['tipo-precio'] : $_POST['tipo-precio'];
    
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
      if ($result_caja['caja_efectivo']) {
          $caja_efectivo = $result_caja['caja_efectivo'];
          $recibe_dinero_result = $recibe_dinero - $cambio_dinero;
          $caja_efectivo = $caja_efectivo + $recibe_dinero_result;
      }
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

        // Sumando dinero en total de todos los emprendimientos
        $sql = "UPDATE dtpmp SET efectivo = ?";
        $sentencia_dinero = $conexion->prepare($sql);
        $params = array($caja_efectivo);
        $sentencia_dinero->execute($params);

    }else // Pagando con transaccion sin Credito
      if($metodo_pago == 1){
        $transferenciaMetodo= isset($_POST['transferenciaMetodo']) ? $_POST['transferenciaMetodo'] : "";

        if ($transferenciaMetodo == 00) {
            // Actualizando el dinero de la caja Davivienda
            $sql = "UPDATE caja SET davivienda = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_davivienda, $result_cajaId, $linkeo );
            $sentencia->execute($params);

           // Sumando dinero en total de todos los emprendimientos
            $sql = "UPDATE dtpmp SET davivienda = ?";
            $sentencia_dinero = $conexion->prepare($sql);
            $params = array($caja_davivienda);
            $sentencia_dinero->execute($params);
        }else if ($transferenciaMetodo == 01) {

            // Actualizando el dinero de la caja Bancolombia
            $sql = "UPDATE caja SET bancolombia = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_bancolombia, $result_cajaId, $linkeo );
            $sentencia->execute($params);

            $sql = "UPDATE dtpmp SET bancolombia = ?";
            $sentencia_dinero = $conexion->prepare($sql);
            $params = array($caja_bancolombia);
            $sentencia_dinero->execute($params);
        }else if ($transferenciaMetodo == 02) {

            // Actualizando el dinero de la caja Nequi
            $sql = "UPDATE caja SET nequi = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_nequi, $result_cajaId, $linkeo );
            $sentencia->execute($params);

            $sql = "UPDATE dtpmp SET nequi = ?";
            $sentencia_dinero = $conexion->prepare($sql);
            $params = array($caja_nequi);
            $sentencia_dinero->execute($params);
        }
    }else if($metodo_pago == 2){
        $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
        if ($tipoAbono === "1") {
        }else if ($tipoAbono === "2") {
            $creditoAbono_efectivo = true;
            if($creditoAbono_efectivo){
                $caja_efectivo = $result_caja['caja_efectivo'];
                $cambio_dineroCredito = abs($cambio_dinero);
                $recibe_dinero_result = $recibe_dinero;
                $caja_efectivoCredito = $caja_efectivo + abs($recibe_dinero_result);
                $sql = "UPDATE caja SET caja_efectivo = ? WHERE caja_id = ? AND link = ? ";
                $sentencia = $conexion->prepare($sql);
                $params = array($caja_efectivoCredito, $result_cajaId, $linkeo );
                $sentencia->execute($params);

                $sql = "UPDATE dtpmp SET efectivo = ?";
                $sentencia_dinero = $conexion->prepare($sql);
                $params = array($caja_efectivoCredito);
                $sentencia_dinero->execute($params);
            }
        }else if($tipoAbono == "00"){
            $sql = "UPDATE caja SET davivienda = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_davivienda, $result_cajaId, $linkeo );
            $sentencia->execute($params);

            $sql = "UPDATE dtpmp SET davivienda = ?";
            $sentencia_dinero = $conexion->prepare($sql);
            $params = array($caja_davivienda);
            $sentencia_dinero->execute($params);

        }else if($tipoAbono == "01"){
            $sql = "UPDATE caja SET bancolombia = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_bancolombia, $result_cajaId, $linkeo );
            $sentencia->execute($params);

            $sql = "UPDATE dtpmp SET bancolombia = ?";
            $sentencia_dinero = $conexion->prepare($sql);
            $params = array($caja_bancolombia);
            $sentencia_dinero->execute($params);

        }else if($tipoAbono == "02"){
            $sql = "UPDATE caja SET nequi = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_nequi, $result_cajaId, $linkeo );
            $sentencia->execute($params);

            $sql = "UPDATE dtpmp SET nequi = ?";
            $sentencia_dinero = $conexion->prepare($sql);
            $params = array($caja_nequi);
            $sentencia_dinero->execute($params);
        }
    }
    
    $tipo_precio === 'porMenor' ? $tipo_precio = 0 : $tipo_precio = 1 ;
    
    // Guardando la venta
    $sql = "INSERT INTO venta (venta_codigo, venta_fecha, venta_hora, venta_total, venta_pagado, venta_cambio, 
                        venta_metodo_pago, transferencia_metodo,  plazo, tiempo, cliente_id, caja_id, link, responsable, estado_venta, estado_mayor_menor) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
            $estado_ventas,
            $tipo_precio
    );
    $sentencia->execute($params);
    // Obtener el ID de la última fila afectada
   $ultimo_id_insertado = $conexion->lastInsertId();

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
                    // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                    if ($metodo_pago == 2) {
                        // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                        $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
                        if ($tipoAbono === "1") {
                            $total_dinero = 0;
                        }else if($tipoAbono === "2"){
                            $total_dinero = $recibe_dinero;
                            $metodo_pago_escogido = "Efectivo";
                        }else if($tipoAbono === "00"){
                            $total_dinero = $recibe_dinero;
                            $metodo_pago_escogido = "Davivienda";
                        }else if($tipoAbono === "01"){
                            $total_dinero = $recibe_dinero;
                            $metodo_pago_escogido = "Bancolombia";
                        }else if($tipoAbono === "02"){
                            $total_dinero = $recibe_dinero;
                            $metodo_pago_escogido = "Nequi";
                        }
                    }                    
                    // echo "total dinero => " .$total_dinero;
                    // echo "<br>";
                    // echo "fila['dinero'] => " .$fila['dinero'];
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
                    // Obtener el último ID actualizado
                    $ultimo_id_insertadoDPQ = $fila['id'];
                
                    // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
                    if ($metodo_pago == 2) {
                        $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                                historial_cliente_id, historial_abono, historial_dinero_pendiente, metodo_pago,
                                historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $sentencia = $conexion->prepare($sql);
                            $params = array(
                            $ultimo_id_insertadoDPQ,
                            $ultimo_id_insertado, 
                            $codigo_factura, 
                            $cliente_id, 
                            $recibe_dinero,
                            $cambio_dinero,
                            $metodo_pago_escogido,
                            $fechaActual, 
                            $horaActual,
                            $user_id
                        );
                        $sentencia->execute($params);
                }
                    $existeRegistro = true;
                    break; 
                }
            }else {
                $valNuevoMes =true;
            }
        }     
        // Insertando cuando el metodo de pago es diferente a los demas o la negacion de la condicion de arriba
        if (!$existeRegistro && $fechaDia <= 15 && !$valNuevoMes) {
            // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
            if ($metodo_pago == 2) {
                  // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
                if ($tipoAbono === "1") {
                    $total_dinero = 0;
                }else if($tipoAbono === "2"){
                    $total_dinero = $recibe_dinero;
                    $transferenciaMetodo = $tipoAbono;
                    $metodo_pago_escogido = "Efectivo";
                }else if($tipoAbono === "00"){
                    $total_dinero = $recibe_dinero;
                    $transferenciaMetodo = $tipoAbono;
                    $metodo_pago_escogido = "Davivienda";
                }else if($tipoAbono === "01"){
                    $total_dinero = $recibe_dinero;
                    $transferenciaMetodo = $tipoAbono;
                    $metodo_pago_escogido = "Bancolombia";
                }else if($tipoAbono === "02"){
                    $total_dinero = $recibe_dinero;
                    $transferenciaMetodo = $tipoAbono;
                    $metodo_pago_escogido = "Nequi";
                }
            }
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
                // Ultima insercion en Dinero Por Quincea
                $ultimo_id_insertadoDPQ = $conexion->lastInsertId();
                
                // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
                if ($metodo_pago == 2) {
                    $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                            historial_cliente_id, historial_abono, historial_dinero_pendiente, metodo_pago, 
                            historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $sentencia = $conexion->prepare($sql);
                        $params = array(
                        $ultimo_id_insertadoDPQ,
                        $ultimo_id_insertado, 
                        $codigo_factura, 
                        $cliente_id, 
                        $recibe_dinero,
                        $cambio_dinero,
                        $metodo_pago_escogido, 
                        $fechaActual, 
                        $horaActual,
                        $user_id
                    );
                    $sentencia->execute($params);
                }
        }
       
        if ($valNuevoMes == true) {
            if($fechaDia <= 15 && $fila['mes'] != $fechaMes) {
                // echo "==========";
                // echo "<br>";
                // echo "...Nueva venta de un Nuevo mes...";
                // echo "<br>";
                // echo "==========";

                // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                if ($metodo_pago == 2) {
                    // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                    $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
                    if ($tipoAbono === "1") {
                        $total_dinero = 0;
                    }else if($tipoAbono === "2"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Efectivo";
                    }else if($tipoAbono === "00"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Davivienda";
                    }else if($tipoAbono === "01"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Bancolombia";
                    }else if($tipoAbono === "02"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Nequi";
                    }
                }

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
                // Ultima insercion en Dinero Por Quincea
                $ultimo_id_insertadoDPQ = $conexion->lastInsertId();
        
                // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
                if ($metodo_pago == 2) {
                    $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                            historial_cliente_id, historial_abono, historial_dinero_pendiente, metodo_pago,
                            historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $sentencia = $conexion->prepare($sql);
                        $params = array(
                        $ultimo_id_insertadoDPQ,
                        $ultimo_id_insertado, 
                        $codigo_factura, 
                        $cliente_id, 
                        $recibe_dinero,
                        $cambio_dinero,
                        $metodo_pago_escogido, 
                        $fechaActual, 
                        $horaActual,
                        $user_id
                    );
                    $sentencia->execute($params);
                }
            }
        }
            // echo "link venta => " . $linkeo_venta;
            // echo "<br>";
            // echo "metodo_pago => " . $metodo_pago;
            // echo "<br>";
            // echo "transferenciaMetodo => " . $transferenciaMetodo;
            // echo "<br>";

            if ($metodo_pago == 2) {
                // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
                if ($tipoAbono === "1") {
                }else if($tipoAbono === "2"){
                    $transferenciaMetodo = $tipoAbono;
                }else if($tipoAbono === "00"){
                    $transferenciaMetodo = $tipoAbono;
                }else if($tipoAbono === "01"){
                    $transferenciaMetodo = $tipoAbono;
                }else if($tipoAbono === "02"){
                    $transferenciaMetodo = $tipoAbono;
                }
            }
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
                
                // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                if ($metodo_pago == 2) {
                    // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                    $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
                    if ($tipoAbono === "1") {
                        $total_dinero = 0;
                    }else if($tipoAbono === "2"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Efectivo";
                    }else if($tipoAbono === "00"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Davivienda";
                    }else if($tipoAbono === "01"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Bancolombia";
                    }else if($tipoAbono === "02"){
                        $total_dinero = $recibe_dinero;
                        $transferenciaMetodo = $tipoAbono;
                         $metodo_pago_escogido = "Nequi";
                    }
                }

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
                    // Ultima insercion en Dinero Por Quincea
                    $ultimo_id_insertadoDPQ = $conexion->lastInsertId();
                    
                    // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
                    if ($metodo_pago == 2) {
                        $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                                historial_cliente_id, historial_abono, historial_dinero_pendiente, metodo_pago,
                                historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $sentencia = $conexion->prepare($sql);
                            $params = array(
                            $ultimo_id_insertadoDPQ,
                            $ultimo_id_insertado, 
                            $codigo_factura, 
                            $cliente_id, 
                            $recibe_dinero,
                            $cambio_dinero,
                            $metodo_pago_escogido, 
                            $fechaActual, 
                            $horaActual,
                            $user_id
                        );
                        $sentencia->execute($params);
                    }
                    // echo "<br>"; 
                    // echo "..INSERTANDO NUEVO METODO DE PAGO===============>  pero de la segunda quincena nene...";
                    // echo "<br>";
            }

            if ($dia_buscado != 16 && $dia_buscado < 16 && $fechaMes == $mes_buscado && $anio_buscado == $fechaYear) {
                if($fechaDia >= 16 && $fechaMes == $mes_buscado && $anio_buscado == $fechaYear) {
                    // echo "...entrando a la validacion de al segunda quincena";
                    // echo "<br>";

                    // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                    if ($metodo_pago == 2) {
                        // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                        $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
                        if ($tipoAbono === "1") {
                            $total_dinero = 0;
                        }else if($tipoAbono === "2"){
                            $total_dinero = $recibe_dinero; 
                            $transferenciaMetodo = $tipoAbono;
                             $metodo_pago_escogido = "Efectivo";
                        }else if($tipoAbono === "00"){
                            $total_dinero = $recibe_dinero; 
                            $transferenciaMetodo = $tipoAbono;
                             $metodo_pago_escogido = "Davivienda";
                        }else if($tipoAbono === "01"){
                            $total_dinero = $recibe_dinero; 
                            $transferenciaMetodo = $tipoAbono;
                             $metodo_pago_escogido = "Bancolombia";
                        }else if($tipoAbono === "02"){
                            $total_dinero = $recibe_dinero; 
                            $transferenciaMetodo = $tipoAbono;
                             $metodo_pago_escogido = "Nequi";
                        }
                    }
                 
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
                    // Ultima insercion en Dinero Por Quincea
                    $ultimo_id_insertadoDPQ = $conexion->lastInsertId();
                    
                    // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
                    if ($metodo_pago == 2) {
                        $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                                historial_cliente_id, historial_abono, historial_dinero_pendiente, metodo_pago,
                                historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $sentencia = $conexion->prepare($sql);
                            $params = array(
                            $ultimo_id_insertadoDPQ,
                            $ultimo_id_insertado, 
                            $codigo_factura, 
                            $cliente_id, 
                            $recibe_dinero,
                            $cambio_dinero,
                            $metodo_pago_escogido, 
                            $fechaActual, 
                            $horaActual,
                            $user_id
                        );
                        $sentencia->execute($params);
                    }  
                    // echo "..Guardando la segunda quincena...";
                    // echo "<br>";
                }
            }else if($dia_buscado >= 16){
                // echo "============";
                // echo "<br>";
                // echo "...Update a la segunda quincena";
                // echo "<br>";
                // echo "============";
                // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                if ($metodo_pago == 2) {
                    // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
                    $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
                    if ($tipoAbono === "1") {
                        $total_dinero = 0;
                    }else if($tipoAbono === "2"){
                        $total_dinero = $recibe_dinero;
                         $metodo_pago_escogido = "Efectivo";
                    }else if($tipoAbono === "00"){
                        $total_dinero = $recibe_dinero;
                         $metodo_pago_escogido = "Davivienda";
                    }else if($tipoAbono === "01"){
                        $total_dinero = $recibe_dinero;
                         $metodo_pago_escogido = "Bancolombia";
                    }else if($tipoAbono === "02"){
                        $total_dinero = $recibe_dinero;
                         $metodo_pago_escogido = "Nequi";
                    }
                }

                $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE id=:id ");
                $sentencia->bindParam(":id", $id);
                $sentencia->execute();
                $buscando_metodo_pago = $sentencia->fetch(PDO::FETCH_LAZY);
                if ($buscando_metodo_pago) {
                    $metodo_pago_encontrado = $buscando_metodo_pago['dinero'];
                }

                $sql = "UPDATE dinero_por_quincena SET dinero = ?, dia = ?, mes = ?, anio = ? WHERE id = ?";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $metodo_pago_encontrado += $total_dinero,
                    $fechaDia,
                    $fechaMes,
                    $fechaYear,
                    $id
                );
                $sentencia->execute($params);
                // Ultima update en Dinero Por Quincena
                // Verificar si se realizó la actualización correctamente
                if ($sentencia->rowCount() > 0) {
                    // La actualización se realizó con éxito
                    $ultimo_id_actualizadoDPQ = $id; // El ID que acabas de actualizar
                    // echo "Último ID actualizado: " . $ultimo_id_actualizadoDPQ;
                } else {
                    // No se realizó ninguna actualización
                    // echo "No se realizó ninguna actualización.";
                    $ultimo_id_actualizadoDPQ = $id; // El ID que acabas de actualizar
                    // echo "Último ID actualizado: " . $ultimo_id_actualizadoDPQ;
                }
                // $ultimo_id_insertadoDPQ = $conexion->lastInsertId();
                // echo " utimo id insertado => " .$ultimo_id_insertadoDPQ;
            
                // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
                if ($metodo_pago == 2) {
                    $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                            historial_cliente_id, historial_abono, historial_dinero_pendiente,metodo_pago, 
                            historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $sentencia = $conexion->prepare($sql);
                        $params = array(
                        $ultimo_id_actualizadoDPQ,
                        $ultimo_id_insertado, 
                        $codigo_factura, 
                        $cliente_id, 
                        $recibe_dinero,
                        $cambio_dinero,
                        $metodo_pago_escogido, 
                        $fechaActual, 
                        $horaActual,
                        $user_id
                    );
                    $sentencia->execute($params);
                }
            }

      
    } else {
        // echo "==========";
        // echo "<br>";
        // echo "...Por primera vez haciendo una venta...";
        // echo "<br>";
        // echo "==========";
        // echo "<br>";
        // echo "...| imprimiendo metodo_pago |... = >" . $metodo_pago;
        // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
        if ($metodo_pago == 2) {
            // echo "...| Luego entro al metodo 2 |..";
            // echo "<br>";
            // Guardando lo que abonen justo en la misma compra en: dinero por quincena cuando es por credito
            $tipoAbono = isset($_POST['tipoAbono']) ? $_POST['tipoAbono'] : $_POST['tipoAbono'];
            // echo "...| Imprimo el tipo abono |.. => " . $tipoAbono;
            // echo "<br>";
            if ($tipoAbono === "1") {
                // echo "...| Entrando al 1 tipo abono |.. => " . $tipoAbono;
                // echo "<br>";
                $total_dinero = 0;
            }else if($tipoAbono === "2"){
                // echo "...| Entrando al 2 tipo abono |.. => " . $tipoAbono;
                // echo "<br>";
                // echo "...| imprimiendo recibe dinero |.. => " . $recibe_dinero;
                // echo "<br>";
                $total_dinero = $recibe_dinero;
                $transferenciaMetodo = $tipoAbono;
                 $metodo_pago_escogido = "Efectivo";
            }else if($tipoAbono === "00"){
                // echo "...| Entrando al 00 tipo abono |.. => " . $tipoAbono;
                $total_dinero = $recibe_dinero;
                $transferenciaMetodo = $tipoAbono;
                 $metodo_pago_escogido = "Davivienda";
            }else if($tipoAbono === "01"){
                // echo "...| Entrando al 01 tipo abono |.. => " . $tipoAbono;
                $total_dinero = $recibe_dinero;
                $transferenciaMetodo = $tipoAbono;
                 $metodo_pago_escogido = "Bancolombia";
            }else if($tipoAbono === "02"){
                // echo "...| Entrando al 02 tipo abono |.. => " . $tipoAbono;
                $total_dinero = $recibe_dinero;
                $transferenciaMetodo = $tipoAbono;
                 $metodo_pago_escogido = "Nequi";
            }
        }

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
        // Ultima insercion en Dinero Por Quincea
        $ultimo_id_insertadoDPQ = $conexion->lastInsertId();
        
        // INSERTANDO AUN ASI CUANDO EL ABONO SE VA EN CERO o ya lleva algo
        if ($metodo_pago == 2) {
            $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                    historial_cliente_id, historial_abono, historial_dinero_pendiente,metodo_pago, 
                    historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                $ultimo_id_insertadoDPQ,
                $ultimo_id_insertado, 
                $codigo_factura, 
                $cliente_id, 
                $recibe_dinero,
                $cambio_dinero,
                $metodo_pago_escogido,
                $fechaActual, 
                $horaActual,
                $user_id
            );
            $sentencia->execute($params);
        }
    }

    // ================================
    foreach ($cantidades as $id => $cantidad) {
        $total = $totales[$id] ?? 0;
        $precio_menor_actual = isset($precio_menor[$id]) ? $precio_menor[$id] : 0;
        $precio_mayor_actual = isset($precio_mayor[$id]) ? $precio_mayor[$id] : 0;

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

                if ($tipo_precio == 0 ) {
                    $precio_detalle = $precio_menor_actual;
                }else {
                    $precio_detalle = $precio_mayor_actual;
                } 
        // Guardando el detalle de la venta
       $sql = "INSERT INTO venta_detalle (venta_detalle_cantidad,
                venta_detalle_precio_compra, venta_detalle_precio_venta, venta_detalle_total, venta_detalle_metodo_pago,
                venta_detalle_descripcion, venta_codigo, producto_id, link, responsable, estado_mayor_menor) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
            $cantidad_vendida, 
            $producto_precio_compra, 
            $precio_detalle, 
            $total,
            $metodo_pago, 
            $producto, 
            $codigo_factura,
            $producto_id,
            $linkeo_venta,
            $user_id,
            $tipo_precio
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

/////////CREAR PRODUCTO MODAL - VER CATEGORIAS Y PROVEEDORES//////////
$user_id = $_SESSION['usuario_id'];
$sudo_admin = "sudo_admin";
if ($user_id == 1) {
    $sentencia_categoria = $conexion->prepare("SELECT * FROM categoria WHERE link = :link");
    $sentencia_categoria->bindParam(":link", $sudo_admin);

    $sentencia_prove = $conexion->prepare("SELECT * FROM proveedores WHERE link = :link");
    $sentencia_prove->bindParam(":link", $sudo_admin);
} else {
    $sentencia_categoria = $conexion->prepare("SELECT * FROM categoria WHERE link = :link");
    $sentencia_categoria->bindParam(":link", $link);
    $sentencia_prove = $conexion->prepare("SELECT * FROM proveedores WHERE link = :link");
    $sentencia_prove->bindParam(":link", $link);
}
    $sentencia_categoria->execute();
    $lista_categoria = $sentencia_categoria->fetchAll(PDO::FETCH_ASSOC);

    $sentencia_prove->execute();
    $lista_proveedores = $sentencia_prove->fetchAll(PDO::FETCH_ASSOC);
////////////////////////////////////////////

// Realizando traslado
if(isset($_POST['productos_traslados'])) {
    $cantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : array();
    $totales = isset($_POST['total']) ? $_POST['total'] : array();
    $totales = str_replace(array('$','.', ','), '', $totales);
    $precio_menor = isset($_POST['precio']) ? $_POST['precio'] : array();
    $precio_menor = str_replace(array('$','.', ','), '', $precio_menor);
    $precio_mayor = isset($_POST['precio_venta_xmayor']) ? $_POST['precio_venta_xmayor'] : array();
    $precio_mayor = str_replace(array('$','.', ','), '', $precio_mayor);
     // Validando traslado
     $val_traslado = isset($_POST['val_traslado']) ? $_POST['val_traslado'] : $_POST['val_traslado'];
    // echo "val_traslado = > " .$val_traslado;
    if ($val_traslado == 1) {
        $ramdonCode_tl_productos = isset($_POST['tl_productos']) ? $_POST['tl_productos'] : $_POST['tl_productos'];
        
        $valor_seleccionado = $_POST['empresa_destino'];
        $partes = explode('-', $valor_seleccionado);
        $empresa_id = $partes[0];
        $link_empresa = $partes[1];
     
        $producto_codigo_trasladar = isset($_POST['producto_codigo_trasladar']) ? $_POST['producto_codigo_trasladar'] : array();
        date_default_timezone_set('America/Bogota'); 
        $fechaTrasladoActual = date("d-m-Y"); 

        foreach ($cantidades as $id => $cantidad) {
            // Obtener el producto_codigo_trasladar correspondiente al $id actual
            $codigo_trasladar_actual = isset($producto_codigo_trasladar[$id]) ? $producto_codigo_trasladar[$id] : 0;

            $precio_menor_Tactual = isset($precio_menor[$id]) ? $precio_menor[$id] : 0;
            $precio_mayor_Tactual = isset($precio_mayor[$id]) ? $precio_mayor[$id] : 0;

            $lista_producto_buscado = $conexion->prepare("SELECT link FROM producto WHERE producto_codigo = :producto_codigo AND link=:link_empresa");
            $lista_producto_buscado->bindParam(":producto_codigo", $producto_codigo_trasladar[$id]);
            $lista_producto_buscado->bindParam(":link_empresa", $link_empresa);
            $lista_producto_buscado->execute();
            $lista_producto_buscado = $lista_producto_buscado->fetch(PDO::FETCH_LAZY);

            // Actualizando cantidad al carrito y estado a 1
            $sql = "UPDATE carrito SET cantidad = ?, estado = ? WHERE id = ?";
            $sentencia = $conexion->prepare($sql);
            $params = array($cantidad, 1, $id);
            $sentencia->execute($params);

            // Sacando la info del carrito
            $sentencia_carrito = $conexion->prepare("SELECT id, cantidad, producto_id, producto, marca, modelo, costo, link, responsable FROM carrito WHERE id= :id");
            $sentencia_carrito->bindParam(":id", $id);
            $sentencia_carrito->execute();
            $row_carrito = $sentencia_carrito->fetch(PDO::FETCH_ASSOC);
            $id_carrito = $row_carrito['id'];
            $cantidad_vendida = $row_carrito['cantidad'];
            $producto_id = $row_carrito['producto_id'];
            $producto = $row_carrito['producto'];
            $marca = $row_carrito['marca'];
            $modelo = $row_carrito['modelo'];
            $costo = $row_carrito['costo'];
            $link_carrito = $row_carrito['link'];
            $responsable_carrito = $row_carrito['responsable'];

            if ($lista_producto_buscado) {
                $sql = "INSERT INTO historial_traslados (producto_id, cantidad, fecha_traslado, link_remitente, link_destino, traslado )         
                VALUES (?, ?, ?, ?, ?, ?)";

                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $producto_id, $cantidad_vendida,
                    $fechaTrasladoActual, $link_carrito,        
                    $link_empresa, $ramdonCode_tl_productos
                );
                $sentencia->execute($params);

                // Actualizando la cantidad en el stock del LOCAL que le pasamos de bodega
                $sql = "UPDATE producto SET producto_fecha_ingreso = ?, producto_stock_total = producto_stock_total + ? , traslado = ? WHERE producto_codigo = ? AND link= ?";
                $sentencia_envio = $conexion->prepare($sql);
                $params = array($fechaTrasladoActual, $cantidad_vendida, $ramdonCode_tl_productos, $producto_codigo_trasladar[$id], $link_empresa);
                $resultado = $sentencia_envio->execute($params);
                
            }else{
                // echo "INSERTANDO NUEVO PRODUCTO";
                $sql = "INSERT INTO producto (producto_codigo, producto_fecha_creacion, producto_nombre, producto_stock_total,
                producto_precio_compra,producto_precio_venta, producto_precio_venta_xmayor, producto_marca,producto_modelo, categoria_id,proveedor_id, link, traslado )         
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $producto_codigo_trasladar[$id], 
                    $fechaTrasladoActual, $producto,
                    $cantidad_vendida, $costo,
                    $precio_menor_Tactual, 
                    $precio_mayor_Tactual, 
                    $marca, $modelo,
                    0, 0,
                    $link_empresa, $ramdonCode_tl_productos
                );
                $sentencia->execute($params);

                $sql = "INSERT INTO historial_traslados (producto_id, cantidad, fecha_traslado, link_remitente, link_destino, traslado )         
                VALUES (?, ?, ?, ?, ?, ?)";

                $sentencia = $conexion->prepare($sql);
                $params = array(
                    $producto_id, $cantidad_vendida,
                    $fechaTrasladoActual,
                    $link_carrito, $link_empresa,
                    $ramdonCode_tl_productos
                );
                $sentencia->execute($params);
            }

            // Actualizando nueva cantidad en el stock del local remitente
            $sql = "UPDATE producto SET producto_stock_total = producto_stock_total - ? WHERE producto_id = ? AND link = ?" ; 
            $sentencia_bodega = $conexion->prepare($sql);
            $params = array($cantidad_vendida, $producto_id, $link_carrito);
            $sentencia_bodega->execute($params);

            // Borrar los datos de carrito una ves se haya realizado el proceso anterior
            $sql = "DELETE FROM carrito WHERE estado = ? AND responsable= ?";
            $sentencia = $conexion->prepare($sql);
            $params = array(1, $responsable_carrito
            );
            $result_traslado_nuevo=$sentencia->execute($params);

            if ($result_traslado_nuevo) {
                echo '<script>
                Swal.fire({
                    title: "¡Productos Trasladado Exitosamente!",
                    icon: "success",
                    confirmButtonText: "¡Entendido!"
                }).then((result)=>{
                    if(result.isConfirmed){
                        window.location.href = "'.$url_base.'secciones/'.$detalles_traslado_local. $ramdonCode_tl_productos.'";
                    }
                })
                </script>';
            }else {
                echo '<script>
                Swal.fire({
                    title: "Error al Realizar Traslado",
                    icon: "error",
                    confirmButtonText: "¡Entendido!"
                });
                </script>';
            }   
        }
    }else{
        echo "no pasa nada";
    }
}

?>
<br>
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title textTabla">PRODUCTOS DISPONIBLES</h3>
        </div>
        <div class="card-body ">
            <div class="card card-info">
                <div class="card-body">
                    <table id="vBuscar" class="table table-bordered table-striped example" style="text-align:center">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Existencias</th>
                                <th>Precio al Detal</th>
                                <th>Precio al por Mayor</th>
                                <th>Escoger</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php error_reporting(E_ERROR | E_PARSE); foreach ($lista_producto as $registro) {?>
                                <tr>
                                    <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                                    <td><?php echo $registro['producto_nombre']; ?></td>
                                    <td><?php echo $registro['producto_marca']; ?></td>
                                    <td><?php echo $registro['producto_modelo']; ?></td>
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
                                    <td class="tdColor"><?php echo '$ ' . number_format($registro['producto_precio_venta_xmayor'], 0, '.', ','); ?></td>
                                    <td>
                                        <form action="<?php echo $ventas_link ?>" method="POST">
                                            <input type="hidden" name="producto_precio_compra" value="<?php echo $registro['producto_precio_compra']; ?>">
                                            <input type="hidden" name="link" value="<?php echo $linkeo; ?>">
                                            <input type="hidden" name="producto_id" value="<?php echo $registro['producto_id']; ?>">
                                            <input type="hidden" name="producto_codigo" value="<?php echo $registro['producto_codigo']; ?>">
                                            <?php if($registro['producto_stock_total'] == 0) { ?>
                                                <button type="button" class="btn btn-warning">Agotado</button>
                                            <?php }else { ?>
                                                   <button type="submit" name="producto_seleccionado" value="<?php echo base64_encode(serialize($registro)); ?>" class="btn btn-primary"><i class="fas fa-chevron-right"></i></button>
                                            <?php } ?>
                                        </form>
                                    </td>
                                </tr>  
                            <?php } ?>
                        </tbody>                  
                    </table>
                </div>
            </div>
        </div>
        <br>
        <!-- formulario -->
        <form method="post" action="">
            <div class="row">
                <div class="col-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title textTabla">PRODUCTOS ESCOGIDOS</h3>
                        </div>
                        <style>
                                /* limitar productos en crear venta */ 
                            .table-container {
                                max-height: 287px; /* Altura máxima del contenedor de la tabla */
                                overflow-y: auto; /* Agrega un scroll vertical cuando el contenido exceda la altura máxima */
                            }

                            .table {
                                width: 100%; /* Ancho completo de la tabla */
                            }
                        </style>
                        <div class="card-body" style="overflow-x: auto;">
                            <div class="table-container">
                            <table class="table table-bordered table-striped" style="text-align:center; max-width: 100%;">
                                <thead>
                                    <tr style="font-size: 14px;">
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Cantidad</th>
                                        <th><input type="radio" name="tipo-precio" value="porMenor" id="porMenor" checked> <label for="porMenor"> Al Detal</label></th>
                                        <th><input type="radio" name="tipo-precio" value="porMayor" id="porMayor"> <label for="porMayor"> Al por Mayor</label></th>
                                        <th>Total</th>
                                        <th>Remover</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lista_carrito as $registro) {?>
                                        <tr id="producto_<?php echo $registro['id']; ?>" style="font-size: 14px;">
                                            <input type="hidden" value="<?php echo $registro['id']; ?>">
                                            <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                                            <td><?php echo $registro['producto']; ?></td>
                                            <td><?php echo $registro['marca']; ?></td>
                                            <td><?php echo $registro['modelo']; ?></td>
                                            <td><input style="width: 49px" type="number" onblur="update_producto(<?php echo $registro['id']; ?>)" class="cantidad-input form-control" id="cantidadProducto_<?php echo $registro['id']; ?>" name="cantidad[<?php echo $registro['id']; ?>]" value="<?php echo $registro['cantidad']; ?>"></td>
                                            <td style="font-weight: 800;"><input type="text" class="precio_menor form-control" name="precio[<?php echo $registro['id']; ?>]" style="width: 77px;" value="<?php echo number_format($registro['precio'], 0, '.', ','); ?>"></td>
                                            <td style="font-weight: 800;"><input type="text" class="precio_mayor form-control" name="precio_venta_xmayor[<?php echo $registro['id']; ?>]" style="width: 77px;" value="<?php echo number_format($registro['precio_venta_mayor'], 0, '.', ','); ?>"> </td>
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
                        </div>
                            <br>    
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title textTabla">DETALLES</h3>
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
                            .custom-radio {
                                transform: scale(1.2);
                            }
                            </style>
                        
                        <!-- TABS -->
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">Ventas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">Trasladar</a>
                            </li>
                        </ul>

                        <!-- CONTENIDO DE TABS -->
                        <div class="tab-content" id="custom-tabs-four-tabContent">
                            <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                                <?php if ($noSeller) { ?>
                                    <article> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Primero asignar una <strong>caja</strong> para poder realizar una <strong>Venta.</strong></article>
                                <?php } else { ?>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label>Métodos de Pago</label> 
                                                    <div class="form-group">
                                                        <select class="form-control" id="metodoPago" name="metodo_pago" onchange="mostrarOcultarPartes(1)">                                    
                                                            <option value="0" style="color:#22c600">Efectivo</option> 
                                                            <option value="1" style="color:#009fc1">Transferencia</option> 
                                                            <option value="3" style="color:#d50000">Datafono</option>  
                                                            <option value="2" style="color:#f4a700">A Crédito</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
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
                                                                <td ><input checked type="radio" class="custom-radio" name="tipoAbono" value="1"> No hay abono</td>
                                                                <td><input type="radio" value="2" class="custom-radio" name="tipoAbono"> Efectivo</td>
                                                            </tr>
                                                            <tr>
                                                                <td><input type="radio" value="02" class="custom-radio" name="tipoAbono"> Nequi</td>
                                                                <td><input type="radio" value="00" class="custom-radio" name="tipoAbono"> Davivienda</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2"><input type="radio" class="custom-radio" value="01" name="tipoAbono"> Bancolombia</td>
                                                            </tr>
                                                        </table>
                                                        <br>
                                                        <article style=" padding: 0px 0px 10px;"> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Escoger  <strong>Cliente</strong>. </article>
                                                            🔻⬇️⬇️🔻
                                                    </div>
                                                    <div class="row" id="metodo_transferencia">
                                                        <div class="form-group">
                                                            <label>Eliga Banco</label> 
                                                            <select class="form-control camposTabla" id="transferenciaMetodo" name="transferenciaMetodo">                                    
                                                                <option value="00" style="color:#22c600">davivienda</option> 
                                                                <option value="01" style="color:#009fc1">bancolombia</option> 
                                                                <option value="02" style="color:#d50000">nequi</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <label>Cliente</label> 
                                                    <select class="form-control select2" name="cliente_id" id="fastClienteVenta" style="height: 20px">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Accesos Rápidos</label> 
                                                    <span><a type="button" data-toggle="modal" data-target="#crearClienteLocal">- Crear Cliente</a> </span>
                                                    <span><a type="button" data-toggle="modal" data-target="#crearProductoLocal">- Crear Producto</a> </span>
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
                                                <label class="textLabel">Cambio</label>
                                                <input type="text" class="form-control camposTabla_dinero se_devuelve" name="cambio_dinero" readonly>
                                            </div>
                                            <input type="hidden" name="link_venta" value="<?php echo $linkeo; ?>">
                                            <input type="hidden" id="generador_codigo_factura" name="codigo_factura">
                                        </div>
                                        <br>
                                        <div class="row" style="justify-content:center">
                                            <div class="col-3">
                                                <button type="submit" name="productos_vendidos" style="width: 108%;font-size: 21px;" class="btn btn-success btn-lg">Vender <i class="fa fa-shopping-cart" aria-hidden="true"></i> </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </form>
                            <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                                <form method="post" action="">
                                    <input type="hidden" value="1" name="val_traslado">
                                    <input type="hidden" id="tlProductos" name="tl_productos">
                                    <?php foreach ($lista_carrito as $registro) {?>
                                            <input type="hidden" name="producto_codigo_trasladar[<?php echo $registro['id']; ?>]" value="<?php echo $registro['producto_codigo']; ?>">
                                            <input type="hidden" class="carrito_debajo carrito_<?php echo $registro['id']; ?>" name="cantidad[<?php echo $registro['id']; ?>]" value="<?php echo $registro['cantidad']; ?>">
                                            <input type="hidden" class="precio_menor" name="precio[<?php echo $registro['id']; ?>]" style="width: 77px;" value="<?php echo number_format($registro['precio'], 0, '.', ','); ?>">
                                            <input type="hidden" class="precio_mayor" name="precio_venta_xmayor[<?php echo $registro['id']; ?>]" style="width: 77px;" value="<?php echo number_format($registro['precio_venta_mayor'], 0, '.', ','); ?>">
                                    <?php } ?>
                                    <br>
                                    <div class="row">
                                        <div class="col-4" style="margin-left: 18px;">
                                            <div class="form-group">
                                                <label class="textLabel">Enviar a:</label> &nbsp;<i class="nav-icon fas fa-share"></i> 
                                                <div class="form-group camposTabla">
                                                    <select class="form-control select2 camposTabla"name="empresa_destino">                                    
                                                        <option value="">Escoger Local</option> 
                                                        <?php foreach ($lista_empresas as $registro) {?>   
                                                            <option value="<?php echo $registro['empresa_id'] . '-' . $registro['link']?>"><?php echo $registro['empresa_nombre']; ?></option> 
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="justify-content:center">
                                        <div class="col-3">
                                            <button type="submit" name="productos_traslados" style="width: 108%;font-size: 21px;" class="btn btn-info">Trasladar <i class="nav-icon fas fa-share"></i> </button>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Modal Crear Cliente-->
        <div class="modal fade" id="crearClienteLocal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Crear Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="cliente_empresa_venta" class="">Empresa</label>
                                    <input type="text" class="form-control" id="cliente_empresa_venta">
                                </div>
                            </div>                          
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="cliente_nit_venta" class="">Nit</label>
                                    <input type="text" class="form-control " id="cliente_nit_venta">
                                    <input type="hidden" id="link" value="<?php echo $link ?>">
                                </div>
                            </div>                            
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="cliente_nombre_venta" class="">Nombres</label>
                                    <input type="text" class="form-control " id="cliente_nombre_venta" required>
                                </div>
                            </div>                     
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="cliente_apellido_venta" class="">Apellidos</label>
                                    <input type="text" class="form-control " id="cliente_apellido_venta" required>
                                </div>
                            </div>                        
                        </div> 
                        <div class="row" style="justify-content:center">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="cliente_telefono_venta" class="">Teléfono</label>
                                    <input type="num" class="form-control" id="cliente_telefono_venta">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="cliente_ciudad_venta" >Ciudad</label>
                                    <input type="text" class="form-control" id="cliente_ciudad_venta">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="cliente_direccion_venta">Dirección</label> 
                                    <input type="text" class="form-control" id="cliente_direccion_venta">
                                </div>
                            </div>                        
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="cliente_email_venta">Correo</label>
                                    <input type="text" class="form-control " id="cliente_email_venta">
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" id="cerrarModalClienteVenta" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" onclick="sendDataVenta()" class="btn btn-primary">Registrar</button>
                </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <!-- Modal Crear Producto-->
        <div class="modal fade" id="crearProductoLocal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Crear Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="">
                    <div class="modal-body">
                        <div class="row">                        
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control camposTabla" name="producto_codigo" required>
                                    <input type="hidden" name="link" value="<?php echo $linkeo; ?>">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control camposTabla" name="producto_nombre" required>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Marca</label>
                                    <input type="text" class="form-control camposTabla" name="producto_marca" required>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Modelo</label>
                                    <input type="text" class="form-control camposTabla" name="producto_modelo" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <div class="form-group camposTabla">
                                        <select class="form-control select2 camposTabla" style="width: 100%;" name="categoria_id">                                    
                                            <option value="">Escoger Categoría</option> 
                                            <?php foreach ($lista_categoria as $registro) {?>   
                                                <option value="<?php echo $registro['categoria_id']; ?>"><?php echo $registro['categoria_nombre']; ?></option> 
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Proveedor</label>
                                    <div class="form-group">
                                        <select class="form-control select2 camposTabla" style="width: 100%;" name="proveedor_id">                                    
                                            <option value="">Escoger tu proveedor</option> 
                                            <?php foreach ($lista_proveedores as $registro) {?>   
                                                <option value="<?php echo $registro['id_proveedores']; ?>"><?php echo $registro['nombre_proveedores']; ?></option> 
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Fecha de Garantía</label>
                                    <div class="input-group date" id="fechaGarantia" data-target-input="nearest">
                                        <input name ="fechaGarantia" type="text" class="form-control datetimepicker-input" data-target="#fechaGarantia" />
                                        <div class="input-group-append" data-target="#fechaGarantia" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Stock o Existencias</label>
                                    <input type="number" class="form-control camposTabla_stock" name="producto_stock_total" required>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="producto_precio_compra">Precio de Compra</label>
                                    <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="producto_precio_compra" id="producto_precio_compra">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="producto_precio_venta">Precio al Detal</label>
                                    <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="producto_precio_venta" id="producto_precio_venta">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="producto_precio_venta_xmayor">Precio al por mayor</label> 
                                    <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="producto_precio_venta_xmayor" id="producto_precio_venta_xmayor">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" name="producto_modal" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Modal -->
    </div>
        <script src="https://code.jquery.com/jquery-3.7.1.js" ></script>
        <script src="accion_fetch_venta.js"></script>
        <?php include("../templates/footer.php") ?>

                            