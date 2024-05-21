<?php include("../templates/header.php") ?>
<?php 

if ($_SESSION['valSudoAdmin']) {
  $ventas_detalles_link = "detalles.php";
}else{
  $ventas_detalles_link = "detalles.php?link=".$link;
}
  if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $link=(isset($_GET['link']))?$_GET['link']:"";

    if($_SESSION['rolSudoAdmin']){
      
      $sentencia=$conexion->prepare("SELECT venta.*, usuario.*,cliente.* 
      FROM venta 
      INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
      INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id WHERE venta.cliente_id=:venta_id");
      $sentencia->bindParam(":venta_id",$txtID);
      
    }else {
      $sentencia=$conexion->prepare("SELECT venta.*, usuario.*,cliente.* 
      FROM venta 
      INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
      INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id WHERE venta.cliente_id=:venta_id AND venta.link = :link");
      $sentencia->bindParam(":venta_id",$txtID);
      $sentencia->bindParam(":link",$link);
    }

    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    $venta_id=$registro["venta_id"];
    $venta_fecha=$registro["venta_fecha"];
    $venta_hora=$registro["venta_hora"];  
    $venta_codigo=$registro["venta_codigo"];
    $venta_total=$registro["venta_total"];
    $venta_pagado=$registro["venta_pagado"];  
    $venta_cambio=$registro["venta_cambio"];
    $venta_metodo_pago=$registro["venta_metodo_pago"];  
    $venta_metodo_pago == 2 ?  $venta_pagado=$registro["venta_pagado"] : $venta_pagado = $venta_pagado + $venta_cambio;
    $venta_cambio = abs($venta_cambio);  
    $plazo=$registro["plazo"];  
    $tiempo=$registro["tiempo"];
    $cliente_telefono=$registro["cliente_telefono"];
    
    $tiempo == 0 ? $tiempo = "Días" : $tiempo = "Meses";

    if ($venta_metodo_pago == 0) {
      $venta_metodo_pago = "Efectivo";
    }else if($venta_metodo_pago == 1){
      $venta_metodo_pago = "Transferencia";
    }else if($venta_metodo_pago == 2){
      $venta_metodo_pago = "Credito";
    }else {
      $venta_metodo_pago = "Datafono";
    }

    $caja_id=$registro["caja_id"];  
    $usuario_nombre=$registro["usuario_nombre"];  
    $cliente_nit=$registro["cliente_nit"];  
    $cliente_nombre=$registro["cliente_nombre"];  
    $cliente_apellido=$registro["cliente_apellido"];  
    $cliente_empresa=$registro["cliente_empresa"];  
    $cliente_ciudad=$registro["cliente_ciudad"];  
    $cliente_direccion=$registro["cliente_direccion"];  

    // Activar/Desactivar boton de editar Cuando la compra sea por credito
  $estado_venta_hc = $registro["estado_venta"];  
  $sentencia_hvc=$conexion->prepare("SELECT COUNT(historial_venta_codigo) as total_abonos FROM historial_credito WHERE historial_venta_codigo = :venta_codigo");
  $sentencia_hvc->bindParam(":venta_codigo", $venta_codigo);
  $sentencia_hvc->execute();
  $resultado_sentencia = $sentencia_hvc->fetch(PDO::FETCH_LAZY);
  if ($resultado_sentencia) {
     $total_abonos = $resultado_sentencia['total_abonos'];
  }
  $estado_boton = true;
  if ($estado_venta_hc == 0 && $total_abonos > 1 ) {
    $estado_boton = false ;
  }
    // Creando link de bono link en detalle
    if ($_SESSION['rolBodega']) {   
      $crear_abono_links = "crear_abono.php?link=sudo_bodega&txtID";
    }else if ($_SESSION['rolSudoAdmin']){
      $crear_abono_links = "crear_abono.php?txtID";
    }else{
      $crear_abono_links = "crear_abono.php?txtID";
    }
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
      echo "clientre => " .$txtID;
      $sentencia_empresa=$conexion->prepare("SELECT link FROM venta WHERE cliente_id = :txtID");
      $sentencia_empresa->bindParam(":txtID", $txtID);
      $sentencia_empresa->execute();
      $buscando_cliente=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
      if ($buscando_cliente) {
          $link_encontrado = $buscando_cliente['link'];
      }

      if ($link_encontrado == "sudo_bodega") {
        $sentencia_empresa = $conexion->prepare("SELECT * FROM empresa_bodega WHERE link =:link_encontrado");
        $sentencia_empresa->bindParam(":link_encontrado", $link_encontrado);
        $sentencia_empresa->execute();
        $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
        $empresa_nombre = isset($registro_empresa["bodega_nombre"]) ? $registro_empresa["bodega_nombre"] : "";
        $empresa_telefono = isset($registro_empresa["bodega_telefono"]) ? $registro_empresa["bodega_telefono"] : "";
        $empresa_direccion = isset($registro_empresa["bodega_direccion"]) ? $registro_empresa["bodega_direccion"] : "";
        $empresa_nit = isset($registro_empresa["bodega_nit"]) ? $registro_empresa["bodega_nit"] : "";
    }else {
      $sentencia_empresa = $conexion->prepare("SELECT * FROM empresa WHERE link = :link_encontrado");
      $sentencia_empresa->bindParam(":link_encontrado", $link_encontrado);
      $sentencia_empresa->execute();
      $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
      $usuario_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
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
      $usuario_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
      $empresa_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
      $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
      $empresa_direccion = isset($registro_empresa["empresa_direccion"]) ? $registro_empresa["empresa_direccion"] : "";
      $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
      $empresa_nit = isset($registro_empresa["empresa_nit"]) ? $registro_empresa["empresa_nit"] : "";

  }

  // Mostrar lista comprados
  if($_SESSION['rolSudoAdmin']){
    $sentencia_venta = $conexion->prepare("SELECT * FROM venta 
    WHERE cliente_id = :txtID AND venta_metodo_pago = 2");
    $sentencia_venta->bindParam(":txtID", $txtID);

  }else  if ($_SESSION['rolBodega']) {
    $sentencia_venta = $conexion->prepare("SELECT * FROM venta 
    WHERE cliente_id = :txtID AND venta_metodo_pago = 2 AND link = :link;");
    $sentencia_venta->bindParam(":txtID", $txtID);
    $sentencia_venta->bindParam(":link", $link);
  }else {

    $sentencia_venta = $conexion->prepare("SELECT * FROM venta 
    WHERE cliente_id = :txtID AND venta_metodo_pago = 2 AND link = :link;");
    $sentencia_venta->bindParam(":txtID", $txtID);
    $sentencia_venta->bindParam(":link", $link);
  
}
  $sentencia_venta->execute();
  $detalle_venta = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);
  
    $sumaVentaTotal = 0;
    $sumaVentaPagado = 0;
    $sumaVentaCambio = 0;
    foreach ($detalle_venta as $key ) {

        $venta_total_facturas = $key['venta_total'];
        $sumaVentaTotal += $venta_total_facturas;

        $venta_pagado_facturas = $key['venta_pagado'];
        $sumaVentaPagado += $venta_pagado_facturas;

        $venta_cambio_facturas = $key['venta_cambio'];
        $sumaVentaCambio += $venta_cambio_facturas;
    }


}else {
  echo " no existe nada";
}

if(isset($_POST['factura_abono_venta'])) {    


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
                <address>      
                  <strong class="textTittleAddress">Cliente: </strong><span class="textContentAddress"><?php echo $cliente_nombre;?> <?php echo $cliente_apellido;?></span><br>
                  <strong class="textTittleAddress">Teléfono: </strong><span class="textContentAddress"><?php echo $cliente_telefono;?></span><br>                                  
                  <?php if ($cliente_empresa) { ?>
                    <strong class="textTittleAddress">Empresa: </strong><span class="textContentAddress"><?php echo $cliente_empresa?></span><br>
                    <?php } ?>
                  <strong class="textTittleAddress">Nit: </strong><span class="textContentAddress"><?php echo $cliente_nit;?></span><br>                                  
                  <strong class="textTittleAddress">Dirección: </strong><span class="textContentAddress"><?php echo $cliente_ciudad ." " . $cliente_direccion?></span><br>                                  
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
                .custom-radio {
                    transform: scale(1.5);
                }
            </style>

            <div class="row tableProductos">
                <div class="col-12 table-responsive " >
                    <table class="table table-bordered table-striped  invoice-info">
                        <thead style="font-size: 13px;">
                            <tr>
                                <th>CÓDIGO DE VENTA</th>
                                <th>FECHA VENTA</th>
                                <th>MONTO TOTAL</th>
                                <th>MONTO ABONADO</th>
                                <th>MONTO PENDIENTE</th>
                                <th class="no-print"></th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 13px;">
                              <?php foreach ($detalle_venta as $registro) {?>
                                  <tr>
                                      <td><?php echo $registro['venta_codigo']; ?></td>
                                      <td><?php echo $registro['venta_fecha']; ?></td>
                                      <td><?php echo '$' . number_format(abs($registro['venta_total']), 0, '.', ','); ?></td>
                                      <td><?php echo '$' . number_format($registro['venta_pagado'], 0, '.', ','); ?></td>    
                                      <td><?php echo '$' . number_format(abs($registro['venta_cambio']), 0, '.', ','); ?></td>
                                      <?php if ($registro['estado_venta'] == 1) { ?>                    
                                        <td class="no-print">  
                                          <a class="btn btn-success btn-sm" href="<?php echo $url_base ?>secciones/<?php echo $crear_abono_links ?>=<?php echo $registro['venta_codigo']; ?>" role="button" title="Pagado">
                                            <i class="fa fa-check" aria-hidden="true"></i> Pagado
                                          </a>
                                        </td>
                                        <?php } else { ?> 
                                        <td class="no-print">
                                            <a class="btn btn-warning btn-sm" href="<?php echo $url_base ?>secciones/<?php echo $crear_abono_links ?>=<?php echo $registro['venta_codigo']; ?>" role="button" title="Abonar">
                                              <i class="fa fa-credit-card" aria-hidden="true"></i> Abonar
                                            </a>
                                        </td>
                                      <?php } ?>
                                      </td>
                                  </tr>  
                              <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <div class="row justify-content-end">
                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:50%">Total:</th>
                                <td class="tdColor"><?php echo '<strong>$' . number_format($sumaVentaTotal, 0, '.', ',') . '</strong>'; ?></td>
                            </tr>
                            <tr>
                                <th>Pagado:</th>
                                <td><?php echo '<strong>$' . number_format($sumaVentaPagado, 0, '.', ',') . '</strong>'; ?></td>
                            </tr>
                            <tr>
                                <?php if ($venta_metodo_pago == "Credito") { ?>
                                    <th>Credito Pendiente:</th>
                                <?php } else { ?>
                                    <th>Cambio:</th>
                                <?php } ?>
                                <td class="text-warning"><?php echo '<strong>$' . number_format(abs($sumaVentaCambio), 0, '.', ',') . '</strong>'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 

<?php include("../templates/footer.php") ?>