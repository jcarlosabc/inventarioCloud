<?php include("../templates/header.php") ?>
<?php 
 if ($_SESSION['valSudoAdmin']) {
    $crear_abono_link  = "index_pendientes.php";
  
 }else{
    $crear_abono_link  = "index_pendientes.php?link=".$link;
 }
 $buscando_usuarioId = $_SESSION['usuario_id'];
 $buscando_usuario = $conexion->prepare("SELECT * FROM usuario WHERE usuario_id=:usuario_id");
 $buscando_usuario->bindParam(":usuario_id",$buscando_usuarioId);
 $buscando_usuario->execute();
 $encontrado_usuario = $buscando_usuario->fetch(PDO::FETCH_LAZY);

if ($encontrado_usuario) {
    $caja_asignada = $encontrado_usuario['caja_id'];
}

if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT hc.historial_abono, hc.historial_fecha, hc.historial_hora, hc.historial_dinero_pendiente, hc.historial_id_dnp, hc.metodo_pago, 
    v.venta_id, v.venta_total,v.venta_cambio, v.venta_fecha, v.venta_hora, v.estado_venta, 
    c.cliente_id,c.cliente_nombre,c.cliente_apellido, c.cliente_telefono, c.cliente_nit FROM historial_credito hc JOIN venta v ON hc.historial_venta_id = v.venta_id LEFT JOIN cliente c ON v.cliente_id = c.cliente_id WHERE hc.historial_venta_codigo = :venta_codigo;");
    $sentencia->bindParam(":venta_codigo",$txtID);
    $sentencia->execute();
    $listaAbonos=$sentencia->fetchAll(PDO::FETCH_ASSOC);
    $venta_codigo= $txtID;

    if ($listaAbonos) {
      $clienteId= $listaAbonos[0]['cliente_id'];
      $venta_id = $listaAbonos[0]['venta_id'];
      $venta_cambio = $listaAbonos[0]['venta_cambio'];
      $historial_id_dnp = $listaAbonos[0]['historial_id_dnp'];
  } 
}

if ($_POST) {
    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d-m-Y");
    $horaActual = date("h:i a");

    $historial_credito = $conexion->prepare("SELECT * FROM `historial_credito`");
    $historial_credito->execute();
    $lista_historial=$historial_credito->fetchAll(PDO::FETCH_ASSOC);

    $venta_id= isset($_POST['venta_id']) ? $_POST['venta_id'] : "";    
    $venta_codigo= isset($_POST['venta_codigo']) ? $_POST['venta_codigo'] : "";
    $cliente_id= isset($_POST['cliente_id']) ? $_POST['cliente_id'] : "";
    $historial_abono= isset($_POST['historial_abono']) ? $_POST['historial_abono'] : "";
    $historial_abono = str_replace(array('$','.',','), '', $historial_abono); 
    $venta_cambio= isset($_POST['venta_cambio']) ? $_POST['venta_cambio'] : "";    
    $responsable = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id']  : 0;
    $metodo_pago_abono= isset($_POST['metodo_pago_abono']) ? $_POST['metodo_pago_abono'] : "";    
    $historial_id_dnp= isset($_POST['historial_id_dnp']) ? $_POST['historial_id_dnp'] : "";    
    $fecha_abono=$fechaActual;
    $hora_abono=$horaActual;

    if ($metodo_pago_abono == 0) {
      $metodo_pago = "Efectivo";
    }else if($metodo_pago_abono == 1){
      $banco_transferencia= isset($_POST['banco_transferencia']) ? $_POST['banco_transferencia'] : "";    
      if ($banco_transferencia == 00) {
          $metodo_pago = "Davivienda";
        }else if ($banco_transferencia == 01) {
        $metodo_pago = "Bancolombia";
      }else if ($banco_transferencia == 02) {
        $metodo_pago = "Nequi";
      }
  }

    if ($lista_historial) {
        $historial_dinero_pendiente = $venta_cambio + $historial_abono;
        $sql = "INSERT INTO historial_credito (historial_id_dnp, historial_venta_id, historial_venta_codigo, 
                historial_cliente_id, historial_abono, historial_dinero_pendiente, metodo_pago, 
                historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
            $historial_id_dnp,
            $venta_id, 
            $venta_codigo, 
            $cliente_id, 
            $historial_abono,
            $historial_dinero_pendiente,
            $metodo_pago, 
            $fecha_abono, 
            $hora_abono,
            $responsable
          );
        $sentencia->execute($params);

        $venta_cambio_positivo = abs($venta_cambio);
        $estado = 0;
        if ($historial_abono >= $venta_cambio_positivo) {
          $estado = 1;
        }

        $sentencia_venta = $conexion->prepare("UPDATE venta SET 
        venta_pagado = venta_pagado+:historial_abono,
        venta_cambio = venta_cambio+:historial_cambio,
        estado_venta=:estado
        WHERE venta_id = :venta_id AND venta_codigo = :venta_codigo");

        $sentencia_venta->bindParam(":venta_id", $venta_id);
        $sentencia_venta->bindParam(":historial_abono", $historial_abono);
        $sentencia_venta->bindParam(":historial_cambio", $historial_abono);
        $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);
        $sentencia_venta->bindParam(":estado", $estado);
        $resultado_upd = $sentencia_venta->execute();  
            
          if ($resultado_upd) {
            echo '<script>
            Swal.fire({
                title: "Abono Procesado Correctamente!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result) => {
                if(result.isConfirmed){
                  window.location.href = "'.$url_base.'secciones/'.$crear_abono_link.'";
                }
            })
            </script>';
          }else {
            echo '<script>
            Swal.fire({
                title: "Error al Procesar Cuota",
                icon: "error",
                confirmButtonText: "¡Entendido!"
            });
            </script>';
          }
      } 
      
         // Buscando caja del vendedor actual
        $sentencia=$conexion->prepare("SELECT * FROM caja WHERE caja_id = :caja_id ");
        $sentencia->bindParam(":caja_id",$caja_asignada);
        $sentencia->execute();
        $result_caja=$sentencia->fetch(PDO::FETCH_LAZY);
        if ($result_caja) {
          $result_cajaId = $result_caja['caja_id'];
          // efectivo
          $caja_efectivo = $result_caja['caja_efectivo'];
          $caja_efectivo = $caja_efectivo + $historial_abono;
          // davivienda
          $caja_davivienda = $result_caja['davivienda'];
          $caja_davivienda = $caja_davivienda + $historial_abono;
          // bancolombia
          $caja_bancolombia = $result_caja['bancolombia'];
          $caja_bancolombia = $caja_bancolombia + $historial_abono;
          // nequi
          $caja_nequi = $result_caja['nequi'];
          $caja_nequi = $caja_nequi + $historial_abono;
        }

        $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE id =:historial_id_dnp ");
        $sentencia->bindParam(":historial_id_dnp",$historial_id_dnp);
        $sentencia->execute();
        $result_idDnp = $sentencia->fetch(PDO::FETCH_LAZY);
        if ($result_idDnp) {
          $encontrando_id_dnp = $result_idDnp['id'];
          $encontrando_dinero = $result_idDnp['dinero'];
        }

        $resultadoSuma = $encontrando_dinero += $historial_abono;
        $sql = "UPDATE dinero_por_quincena SET dinero = ? WHERE id= ?";
        $sentencia = $conexion->prepare($sql);
        $params = array($resultadoSuma, $encontrando_id_dnp);
        $sentencia->execute($params);

        if ($metodo_pago_abono == 0) {
         
            // Actualizando el dinero de la caja Efectivo
            $sql = "UPDATE caja SET caja_efectivo = ? WHERE caja_id = ? AND link = ? ";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_efectivo, $result_cajaId, $link );
            $sentencia->execute($params);

            $sql = "UPDATE dtpmp SET efectivo = ?";
            $sentencia = $conexion->prepare($sql);
            $params = array($caja_efectivo);
            $sentencia->execute($params);

        }else if($metodo_pago_abono == 1){
            $banco_transferencia= isset($_POST['banco_transferencia']) ? $_POST['banco_transferencia'] : "";    
            if ($banco_transferencia == 00) {

              // Actualizando el dinero de la caja Davivienda
              $sql = "UPDATE caja SET davivienda = ? WHERE caja_id = ? AND link = ? ";
              $sentencia = $conexion->prepare($sql);
              $params = array($caja_davivienda, $result_cajaId, $link );
              $sentencia->execute($params);

              $sql = "UPDATE dtpmp SET davivienda = ?";
              $sentencia = $conexion->prepare($sql);
              $params = array($caja_davivienda);
              $sentencia->execute($params);

            }else if ($banco_transferencia == 01) {

              // Actualizando el dinero de la caja Bancolombia
              $sql = "UPDATE caja SET bancolombia = ? WHERE caja_id = ? AND link = ? ";
              $sentencia = $conexion->prepare($sql);
              $params = array($caja_bancolombia, $result_cajaId, $link );
              $sentencia->execute($params);
              
              $sql = "UPDATE dtpmp SET bancolombia = ?";
              $sentencia = $conexion->prepare($sql);
              $params = array($caja_bancolombia);
              $sentencia->execute($params);

            }else if ($banco_transferencia == 02) {

              // Actualizando el dinero de la caja Nequi
              $sql = "UPDATE caja SET nequi = ? WHERE caja_id = ? AND link = ? ";
              $sentencia = $conexion->prepare($sql);
              $params = array($caja_nequi, $result_cajaId, $link );
              $sentencia->execute($params);

              $sql = "UPDATE dtpmp SET nequi = ?";
              $sentencia = $conexion->prepare($sql);
              $params = array($caja_nequi);
              $sentencia->execute($params);
            }
        }
    }

?>

  <!-- general form elements -->
    <div class="card card-warning" style="margin-top:7%">
          <img src="../dist/img/logos/logofernando.jpg" style="width: 88px;margin-left: 20%;margin-bottom: 1%;" alt="AdminLTE Logo" class="float-right brand-image img-circle elevation-3">

      <div class="card-header ">
          <h3 class="card-title textTabla">INFORMACIÓN DEL CREDITO</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start --> 
      <form action="" method="POST" id="Historial_credito">
        <div class="card-body ">
          <div class="row text-center justify-content-center">
              <div class="">
                <div class="table-responsive">                    
                  <table class="table">
                      <tr>
                          <th></th>
                            <td></td>
                          <th> <h5>Información del Cliente</h5></th>
                            <td></td>
                          <th></th>
                            <td></td>
                        </tr>
                      <?php 
                        // Controlando que esos datos se hayan mostrado antes
                        $clienteMostrado = false; 
                        $ventasMostrado = false; 
                        foreach ($listaAbonos as $registro) { 
                          if (!$clienteMostrado) { ?>
                            <tr>
                                <th>Cliente:</th>
                                  <td></strong><?php echo $registro['cliente_nombre'] ."  ". $registro['cliente_apellido']; ?></td>
                                <th>CC:</th>
                                  <td></strong><?php echo $registro['cliente_nit']; ?></td>
                                <th>Teléfono:</th>
                                  <td></strong><?php echo $registro['cliente_telefono']; ?></td>
                            </tr>
                          <?php $clienteMostrado = true; } // indica que el cliente ya se ha mostrado ?>

                          <style>
                            #factura_pagada{
                              position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                pointer-events: none; /* Para evitar que la marca de agua interfiera con la interacción del usuario */
                                opacity: 0.1; 
                            }
                          </style>
                          <?php if (!$ventasMostrado ) { ?>
                            <?php if ($registro['estado_venta'] == 1) { ?>
                            <img src="../dist/img/pagada/pagada.png" id="factura_pagada" alt="">
                            <?php } ?>
                            <tr>
                              <th></th>
                                <td></td>
                              <th> </th>
                                <td></td>
                              <th></th>
                                <td></td>
                            </tr>
                            <tr>
                              <th></th>
                                <td></td>
                              <th> <h5>Información del la Venta</h5></th>
                                <td></td>
                              <th></th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Codigo de Venta:</th>
                                <td></strong><?php echo $venta_codigo; ?></td>
                                <th>Fecha de la Compra:</th>
                                <td></strong><?php echo $registro['venta_fecha']." / ".$registro['venta_hora']; ?></td>
                                <th>Valor Total de la Venta:</th>
                                <td class="tdColor"></strong><?php echo '$' . number_format($registro['venta_total'], 0, '.', ','); ?></td>
                            </tr>
                            <tr>
                              <th></th>
                                <td></td>
                              <th> </th>
                                <td></td>
                              <th></th>
                                <td></td>
                            </tr>
                            <tr>
                              <th></th>
                                <td></td>
                              <th> <h5>Pagos Diferidos</h5></th>
                                <td></td>
                              <th></th>
                                <td></td>
                            </tr>
                            <?php $ventasMostrado = true; } ?>
                            <tr>
                              <th>Fecha:</th>
                                <td></strong><?php echo $registro['historial_fecha']." / ".$registro['historial_hora']?></td>
                                <th>Abono:</th>
                                  <td class="tdColor"></strong><?php echo '$' . number_format($registro['historial_abono'], 0, '.', ',') ." " ?> &nbsp;<span class="text-info" ><?php echo $registro['metodo_pago']; ?> </span></td>
                                <th>Pago Pendiente:</th>
                                  <td class="text-danger"></strong><?php echo '$' . number_format(abs($registro['historial_dinero_pendiente']), 0, '.', ','); ?></td>
                            </tr>
                        <?php } ?>
                      </table>
                    </div>
                    <?php if ($registro['estado_venta'] != 1) { ?>
                      <br>
                      <br>
                      <hr>
                      <div class="row" style="justify-content:center">
                        <div class="col-3">
                            <div class="form-group">
                                <label class="textLabel">Métodos de Pago</label> 
                                <div class="form-group">
                                    <select class="form-control camposTabla" id="metodoPagoAbono" name="metodo_pago_abono" onchange="mostrarOcultarPartesAbono(1)">                                    
                                        <option value="0" style="color:#22c600">Efectivo</option> 
                                        <option value="1" style="color:#009fc1">Transferencia</option> 
                                    </select>
                                </div>
                            </div>
                        </div>
                        <style>
                          #tipoTransferencia {
                              display: none;
                          }
                      </style>
                        <div class="col-3" id="tipoTransferencia">
                            <div class="form-group">
                                <label class="textLabel">Bancos</label> 
                                <div class="form-group">
                                    <select class="form-control camposTabla" name="banco_transferencia" onchange="mostrarOcultarPartesAbono()">                                    
                                        <option value="00" style="color:#22c600">Davivienda</option> 
                                        <option value="01" style="color:#009fc1">Bancolombia</option> 
                                        <option value="02" style="color:#d50000">Nequi</option>  
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                          <div class="form-group">
                                <label class="textLabel">Monto a Abonar</label> 
                                <div class="form-group">
                                  <input type="text" class="form-control tdColor camposTabla_dinero" name="historial_abono" id="historialAbono" required>
                                </div>
                            </div>
                        </div>
                      </div>
                    <?php } ?>
              </div>
              <!-- /.col -->
            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer" style="text-align:center">
            <?php if ($registro['estado_venta'] != 1) { ?>
              <button type="submit"  class="btn btn-primary btn-lg">Guardar</button>
              <a role="button" href="<?php echo $url_base;?>secciones/<?php echo $crear_abono_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
            <?php } ?>
          </div>
          <input type="hidden" name="cliente_id" value="<?php echo $clienteId?>">
          <input type="hidden" name="venta_id" value="<?php echo $venta_id?>">
          <input type="hidden" name="venta_codigo" value="<?php echo $venta_codigo?>">
          <input type="hidden" name="venta_cambio" value="<?php echo $venta_cambio?>">
          <input type="hidden" name="historial_id_dnp" value="<?php echo $historial_id_dnp?>">
      </form>
  </div>

<?php include("../templates/footer.php") ?>