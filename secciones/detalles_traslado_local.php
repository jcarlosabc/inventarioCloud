<?php include("../templates/header.php") ?>
<?php 

  if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $link=(isset($_GET['link']))?$_GET['link']:"";
  
    // Datos de empresa para la factura
    if ($_SESSION['rolBodega']) {   
        $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa_bodega WHERE link = :link");
        $sentencia_empresa->bindParam(":link", $link);
        $sentencia_empresa->execute();
        $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
        $empresa_nombre = isset($registro_empresa["bodega_nombre"]) ? $registro_empresa["bodega_nombre"] : "";
        $empresa_telefono = isset($registro_empresa["bodega_telefono"]) ? $registro_empresa["bodega_telefono"] : "";
        $empresa_direccion = isset($registro_empresa["bodega_direccion"]) ? $registro_empresa["bodega_direccion"] : "";
        $empresa_nit = isset($registro_empresa["bodega_nit"]) ? $registro_empresa["bodega_nit"] : "";
    }else if ($_SESSION['rolSudoAdmin']){
        // $sentencia_empresa = $conexion->prepare("SELECT empresa.* FROM empresa INNER JOIN venta ON empresa.link = venta.link
        // WHERE venta.venta_id = :venta_id");
        // $sentencia_empresa->bindParam(":venta_id", $txtID);
        // $sentencia_empresa->execute();
        // $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
        // $usuario_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
        // $empresa_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
        // $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
        // $empresa_direccion = isset($registro_empresa["empresa_direccion"]) ? $registro_empresa["empresa_direccion"] : "";
        // $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
        // $empresa_nit = isset($registro_empresa["empresa_nit"]) ? $registro_empresa["empresa_nit"] : "";
    }else{
        $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa WHERE link = :link");
        $sentencia_empresa->bindParam(":link", $link);
        $sentencia_empresa->execute();
        $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
        $usuario_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
        $empresa_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
        $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
        $empresa_direccion = isset($registro_empresa["empresa_direccion"]) ? $registro_empresa["empresa_direccion"] : "";
        $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
        $empresa_nit = isset($registro_empresa["empresa_nit"]) ? $registro_empresa["empresa_nit"] : "";
    }
    $empresa_nombre=$registro_empresa["empresa_nombre"];  
    $empresa_telefono=$registro_empresa["empresa_telefono"];  
    $empresa_direccion=$registro_empresa["empresa_direccion"];  
    $empresa_nit=$registro_empresa["empresa_nit"];  
 
  // Mostrar lista comprados
//   if($_SESSION['rolSudoAdmin']){
//     $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo,producto_precio_venta_xmayor
//     FROM venta
//     INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
//     INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
//     WHERE venta_detalle.venta_codigo = :venta_codigo");
//     $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);

//   }else  if ($_SESSION['rolBodega']) {
//     $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo,producto_precio_venta_xmayor
//     FROM venta
//     INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
//     INNER JOIN bodega ON venta_detalle.producto_id = bodega.producto_id
//     WHERE venta_detalle.venta_codigo = :venta_codigo AND venta_detalle.link = :link");
//     $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);
//     $sentencia_venta->bindParam(":link", $link);
//    }else {
//     $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo,producto_precio_venta_xmayor
//     FROM venta
//     INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
//     INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
//     WHERE venta_detalle.venta_codigo = :venta_codigo AND venta_detalle.link = :link");
    // $sentencia_venta = $conexion->prepare("SELECT p.*, ht.* FROM producto p INNER JOIN historial_traslados ht ON p.producto_id = ht.producto_id 
    // WHERE p.producto_id =: txtID");
    // $sentencia_venta->bindParam(":txtID", $txtID);

//   }
//     $sentencia_venta = $conexion->prepare("SELECT p.*, ht.*, e.* FROM producto p 
//         INNER JOIN historial_traslados ht ON p.producto_id = ht.producto_id INNER JOIN empresa e ON e.link = ht.link_destino 
//         WHERE p.producto_id =:producto_id AND p.producto_fecha_creacion = ht.fecha_traslado");
//     $sentencia_venta->bindParam(":producto_id", $txtID);
//     $sentencia_venta->execute();
//     $detalle_traslado = $sentencia_venta->fetch(PDO::FETCH_LAZY);

//   if ($detalle_traslado) {
//         $producto_codigo = $detalle_traslado['producto_codigo'];
//         $remision_traslado = $detalle_traslado['remision_traslado'];
//         $producto_nombre = $detalle_traslado['producto_nombre'];
//         $producto_marca = $detalle_traslado['producto_marca'];
//         $producto_modelo = $detalle_traslado['producto_modelo'];
//         $cantidad_traladada = $detalle_traslado['cantidad'];
//         $producto_precio_compra = $detalle_traslado['producto_precio_compra'];
//         $producto_precio_venta = $detalle_traslado['producto_precio_venta'];
//         $producto_precio_venta_xmayor = $detalle_traslado['producto_precio_venta_xmayor'];
//         $empresa_nombre = $detalle_traslado['empresa_nombre'];
//         $empresa_telefonot = $detalle_traslado['empresa_telefono'];
//         $empresa_nitt = $detalle_traslado['empresa_nit'];
//         $empresa_direcciont = $detalle_traslado['empresa_direccion'];
//         $fecha_traslado = $detalle_traslado['fecha_traslado'];
//   }
    $sentencia_venta = $conexion->prepare("SELECT ht.* , p.* FROM historial_traslados ht 
        JOIN producto p ON ht.producto_id = p.producto_id   
        WHERE ht.link_remitente =:link AND ht.traslado=:txtID");
    $sentencia_venta->bindParam(":link", $link);
    $sentencia_venta->bindParam(":txtID", $txtID);
    $sentencia_venta->execute();
    $detalle_traslado = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);
    $sumaTotal = 0;
    foreach ($detalle_traslado as $dataTraslado) {
        $empresa_destido = $dataTraslado['link_destino'];
        $fecha_traslado = $dataTraslado['fecha_traslado'];
        $resulCadaUno = $dataTraslado['cantidad'] * $dataTraslado['producto_precio_compra'];
        $sumaTotal += $resulCadaUno;
    }
    $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa WHERE link = :link");
    $sentencia_empresa->bindParam(":link", $empresa_destido);
    $sentencia_empresa->execute();
    $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);

    $empresa_nombreT = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
    $empresa_telefonoT = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
    $empresa_direccionT = isset($registro_empresa["empresa_direccion"]) ? $registro_empresa["empresa_direccion"] : "";
    $empresa_telefonoT = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
    $empresa_nitT = isset($registro_empresa["empresa_nit"]) ? $registro_empresa["empresa_nit"] : "";

//   if ($detalle_traslado) {
//         $producto_codigo = $detalle_traslado['producto_codigo'];
//         $remision_traslado = $detalle_traslado['remision_traslado'];
//         $producto_nombre = $detalle_traslado['producto_nombre'];
//         $producto_marca = $detalle_traslado['producto_marca'];
//         $producto_modelo = $detalle_traslado['producto_modelo'];
//         $cantidad_traladada = $detalle_traslado['cantidad'];
//         $producto_precio_compra = $detalle_traslado['producto_precio_compra'];
//         $producto_precio_venta = $detalle_traslado['producto_precio_venta'];
//         $producto_precio_venta_xmayor = $detalle_traslado['producto_precio_venta_xmayor'];
//         $empresa_nombre = $detalle_traslado['empresa_nombre'];
//         $empresa_telefonot = $detalle_traslado['empresa_telefono'];
//         $empresa_nitt = $detalle_traslado['empresa_nit'];
//         $empresa_direcciont = $detalle_traslado['empresa_direccion'];
//         $fecha_traslado = $detalle_traslado['fecha_traslado'];
//   }


}else {
  echo " no existe nada";
}

?>
<br>

<!-- Main content -->
<div class="invoice p-3 mb-3">
 <br>
 <br>
            <div class="row" style="margin-left: 16%;">
                <div class="col-2 text-right">
                    <img src="../dist/img/logos/logofernando.jpg" style="width: 84px; margin-top: -5%;" alt="AdminLTE Logo" class="brand-image img-circle elevation-3">
                </div>
                <div class="col-10 text-left align-self-center">
                    <h1 style="font-size: 2.8rem !important; margin-top: 20px; margin-bottom: 0;">
                    VARIEDADES21TECHNOLOGY
                    </h1>
                </div>
            </div>
            <!-- info row -->
            <style>
                .contorno-negro {
                    border: 1px solid grey;
                    padding: 10px; /* Opcional: ajusta el relleno según sea necesario */
                    position: relative; /* Establece el contexto de posicionamiento */
                }
                
                .contorno-negro2 {
                    border: 1px solid grey;
                    padding: 10px; /* Opcional: ajusta el relleno según sea necesario */
                    position: absolute; /* Posiciona el elemento en relación con el padre (.contorno-negro) */
                    top: -9%; /* Ajusta la posición en la parte superior del contenedor */
                    right: -11px; /* Ajusta la posición en la parte derecha del contenedor */
                    width: calc(100% - 20px); /* Calcula el ancho del div para que ocupe todo el espacio disponible */
                }

                .row.invoice-info {
                    display: flex;
                    justify-content: space-between; /* Distribuye uniformemente los elementos con espacio entre ellos */
                    align-items: center; /* Centra verticalmente */
                    position: relative; /* Establece el contexto de posicionamiento */
                    margin: 1% 1%;
                    margin-left: 5%;
                    margin-right: 5%;
                }

                .invoice-col {
                    flex: 1;
                    margin: -15px 0;
                }

                .invoice-col address {
                    font-size: 1.2em;
                }
                .remision{
                  margin-top: inherit;
                  float: right;
                  font-size: 1.5em;
                }
                .textTittleAddress{
                  font-size: 17px;
                }
                .textContentAddress{
                  font-size: 16px;
                }
            </style>
           <div class="row invoice-info contorno-negro" style="border: solid 1px #00000047; border-radius: 7px;">
            <div class= "invoice-col">
              <br>                
              <address>
                <strong class="textTittleAddress">Vendedor: </strong><span class="textContentAddress"><?php echo $usuario_nombre;?></span><br>
                <strong class="textTittleAddress">Dirección: </strong><span class="textContentAddress"><?php echo $empresa_direccion;?></span><br>
                <strong class="textTittleAddress">Teléfono: </strong><span class="textContentAddress"><?php echo $empresa_telefono;?></span><br>
                <strong class="textTittleAddress">Ciudad: </strong><span class="textContentAddress"> Cartagena de Indias</span><br>  
              </address>
            </div>
            <!-- /.col -->
            <div class=" invoice-col" style="position: relative;">
            <br>
              <strong style="font-size: 1.5em;" class="remision">Referencia: <?php echo $txtID; ?></strong> 
                <address>   
                    <strong class="textTittleAddress">Destino: </strong><span class="textContentAddress"><?php echo $empresa_nombreT;?></span><br>
                    <strong class="textTittleAddress">Teléfono: </strong><span class="textContentAddress"><?php echo $empresa_telefonoT;?></span><br>                                  
                    <strong class="textTittleAddress">Nit: </strong><span class="textContentAddress"><?php echo $empresa_nitT;?></span><br>                                  
                    <strong class="textTittleAddress">Dirección: </strong><span class="textContentAddress"><?php echo $empresa_direccionT ;?></span><br>                                  
                    <strong class="textTittleAddress">Fecha de Traslado: </strong><span class="textContentAddress"><?php echo $fecha_traslado ?><br>           
                  </address>
                </div>               
              </div>
              <!-- /.row -->
              <style>
                .table-bordered {
                    border: 1px solid grey;
                    border-collapse: collapse;                     
                }

                .table-bordered th,
                .table-bordered td {
                    /* border: 1px solid grey;  */
                     text-align: center; 
                 }
                .tableProductos{
                margin-left: 5%;
                margin-right: 5% ;
                }
            </style>

            <div class="row tableProductos">
                <div class="col-12 table-responsive " >
                    <table class="table table-bordered table-striped  invoice-info">
                        <thead style="font-size: 13px;">
                            <tr>
                                <th>CÓDIGO</th>
                                <th>PRODUCTO</th>
                                <th>MARCA</th>
                                <th>REFERENCIA</th>
                                <th>CANTIDAD</th>
                                <th>COSTO</th>
                                <th>AL DETAL</th>
                                <th>AL POR MAYOR</th>
                                <th>SUBTOTAL COSTO</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13px;">
                            <?php foreach ($detalle_traslado as $registro) { ?>
                                <tr>
                                    <td><?php echo $registro['producto_codigo']; ?></td>
                                    <td><?php echo $registro['producto_nombre']; ?></td>
                                    <td><?php echo $registro['producto_marca']; ?></td>
                                    <td><?php echo $registro['producto_modelo']; ?></td>
                                    <td><?php echo $registro['cantidad']; ?></td>
                                    <td><?php echo '$' . number_format($registro['producto_precio_compra'], 0, '.', ','); ?></td>
                                    <td><?php echo '$' . number_format($registro['producto_precio_venta'], 0, '.', ','); ?></td>
                                    <td><?php echo '$' . number_format($registro['producto_precio_venta_xmayor'], 0, '.', ','); ?></td>
                                    <td><?php echo '$' . number_format($registro['producto_precio_venta'] = $registro['cantidad']*$registro['producto_precio_compra'], 0, '.', ','); ?></td>
                                </tr>  
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:50%">Total:</th>
                                <td class="tdColor"><?php echo '<strong>$' . number_format($sumaTotal, 0, '.', ',') . '</strong>'; ?></td>
                            </tr>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
<?php include("../templates/footer.php") ?>