<?php include("../templates/header.php") ?>
<?php 

  if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $link=(isset($_GET['link']))?$_GET['link']:"";
  
    if($_SESSION['rolSudoAdmin']){
      $sentencia=$conexion->prepare("SELECT venta.*, usuario.*,cliente.* 
      FROM venta 
      INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
      INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id WHERE venta.venta_id=:venta_id");
      $sentencia->bindParam(":venta_id",$txtID);

    }else  {
      $sentencia=$conexion->prepare("SELECT venta.*, usuario.*,cliente.* 
      FROM venta 
      INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
      INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id WHERE venta.venta_id=:venta_id AND venta.link = :link");
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
      $sentencia_empresa = $conexion->prepare("SELECT empresa.* FROM empresa INNER JOIN venta ON empresa.link = venta.link
        WHERE venta.venta_id = :venta_id");
        $sentencia_empresa->bindParam(":venta_id", $txtID);
        $sentencia_empresa->execute();

    $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
    $usuario_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
    $empresa_nombre = isset($registro_empresa["empresa_nombre"]) ? $registro_empresa["empresa_nombre"] : "";
    $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
    $empresa_direccion = isset($registro_empresa["empresa_direccion"]) ? $registro_empresa["empresa_direccion"] : "";
    $empresa_telefono = isset($registro_empresa["empresa_telefono"]) ? $registro_empresa["empresa_telefono"] : "";
    $empresa_nit = isset($registro_empresa["empresa_nit"]) ? $registro_empresa["empresa_nit"] : "";

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
  // $empresa_nombre=$registro_empresa["empresa_nombre"];  
  // $empresa_telefono=$registro_empresa["empresa_telefono"];  
  // $empresa_direccion=$registro_empresa["empresa_direccion"];  
  // $empresa_nit=$registro_empresa["empresa_nit"];  
 
  // Mostrar lista comprados
  if($_SESSION['rolSudoAdmin']){
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo,producto_precio_venta_xmayor
    FROM venta
    INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
    INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo");
    $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);

  }else  if ($_SESSION['rolBodega']) {
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo,producto_precio_venta_xmayor
    FROM venta
    INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
    INNER JOIN bodega ON venta_detalle.producto_id = bodega.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo AND venta_detalle.link = :link");
    $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);
    $sentencia_venta->bindParam(":link", $link);
  }else {
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo,producto_precio_venta_xmayor
    FROM venta
    INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
    INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo AND venta_detalle.link = :link");
    $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);
    $sentencia_venta->bindParam(":link", $link);

  }
  $sentencia_venta->execute();
  $detalle_venta = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);

}else {
  echo " no existe nada";
}
////// Fecha de Vencimiento /////////

// Establecer la zona horaria
date_default_timezone_set('America/Bogota');

$fechaCompraDB = $venta_fecha; // Obtienes esto de tu base de datos

// Plazo en días obtenido de la base de datos
$plazoDias = $plazo; // Ejemplo, obtienes esto de tu base de datos

// Convertir la fecha de compra a un objeto de fecha/hora
$fechaCompra = date_create($fechaCompraDB);

// Sumar el plazo de días a la fecha de compra
date_add($fechaCompra, date_interval_create_from_date_string($plazoDias . ' days'));

// Obtener la fecha de vencimiento
$fechaVencimiento = date_format($fechaCompra, 'd-m-Y');


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
                <strong>Código de Venta: </strong><span class="textContentAddress"><span><?php echo $venta_codigo;?></span><br>
                <strong class="textTittleAddress">Vendedor: </strong><span class="textContentAddress"><?php echo $usuario_nombre;?></span><br>
                <strong class="textTittleAddress">Dirección: </strong><span class="textContentAddress"><?php echo $empresa_direccion;?></span><br>
                <strong class="textTittleAddress">Teléfono: </strong><span class="textContentAddress"><?php echo $empresa_telefono;?></span><br>
                <strong class="textTittleAddress">Ciudad: </strong><span class="textContentAddress"> Cartagena de Indias</span><br>  
              </address>
            </div>
            <!-- /.col -->
            <div class=" invoice-col" style="position: relative;">
            <br>
              <strong style="font-size: 1.5em;" class="remision">REMISION: <?php echo $venta_id; ?></strong> 
                <address>      
                  <strong class="textTittleAddress">Cliente: </strong><span class="textContentAddress"><?php echo $cliente_nombre;?> <?php echo $cliente_apellido;?></span><br>
                  <strong class="textTittleAddress">Teléfono: </strong><span class="textContentAddress"><?php echo $cliente_telefono;?></span><br>                                  
                  <?php if ($cliente_empresa) { ?>
                    <strong class="textTittleAddress">Empresa: </strong><span class="textContentAddress"><?php echo $cliente_empresa?></span><br>
                    <?php } ?>
                  <strong class="textTittleAddress">Nit: </strong><span class="textContentAddress"><?php echo $cliente_nit;?></span><br>                                  
                  <strong class="textTittleAddress">Dirección: </strong><span class="textContentAddress"><?php echo $cliente_ciudad ." " . $cliente_direccion?></span><br>                                  
                  <?php if ($venta_metodo_pago == "Credito") { ?>
                    <strong class="textTittleAddress">Fecha: </strong><span class="textContentAddress"><?php echo $venta_fecha ?> <strong> Vence </strong> <?php echo $fechaVencimiento?></span><br>           
                    <?php } else { ?>
                      <strong class="textTittleAddress">Fecha: </strong><span class="textContentAddress"><?php echo $venta_fecha;?></span><br>           
                    <?php } ?>
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
                                <th>PRECIO</th>
                                <!-- <th>PAGADO</th> -->
                                <th>SUBTOTAL</th>
                                <!-- <?php if ($venta_metodo_pago == "Credito") { ?>
                                  <th>CREDITO PENDIENTE</th>
                                <?php } else { ?>
                                  <th>CAMBIO</th>
                                <?php } ?> -->
                            </tr>
                        </thead>
                        <tbody style="font-size: 13px;">
                            <?php foreach ($detalle_venta as $registro) {?>
                                <tr>
                                    <td><?php echo $registro['producto_codigo']; ?></td>
                                    <td><?php echo $registro['venta_detalle_descripcion']; ?></td>
                                    <td><?php echo $registro['producto_marca']; ?></td>
                                    <td><?php echo $registro['producto_modelo']; ?></td>
                                    <td><?php echo $registro['venta_detalle_cantidad']; ?></td>
                                    <td><?php if ($registro['estado_mayor_menor'] == 0) { echo '$' . number_format($registro['venta_detalle_precio_venta'], 0, '.', ',');}else { echo '$' . number_format($registro['producto_precio_venta_xmayor'], 0, '.', ',') ;} ?></td> 
                                    <!-- <td><?php echo '$' . number_format(abs($registro['venta_pagado']), 0, '.', ','); ?></td> -->
                                    <td><?php echo '$' . number_format($registro['venta_detalle_total'], 0, '.', ','); ?></td>    
                                    <!-- <td><?php echo '$' . number_format(abs($registro['venta_cambio']), 0, '.', ','); ?></td> -->
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
                    <td class="tdColor"><?php echo '<strong>$' . number_format($venta_total, 0, '.', ',') . '</strong>'; ?></td>
                </tr>
                <tr>
                    <th>Pagado:</th>
                    <td><?php echo '<strong>$' . number_format($venta_pagado, 0, '.', ',') . '</strong>'; ?></td>
                </tr>
                <tr>
                    <?php if ($venta_metodo_pago == "Credito") { ?>
                        <th>Credito Pendiente:</th>
                    <?php } else { ?>
                        <th>Cambio:</th>
                    <?php } ?>
                    <td class="text-warning"><?php echo '<strong>$' . number_format($venta_cambio, 0, '.', ',') . '</strong>'; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

            
            <?php
                $detalles_venta_array = array();
                foreach ($detalle_venta as $registro) {
                    $detalles_venta_array[] = array(
                      'venta_id' => $registro['venta_id'],
                      'descripcion' => $registro['venta_detalle_descripcion'],
                      'cantidad' => $registro['venta_detalle_cantidad'],
                      'precio_venta' => $registro['venta_detalle_precio_venta'],
                      'total' => $registro['venta_detalle_total'],
                      'fecha_garantia' => $registro['producto_fecha_garantia'],
                      'producto_marca' => $registro['producto_marca'],
                      'producto_modelo' => $registro['producto_modelo'],
                    );
                }
                ?>
            <!-- this row will not appear when printing -->
            <form method="POST" action="ticket.php" target="_blank">
              <!-- Datos de la empresa -->
              <input type="hidden" name="empresa_nombre" value="<?php echo $empresa_nombre ?>">
              <input type="hidden" name="empresa_telefono" value="<?php echo $empresa_telefono ?>">
              <input type="hidden" name="empresa_direccion" value="<?php echo $empresa_direccion ?>">
              <input type="hidden" name="empresa_nit" value="<?php echo $empresa_nit ?>">
              <!-- Datos de la venta -->
              <input type="hidden" name="venta_id" value="<?php echo $venta_id ?>">
              <input type="hidden" name="venta_codigo" value="<?php echo $venta_codigo ?>">
              <input type="hidden" name="venta_fecha" value="<?php echo $venta_fecha;?>">
              <input type="hidden" name="venta_hora" value="<?php echo $venta_hora;?>">
              <input type="hidden" name="caja_id" value="<?php echo $caja_id ?>">
              <input type="hidden" name="venta_metodo_pago" value="<?php echo $venta_metodo_pago ?>">
              <!-- Datos del empleado  -->
              <input type="hidden" name="usuario_nombre" value="<?php echo $usuario_nombre ?>">
              <!-- Datos del cliente -->
              <input type="hidden" name="nombre_cliente" value="<?php echo $cliente_nombre;?> <?php echo $cliente_apellido;?>">
              <input type="hidden" name="cliente_nit" value="<?php echo $cliente_nit ?>">
              <input type="hidden" name="cliente_telefono" value="<?php echo $cliente_telefono ?>">
              <!-- Datos de dinero  -->
              <input type="hidden" name="venta_total" value="<?php echo $venta_total ?>">
              <input type="hidden" name="venta_pagado" value="<?php echo $venta_pagado ?>">
              <input type="hidden" name="venta_cambio" value="<?php echo $venta_cambio ?>">
              <input type="hidden" name="plazo" value="<?php echo $plazo ?>">
              <input type="hidden" name="tiempo" value="<?php echo $tiempo ?>">

              <input type="hidden" name="detalles_venta" value='<?php echo json_encode($detalles_venta_array); ?>'>
              <div class="row no-print">
                <div class="col-12">                    
                  <button type="submit" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fas fa-download"></i> Generar Ticket                    
                  </button>   
                  <a href="https://api.whatsapp.com/send?phone=57<?php echo $cliente_telefono ?>" class="btn btn-success float-right" style="margin-right: 5px;" target="_blank">
                      <i class="fab fa-whatsapp"></i>
                  </a>
                  <button type="button" class="btn btn-default float-right" onclick="window.print()" style="margin-right: 5px;">
                    <i class="fa fa-print"></i>
                  </button>
                  <?php if ($venta_metodo_pago == "Credito") { ?>
                    <a class="btn btn-warning float-right" style="margin-right: 5px;" href="<?php echo $url_base ?>secciones/<?php echo $crear_abono_links ?>=<?php echo $venta_codigo; ?>" role="button" title="Abonar">
                      <i class="fa fa-credit-card" aria-hidden="true"></i> Abonar
                    </a>   
                  <?php } ?>
                  <div id="ID_mostrar_info"></div>
                </div> 
              </div>
            </form>
          </div>
            </div> 
            <!-- /.row -->
            

          
<!-- <script>
    function generarTicket() {
        let form = document.getElementById('ticketForm');
        let venta_codigo = form.elements['venta_codigo'].value;
        console.log("Venta Codigo:", venta_codigo);
        // Enviar solicitud AJAX
        $.ajax({
          data: { venta_codigo: venta_codigo },
          url: 'ticket.php',
          type: 'POST',
          beforeSend: function () {
              $('#ID_mostrar_info').html("Mensaje antes de enviar");
          },
          success: function (mensaje_mostrar) {
              $('#ID_mostrar_info').html(mensaje_mostrar);
              var urlDelTicket = 'ticket.php?factura=' + encodeURIComponent(venta_codigo);
              window.open(urlDelTicket, 'Imprimir Ticket', 'width=400,height=720,top=0,left=100,menubar=no,toolbar=yes');
          },
          error: function (xhr, status, error) {
              console.error("Error en la solicitud AJAX:", status, error);
          },
          complete: function () {
              console.log("Solicitud AJAX completada");
          }
});
    }
</script> -->
<?php include("../templates/footer.php") ?>