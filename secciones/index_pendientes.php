<?php include("../templates/header.php") ?>
<?php 

  if ($_SESSION['rolSudoAdmin']) {
    $sentencia=$conexion->prepare("SELECT * FROM venta WHERE venta_metodo_pago = 2 ");
    $crear_abono_links = "crear_abono.php?txtID";
  }else if($_SESSION['rolBodega']) {
    if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";}
    $sentencia=$conexion->prepare("SELECT * FROM venta WHERE venta_metodo_pago = 2 AND link=:link");
    $sentencia->bindParam(":link",$linkeo);
    $crear_abono_links = "crear_abono.php?link=sudo_bodega&txtID";
  }else {
    if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";}
    $sentencia=$conexion->prepare("SELECT * FROM venta WHERE venta_metodo_pago = 2 AND link=:link");
    $sentencia->bindParam(":link",$linkeo);
    $crear_abono_links = "crear_abono.php?txtID";
  }
  $sentencia->execute();
  $lista_ventas_credito=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d-m-Y");


    foreach ($lista_ventas_credito as $item) {
    
      $venta_fecha = $item['venta_fecha'];
      $plazo = $item['plazo'];
      $tiempo = $item['tiempo'];

      $fechaInicio = strtotime($venta_fecha);
      $fechaActuales = strtotime(date($fechaActual));

      $diferenciaDias = ($fechaActuales - $fechaInicio) / (60*60*24);
      $plazo -= $diferenciaDias;

      if ($plazo < 0) {
          $plazo = 0;     
        }
     
  } 

?>

      <div class="card card-primary ">
        <div class="card-header text-center ">
          <h2 class="card-title textTabla">CUENTAS PENDIENTE DE LOS CLIENTES</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_usuario" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Código Venta</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Pagado</th>
              <th>Pago Pendiente</th>
              <th>Método de Pago</th>
              <th>Plazo Inicial</th>
              <th>Tiempo Restante</th>
              <th>Abonar</th>

            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
               foreach ($lista_ventas_credito as $registro) { ?>
                <tr>
                  <td scope="row"><?php $count++; echo $count;  ?></td>
                  <td><?php echo $registro['venta_codigo']; ?></td>
                  <td><?php echo $registro['venta_fecha'] . " " . $registro['venta_hora'] ; ?></td>
                  <td class="tdColor"><?php echo '$'. number_format( $registro['venta_total'] ,0, '.', ','); ?></td>                
                  <td class="tdColor"><?php echo '$'. number_format( $registro['venta_pagado'] ,0, '.', ','); ?></td>                
                  <td ><?php echo $registro['venta_cambio'] < 0 ? '$'.number_format(abs($registro['venta_cambio']) ,0, '.', ',') : "Cuenta Saldada ✅"; ?></td>
                  <td ><?php echo ($registro['venta_metodo_pago'] == 2)? "Credito": "nad"; ?></td>
                  <td><?php echo $registro['plazo']; ?> <?php if ($registro['tiempo'] == 0) { echo "Dias" ;}else {echo "Meses" ;}; ?></td>
                  <td>
                    <?php if ($registro['estado_venta'] != 1) { ?>
                        <?php echo $plazo . " " ; if ($registro['tiempo'] == 0) { echo "Dias" ;}else {echo "Meses" ;}; ?>
                        <?php if ($plazo == 20 && $plazo <= 8) { ?>
                          <article class="text-warning">
                            <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong> Quedan <strong class="text-warning">
                              <?php $venta_fecha = $registro['venta_fecha'];
                                  $plazo = $registro['plazo'];
                                  $fechaInicio = strtotime($venta_fecha);
                                  $fechaActual = time(); // Obtener la fecha actual
                                  $fechaActuales = strtotime(date("Y-m-d", $fechaActual));
                                  $diferenciaDias = ($fechaActuales - $fechaInicio) / (60*60*24);
                                  $plazo -= $diferenciaDias;
                                  // if ($plazo < 0) {$plazo = 0; }
                                  echo $plazo; ?>
                              </strong> días <br>para vencerse el plazo, del pago de esta <strong>Factura</strong>.
                          </article>
                        <?php } else if($plazo <= 8 && $plazo >= 1){ ?>
                          <article class="text-danger"><strong class="text-danger"><i class="fa fa-info-circle"></i> Recuerde: </strong>Quedan <strong class="text-danger">
                            <?php $venta_fecha = $registro['venta_fecha'];
                              $plazo = $registro['plazo'];
                              $fechaInicio = strtotime($venta_fecha);
                              $fechaActual = time(); // Obtener la fecha actual
                              $fechaActuales = strtotime(date("Y-m-d", $fechaActual));
                              $diferenciaDias = ($fechaActuales - $fechaInicio) / (60*60*24);
                              $plazo -= $diferenciaDias;
                              // if ($plazo < 0) {$plazo = 0;}
                              echo $plazo;?>
                              </strong> días <br>para vencerse el plazo, del pago de esta <strong>Factura</strong>.
                          </article>
                        <?php } else if($plazo == 0) {?>
                          <article class="text-danger"><strong class="text-danger"><i class="fa fa-info-circle"></i> Atención: </strong>Quedan <strong class="text-danger">
                            <?php $venta_fecha = $registro['venta_fecha'];
                              $plazo = $registro['plazo'];
                              $fechaInicio = strtotime($venta_fecha);
                              $fechaActual = time();
                              $fechaActuales = strtotime(date("Y-m-d", $fechaActual));
                              $diferenciaDias = ($fechaActuales - $fechaInicio) / (60*60*24);
                              $plazo -= $diferenciaDias;
                              if ($plazo < 0) {$plazo = 0;}
                              echo $plazo;?>
                              </strong> días <br>Plazo <strong>Vencido</strong>.
                          </article>
                        <?php }else if($registro['estado_venta'] == 1 ){ ?>
                          <a class="btn btn-success btn-sm" role="button" title="Pagado">
                            <i class="fa fa-check" aria-hidden="true"></i> Pagado
                          </a>
                        <?php } ?>
                      <?php } ?>
                  </td>
                  <?php if ($registro['estado_venta'] == 1) { ?>                    
                    <td>  
                      <a class="btn btn-success btn-sm" href="<?php echo $url_base ?>secciones/<?php echo $crear_abono_links ?>=<?php echo $registro['venta_codigo']; ?>" role="button" title="Pagado">
                        <i class="fa fa-check" aria-hidden="true"></i> Pagado
                      </a>
                    </td>
                    <?php } else { ?> 
                    <td>
                        <a class="btn btn-warning btn-sm" href="<?php echo $url_base ?>secciones/<?php echo $crear_abono_links ?>=<?php echo $registro['venta_codigo']; ?>" role="button" title="Abonar">
                          <i class="fa fa-credit-card" aria-hidden="true"></i> Abonar
                        </a>
                    </td>
                  <?php } ?>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>

<?php include("../templates/footer.php") ?>