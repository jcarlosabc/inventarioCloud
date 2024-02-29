<?php include("../../templates/header_content.php") ?>

<?php 
include("../../db.php");

if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("SELECT venta.*, usuario.*,cliente.* 
  FROM venta 
  INNER JOIN usuario ON venta.usuario_id = usuario.usuario_id 
  INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id WHERE venta.venta_id=:venta_id; ");

  $sentencia->bindParam(":venta_id",$txtID);
  $sentencia->execute();
  $registro=$sentencia->fetch(PDO::FETCH_LAZY);

  $venta_id=$registro["venta_id"];
  $venta_fecha=$registro["venta_fecha"];
  $venta_codigo=$registro["venta_codigo"];
  $venta_total=$registro["venta_total"];
  $venta_pagado=$registro["venta_pagado"];  
  $venta_cambio=$registro["venta_cambio"];  
  
  $caja_numero=$registro["caja_numero"];  
  $usuario_nombre=$registro["usuario_nombre"];  

  $cliente_numero_documento=$registro["cliente_numero_documento"];  
  $cliente_nombre=$registro["cliente_nombre"];  
  $cliente_apellido=$registro["cliente_apellido"];  

  $sentencia_venta=$conexion->prepare("SELECT venta.*, venta_detalle.*
  FROM venta
  INNER JOIN venta_detalle ON venta.venta_id = venta_detalle.venta_detalle_id
  WHERE venta_detalle.venta_codigo = :venta_codigo
  GROUP BY venta.venta_id");
  
  $sentencia_venta->bindParam(":venta_codigo",$venta_codigo);
  $sentencia_venta->execute();
  $detalle_venta=$sentencia_venta;
}




?>


<br>
<!-- Main content -->
<div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <i class="fa fa-shopping-basket"></i> Detalles de la Venta
                    <small class="float-right"><?php echo $venta_fecha;?></small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  <br>                
                  <address>
                    <strong>Fecha de la Venta: </strong> <?php echo $venta_fecha;?><br>                    
                    <strong>Nro. de Factura: </strong><?php echo $venta_id;?><br>
                    <strong>Codigo de Venta: </strong><?php echo $venta_codigo;?><br>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  <address>
                    <strong>Caja: </strong><?php echo $caja_numero;?><br>
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
                      <th>Qty</th>
                      <th>PRODUCTO</th>
                      <th>CANTIDAD</th>
                      <th>PRECIO</th>
                      <th>SUBTOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($detalle_venta as $registro) {?>
                <tr class="">
                  <td scope="row"><?php echo $registro['venta_id']; ?></td>
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
                <!-- accepted payments column -->
                
                <!-- /.col -->
                <div class="col-6">
                  <p class="lead">Amount Due 2/22/2014</p>

                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <th style="width:50%">Total:</th>
                        <td></strong><?php echo '$' . number_format($venta_total, 0, '.', ','); ?></td>
                      </tr>
                      <tr>
                        <th>Pagado:</th>
                        <td></strong><?php echo '$' . number_format($venta_pagado, 0, '.', ','); ?></td>
                      </tr>                      
                      <tr>
                        <th>Total:</th>
                        <td></strong><?php echo '$' . number_format($venta_cambio, 0, '.', ','); ?></td>
                      </tr>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <div class="col-12">
                  <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fas fa-download"></i> Generate PDF
                  </button>
                </div>
              </div>
            </div>
            <!-- /.invoice -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->




<?php include("../../templates/footer_content.php") ?>