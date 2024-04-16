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
    $venta_cambio = abs($venta_cambio);  
    $venta_metodo_pago=$registro["venta_metodo_pago"];  
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
    $cliente_numero_documento=$registro["cliente_numero_documento"];  
    $cliente_nombre=$registro["cliente_nombre"];  
    $cliente_apellido=$registro["cliente_apellido"];  
    $cliente_telefono=$registro["cliente_telefono"];  

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

    }if ($_SESSION['rolSudoAdmin']){
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
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo
    FROM venta
    INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
    INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo");
    $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);

  }else  if ($_SESSION['rolBodega']) {
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo
    FROM venta
    INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
    INNER JOIN bodega ON venta_detalle.producto_id = bodega.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo AND venta_detalle.link = :link");
    $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);
    $sentencia_venta->bindParam(":link", $link);
  }else {
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*,producto_codigo, producto_fecha_garantia,producto_marca, producto_modelo
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
  <div class="row">
    <div class="col-12 text-center">
      <h1><br>
        <?php echo $empresa_nombre;?>
      </h1>
    </div>
  </div>
            <!-- info row -->
            <style>
                .contorno-negro {
                    border: 1px solid black;
                    padding: 10px; /* Opcional: ajusta el relleno según sea necesario */
                    position: relative; /* Establece el contexto de posicionamiento */
                }
                
                .contorno-negro2 {
                    border: 1px solid black;
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
                    margin-left: 10%;
                    margin-right: 10%;
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
            </style>
          <div class="row invoice-info contorno-negro">
            <div class= "invoice-col">
              <br>                
              <address>
                <strong>Código de Venta: </strong><?php echo $venta_codigo;?><br>
                <strong>Vendedor: </strong><?php echo $usuario_nombre;?><br>
                <strong>Direccion: </strong><?php echo $empresa_direccion;?><br>
                <strong>Telefono: </strong><?php echo $empresa_telefono;?><br>
                <strong>Ciudad: </strong> Cartagena de Indias<br>  
              </address>
            </div>
            <!-- /.col -->
            <div class=" invoice-col" style="position: relative;">
              <strong style="font-size: 1.5em;" class="remision">REMISION: <?php echo $venta_id; ?></strong> 
                <address>      
                  <strong>Fecha: </strong> <?php echo $venta_fecha;?><br>           
                  <?php if ($venta_metodo_pago == "Credito") { ?>
                    <strong>Vence: </strong><?php echo $fechaVencimiento?><br>
                    <?php } ?>
                    <strong>Cliente: </strong><?php echo $cliente_nombre;?> <?php echo $cliente_apellido;?><br>
                    <strong>CC: </strong><?php echo $cliente_numero_documento;?>                                  
                  </address>
                </div>               
              </div>
              <!-- /.row -->
              <style>
                .table-bordered {
                    border: 1px solid black;
                    border-collapse: collapse;                     
                }

                .table-bordered th,
                .table-bordered td {
                    border: 1px solid black; 
                     text-align: center; /* Centra el contenido horizontalmente */
                 }
                .tableProductos{
                margin-left: 10%;
                margin-right: 10% ;
                }
            </style>

            <div class="row tableProductos">
                <div class="col-12 table-responsive " >
                    <table class="table table-bordered table-striped  invoice-info">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Codigo de Producto</th>
                                <th>PRODUCTO</th>
                                <th>CANTIDAD</th>
                                <th>PRECIO</th>
                                <th>SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0;
                            foreach ($detalle_venta as $registro) {?>
                                <tr class="">
                                    <td scope="row"><?php $count++; echo $count; ?></td>
                                    <td><?php echo $registro['producto_codigo']; ?></td>
                                    <td><?php echo $registro['venta_detalle_descripcion']; ?></td>
                                    <td><?php echo $registro['venta_detalle_cantidad']; ?></td>
                                    <td><?php echo '$' . number_format($registro['venta_detalle_precio_venta'], 0, '.', ','); ?></td> 
                                    <td><?php echo '$' . number_format($registro['venta_detalle_total'], 0, '.', ','); ?></td>                 
                                </tr>  
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->


            <!-- <div class="row">
              <div class="col-6">
                <div class="table-responsive">
                  <table class="table">
                    <tr>
                      <th style="width:50%">Total:</th>
                      <td class="tdColor"></strong><?php echo '$' . number_format($venta_total, 0, '.', ','); ?></td>
                    </tr>
                    <tr>
                      <th>Pagado:</th>
                      <td></strong><?php echo '$' . number_format($venta_pagado, 0, '.', ','); ?></td>
                    </tr>                      
                    <tr>
                      <?php if ($venta_metodo_pago == "Credito") { ?>
                        <th>Credito Pendiente:</th>
                      <?php } else { ?>
                        <th>Cambio:</th>
                      <?php } ?>
                      <td></strong><?php echo '$' . number_format($venta_cambio, 0, '.', ','); ?></td>
                    </tr>
                  </table>
                </div>
              </div>
               /.col -->

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
              <input type="hidden" name="cliente_numero_documento" value="<?php echo $cliente_numero_documento ?>">
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
                      <i class="fab fa-whatsapp"></i> WhatsApp
                  </a><!-- intentar que abra whatssap WEB -->
                  
               
                  <!-- <div id="ID_mostrar_info"></div> -->
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