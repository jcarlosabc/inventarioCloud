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

  }else{
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
  $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa ");
  $sentencia_empresa->execute();
  $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);

  $empresa_nombre=$registro_empresa["empresa_nombre"];  
  $empresa_telefono=$registro_empresa["empresa_telefono"];  
  $empresa_direccion=$registro_empresa["empresa_direccion"];  
  $empresa_nit=$registro_empresa["empresa_nit"];  
 
  // Mostrar lista comprados
  if($_SESSION['rolSudoAdmin']){
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*, producto_fecha_garantia
    FROM venta
    INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo 
    INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo");
    $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);

  }else{
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*, producto_fecha_garantia
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

?>
<br>

<!-- Main content -->
          <div class="invoice p-3 mb-3">
            <div class="row">
              <div class="col-12">
                <h4>
                  <i class="fa fa-shopping-basket"></i> Detalles de la Venta
                  <small class="float-right"><?php echo $venta_fecha;?></small>
                </h4>
              </div>
            </div>
            <!-- info row -->
            <div class="row invoice-info">
              <div class="col-sm-4 invoice-col">
                <br>                
                <address>
                  <strong>Fecha de la Venta: </strong> <?php echo $venta_fecha;?><br>                    
                  <strong>Nro. de Factura: </strong><?php echo $venta_id;?><br>
                  <strong>Codigo de Venta: </strong><?php echo $venta_codigo;?><br>
                  <?php if ($venta_metodo_pago == "Credito") { ?>
                    <strong>Plazo del Pago: </strong><?php echo $plazo . " " . $tiempo;?><br>
                  <?php } ?>
                </address>
              </div>
              <!-- /.col -->
              <div class="col-sm-4 invoice-col">
                <br>
                <address>
                  <strong>Caja: </strong><?php echo $caja_id;?><br>
                    <strong>Vendedor: </strong><?php echo $usuario_nombre;?><br>
                    <strong>Cliente: </strong><?php echo $cliente_nombre;?> <?php echo $cliente_apellido;?><br>
                    <strong>CC: </strong><?php echo $cliente_numero_documento;?>                    
                  </address>
                </div>               
              </div>
              <!-- /.row -->

            <!-- Table row -->
            <div class="row">
              <div class="col-12 table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
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
            <div class="row">
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
              <!-- /.col -->
            </div>
            <!-- /.row -->
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
                  <!-- <div id="ID_mostrar_info"></div> -->
                </div>
              </div>
            </form>
          </div>

          
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