<?php include("../templates/header.php") ?>
<?php 
if(isset($_SESSION['link_remitente_array'])){
    // Recorre el array y utiliza cada valor de link_remitente
    foreach ($_SESSION['link_remitente_array'] as $link_remitente) {
        // Puedes usar $link_remitente aquí como lo necesites
    }
} else {
    // Si el array no está presente en la sesión, maneja el caso según tus necesidades
}
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
        $sentencia_empresa=$conexion->prepare("SELECT link_remitente FROM historial_traslados WHERE traslado = :txtID");
        $sentencia_empresa->bindParam(":txtID", $txtID);
        $sentencia_empresa->execute();
        $buscando_remitente=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
        if ($buscando_remitente) {
            $link_encontrado = $buscando_remitente['link_remitente'];
        }

        if ($link_encontrado == "sudo_bodega") {
            $link_bodega = "sudo_bodega";
            $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa_bodega WHERE link = :link");
            $sentencia_empresa->bindParam(":link", $link_bodega);
            $sentencia_empresa->execute();
            $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
            $empresa_nombre = isset($registro_empresa["bodega_nombre"]) ? $registro_empresa["bodega_nombre"] : "";
            $empresa_telefono = isset($registro_empresa["bodega_telefono"]) ? $registro_empresa["bodega_telefono"] : "";
            $empresa_direccion = isset($registro_empresa["bodega_direccion"]) ? $registro_empresa["bodega_direccion"] : "";
            $empresa_nit = isset($registro_empresa["bodega_nit"]) ? $registro_empresa["bodega_nit"] : "";
        }else {
            $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa WHERE link = :link");
            $sentencia_empresa->bindParam(":link", $link);
            $sentencia_empresa->execute();
            $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
            $empresa_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
            $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
            $empresa_direccion = isset($registro_empresa["empresa_direccion"]) ? $registro_empresa["empresa_direccion"] : "";
            $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
            $empresa_nit = isset($registro_empresa["empresa_nit"]) ? $registro_empresa["empresa_nit"] : "";
        }
    }else{
        $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa WHERE link = :link");
        $sentencia_empresa->bindParam(":link", $link);
        $sentencia_empresa->execute();
        $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
        $empresa_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
        $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
        $empresa_direccion = isset($registro_empresa["empresa_direccion"]) ? $registro_empresa["empresa_direccion"] : "";
        $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
        $empresa_nit = isset($registro_empresa["empresa_nit"]) ? $registro_empresa["empresa_nit"] : "";
    }

    if ($_SESSION['rolBodega']) {   
        $sentencia_venta = $conexion->prepare("SELECT ht.* , p.* FROM historial_traslados ht 
            JOIN producto p ON ht.producto_id = p.producto_id   
            WHERE ht.link_remitente =:link AND ht.traslado=:txtID");
            $sentencia_venta->bindParam(":link", $link);
            $sentencia_venta->bindParam(":txtID", $txtID);
            $sentencia_venta->execute();
            $detalle_traslado = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);
            $sumaTotal = 0;
            $sumaTotalxMenor = 0;
            $sumaTotalxMayor = 0;
        foreach ($detalle_traslado as $dataTraslado) {
            $empresa_destido = $dataTraslado['link_destino'];
            $fecha_traslado = $dataTraslado['fecha_traslado'];

            // calculando el total de costos
            $resulCadaUno = $dataTraslado['cantidad'] * $dataTraslado['producto_precio_compra'];
            $sumaTotal += $resulCadaUno;

            // calculando el total de al por menor
            $resulCadaUno = $dataTraslado['cantidad'] * $dataTraslado['producto_precio_venta'];
            $sumaTotalxMenor += $resulCadaUno;

            // calculando el total de al por mayor
            $resulCadaUno = $dataTraslado['cantidad'] * $dataTraslado['producto_precio_venta_xmayor'];
            $sumaTotalxMayor += $resulCadaUno;
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
    
    }else {
        $sentencia_venta = $conexion->prepare("SELECT ht.* , p.* FROM historial_traslados ht 
            JOIN producto p ON ht.producto_id = p.producto_id   
            WHERE ht.link_remitente =:link AND ht.traslado=:txtID");
        $sentencia_venta->bindParam(":link", $link);
        $sentencia_venta->bindParam(":txtID", $txtID);
        $sentencia_venta->execute();
        $detalle_traslado = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);
        $sumaTotal = 0;
        $sumaTotalxMenor = 0;
        $sumaTotalxMayor = 0;
        foreach ($detalle_traslado as $dataTraslado) {
            $empresa_destido = $dataTraslado['link_destino'];
            $fecha_traslado = $dataTraslado['fecha_traslado'];
    
            // calculando el total de costos
            $resulCadaUno = $dataTraslado['cantidad'] * $dataTraslado['producto_precio_compra'];
            $sumaTotal += $resulCadaUno;
    
            // calculando el total de al por menor
            $resulCadaUno = $dataTraslado['cantidad'] * $dataTraslado['producto_precio_venta'];
            $sumaTotalxMenor += $resulCadaUno;
    
            // calculando el total de al por mayor
            $resulCadaUno = $dataTraslado['cantidad'] * $dataTraslado['producto_precio_venta_xmayor'];
            $sumaTotalxMayor += $resulCadaUno;
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

    }

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
                <strong class="textTittleAddress">Despacho: </strong><span class="textContentAddress"><?php echo $empresa_nombre;?></span><br>
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
                                <!-- <th>SUBTOTAL COSTO</th> -->
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
                                    <!-- <td><?php echo '$' . number_format($registro['producto_precio_venta'] = $registro['cantidad']*$registro['producto_precio_compra'], 0, '.', ','); ?></td> -->
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
                                <th style="width:50%">Total Costos:</th>
                                <td class="tdColor"><?php echo '<strong>$' . number_format($sumaTotal, 0, '.', ',') . '</strong>'; ?></td>
                            </tr>
                            <tr>
                                <th style="width:50%">Total al Detal:</th>
                                <td class="tdColor"><?php echo '<strong>$' . number_format($sumaTotalxMenor, 0, '.', ',') . '</strong>'; ?></td>
                            </tr>
                            <tr>
                                <th style="width:50%">Total al por Mayor:</th>
                                <td class="tdColor"><?php echo '<strong>$' . number_format($sumaTotalxMayor, 0, '.', ',') . '</strong>'; ?></td>
                            </tr>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
<?php include("../templates/footer.php") ?>