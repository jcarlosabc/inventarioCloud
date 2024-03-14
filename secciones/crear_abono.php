<?php include("../templates/header.php") ?>
<?php 
if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("SELECT venta.*, cliente.*
    FROM venta
    INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id
    WHERE venta.venta_codigo = :venta_codigo;");
    $sentencia->bindParam(":venta_codigo",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);

    $venta_codigo= $txtID;
    $cuotas=$registro["partes"];
    $venta_id=$registro["venta_id"];
    $venta_total=$registro["venta_total"];
    $venta_pagado=$registro["venta_pagado"];
    $venta_cambio=$registro["venta_cambio"];
    $venta_fecha=$registro["venta_fecha"];
    $cliente_id=$registro["cliente_id"];
    $cliente_nombre=$registro["cliente_nombre"];
    $cliente_apellido=$registro["cliente_apellido"];
    $cliente_numero_documento=$registro["cliente_numero_documento"];
    $partes=$registro["partes"];
    
}

if ($_POST) {
    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d/m/Y");
    $horaActual = date("h:i a");

    $historial_credito = $conexion->prepare("SELECT * FROM `historial_credito`");
    $historial_credito->execute();
    $lista_historial=$historial_credito->fetchAll(PDO::FETCH_ASSOC);

    $venta_id= isset($_POST['venta_id']) ? $_POST['venta_id'] : "";    
    $venta_codigo= isset($_POST['venta_codigo']) ? $_POST['venta_codigo'] : "";
    $cliente_id= isset($_POST['cliente_id']) ? $_POST['cliente_id'] : "";
    $cuota= isset($_POST['cuotas']) ? $_POST['cuotas'] : "";
    $historial_abono= isset($_POST['historial_abono']) ? $_POST['historial_abono'] : "";
    $venta_cambio= isset($_POST['venta_cambio']) ? $_POST['venta_cambio'] : "";    
    $$partes= isset($_POST['$partes']) ? $_POST['$partes'] : "";    
    $responsable = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id']  : 0;
    $fecha_abono=$fechaActual;
    $hora_abono=$horaActual;

    if ($lista_historial) {

            $historial_dinero_pendiente = $venta_cambio + $historial_abono;
            $sql = "INSERT INTO historial_credito (historial_venta_id, historial_venta_codigo, 
                    historial_cliente_id, historial_abono, historial_dinero_pendiente, 
                    historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                $venta_id, 
                $venta_codigo, 
                $cliente_id, 
                $historial_abono,
                $historial_dinero_pendiente, 
                $fecha_abono, 
                $hora_abono,
                $responsable
              );
            $sentencia->execute($params);
            $partes = $partes - 1;
            $estado_venta = 0;
            if ($partes == 0) {
              $estado_venta = 1;
            }

            $sentencia_venta = $conexion->prepare("UPDATE venta SET 
            venta_pagado = venta_pagado+:historial_abono,
            venta_cambio = venta_cambio+:historial_cambio,
            partes = :partes, estado_venta=:estado_venta
            WHERE venta_id = :venta_id AND venta_codigo = :venta_codigo");

            $sentencia_venta->bindParam(":venta_id", $venta_id);
            $sentencia_venta->bindParam(":historial_abono", $historial_abono);
            $sentencia_venta->bindParam(":historial_cambio", $historial_abono);
            $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);
            $sentencia_venta->bindParam(":partes", $partes);
            $sentencia_venta->bindParam(":estado_venta", $estado_venta);
            $resultado = $sentencia_venta->execute();  
            
            if ($resultado) {
              echo '<script>
              // Código JavaScript para mostrar SweetAlert
              Swal.fire({
                  title: "Cuota Procesada Correctamente!",
                  icon: "success",
                  confirmButtonText: "¡Entendido!"
              }).then((result) => {
                  if(result.isConfirmed){
                      window.location.href = "'.$url_base.'secciones/index_pendientes.php";
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
} else {
          $historial_dinero_pendiente = $venta_cambio + $historial_abono;
            $sql = "INSERT INTO historial_credito (historial_venta_id, historial_venta_codigo, 
                    historial_cliente_id, historial_abono, historial_dinero_pendiente, 
                    historial_fecha, historial_hora, historial_responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $sentencia = $conexion->prepare($sql);
                $params = array(
                $venta_id, 
                $venta_codigo, 
                $cliente_id, 
                $historial_abono,
                $historial_dinero_pendiente, 
                $fecha_abono, 
                $hora_abono,
                $responsable
              );
            $sentencia->execute($params);

            $sentencia_venta = $conexion->prepare("UPDATE venta SET 
                  venta_pagado = venta_pagado + :historial_abono,
                  venta_cambio = venta_cambio + :historial_cambio,
                  partes = partes - 1
              WHERE venta_id = :venta_id AND venta_codigo = :venta_codigo");

            $sentencia_venta->bindParam(":venta_id", $venta_id);
            $sentencia_venta->bindParam(":historial_abono", $historial_abono);
            $sentencia_venta->bindParam(":historial_cambio", $historial_abono);
            $sentencia_venta->bindParam(":venta_codigo", $venta_codigo);
            $resultado = $sentencia_venta->execute();   

            if ($resultado) {
                echo '<script>
                // Código JavaScript para mostrar SweetAlert
                Swal.fire({
                    title: "Cuota Procesada correctamente!!",
                    icon: "success",
                    confirmButtonText: "¡Entendido!"
                }).then((result) => {
                    if(result.isConfirmed){
                        window.location.href = "'.$url_base.'secciones/index_pendientes.php";
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

    }

    

?>

<!-- left column -->
<div class="">
  <!-- general form elements -->
    <div class="card card-warning" style="margin-top:7%">
      <div class="card-header ">
          <h3 class="card-title textTabla">Abonos</h3>
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
                      <th >Codigo de Venta:</th>
                      <td></strong><?php echo $venta_codigo; ?></td>
                    
                      <th >Cliente:</th>
                      <td></strong><?php echo $cliente_nombre; ?><?php echo $cliente_apellido; ?></td>
                    </tr>
                    <tr>
                        <th >CC:</th>
                        <td></strong><?php echo $cliente_numero_documento; ?></td>
                        <th >Fecha de la Compra:</th>
                        <td></strong><?php echo $venta_fecha; ?></td>
                    </tr>
                    <tr>
                      <th >Valor Total de la Venta:</th>
                      <td></strong><?php echo $venta_total; ?></td>
                      <th>Pagado:</th>
                      <td></strong><?php echo $venta_pagado; ?></td>
                    </tr>
                    <tr>
                      <th>Pago Pendiente:</th>
                      <td class="text-danger"></strong><?php echo $venta_cambio; ?></td>
                        <th>Valor a abonar:</th>
                        <td></strong><input type="number" name="historial_abono" id="historial_abono" required></td>
                    </tr>
                  </table>
                </div>
              </div>
              <!-- /.col -->
            </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer" style="text-align:center">
              <button type="submit"  class="btn btn-primary btn-lg">Guardar</button>
              <a role="button" href="<?php echo $url_base ?>secciones/index_pendientes.php" class="btn btn-danger btn-lg">Cancelar</a>
          </div>
          <input type="hidden" name="cliente_id" value="<?php echo $cliente_id?>">
          <input type="hidden" name="venta_id" value="<?php echo $venta_id?>">
          <input type="hidden" name="venta_codigo" value="<?php echo $venta_codigo?>">
          <input type="hidden" name="venta_cambio" value="<?php echo $venta_cambio?>">
          <input type="hidden" name="cuotas" value="<?php echo $cuotas?>">
          <input type="hidden" name="partes" value="<?php echo $partes?>">
      </form>
  </div>
  <!-- /.card -->
</div>

<?php include("../templates/footer.php") ?>