<?php include("../templates/header.php") ?>
<?php 
date_default_timezone_set('America/Bogota');
$fecha_actual = date("d-m-Y");
$horaActual = date("h:i a");

if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT venta.*, usuario.*,cliente.* 
    FROM venta 
    INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
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
    
    $caja_id=$registro["caja_id"];  
    $usuario_nombre=$registro["usuario_nombre"];  

    $cliente_numero_documento=$registro["cliente_numero_documento"];  
    $cliente_nombre=$registro["cliente_nombre"];  
    $cliente_apellido=$registro["cliente_apellido"];  

    // PRINCIPAL
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*, producto.*, devolucion.*
    FROM venta
    INNER JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo
    INNER JOIN producto ON venta_detalle.producto_id = producto.producto_id
    LEFT JOIN devolucion ON devolucion.venta_codigo = venta_detalle.venta_codigo
    WHERE venta_detalle.venta_codigo = :venta_codigo;");

    $sentencia_venta->bindParam(":venta_codigo",$venta_codigo);
    $sentencia_venta->execute();
    $detalle_venta = $sentencia_venta;
}
if ($_POST) {
    $devolucion_motivos = isset($_POST['devolucion_motivo']) ? $_POST['devolucion_motivo'] : array();
    $devolucion_seriales = isset($_POST['devolucion_serial']) ? $_POST['devolucion_serial'] : array();
    $productos_a_devolver = isset($_POST['productos_a_devolver']) ? $_POST['productos_a_devolver'] : array();
    $producto_ids = isset($_POST['producto_id']) ? $_POST['producto_id'] : array();
    $venta_codigo = isset($_POST['venta_codigo']) ? $_POST['venta_codigo'] : "";
    $cantidades_devolucion = isset($_POST['cantidad_devolucion']) ? $_POST['cantidad_devolucion'] : array();
    
    $responsable = $_SESSION['usuario_id'];
    
    // Iterar sobre los productos seleccionados
    foreach ($productos_a_devolver as $producto_codigo) {
        // Obtener los datos correspondientes al producto
        $index = array_search($producto_codigo, $_POST['producto_codigo']);
        $devolucion_motivo = $devolucion_motivos[$index];
        $devolucion_serial = $devolucion_seriales[$index];
        $producto_id = $producto_ids[$index];
        $cantidad_devolucion = $cantidades_devolucion[$index];
    
        if (!$devolucion_motivo || !$devolucion_serial) {
            echo '<script>
                Swal.fire({
                    title: "Opps los campos no se pueden ir vacíos, o has seleccionado la casilla incorrecta ",
                    icon: "error",
                    confirmButtonText: "¡Entendido!"
                });
            </script>';
        } else {
    
            // Insertar los datos de la devolución en la base de datos para este producto
            $sql = "INSERT INTO devolucion (venta_codigo, producto_id, producto_codigo, devolucion_fecha, 
                devolucion_motivo, devolucion_serial, devolucion_hora, responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
                $venta_codigo,
                $producto_id,
                $producto_codigo,
                $fecha_actual,
                $devolucion_motivo,
                $devolucion_serial,
                $horaActual,
                $responsable
            );
            $resultado = $sentencia->execute($params);
            // UPDATE DE ESTADO
            $estado_devolucion = $conexion->prepare("UPDATE venta_detalle 
            SET estado_devolucion = '1'
            WHERE venta_codigo = :venta_codigo AND producto_id = :producto_id");
            $estado_devolucion->bindParam(":venta_codigo", $venta_codigo);
            $estado_devolucion->bindParam(":producto_id", $producto_id);
            $resultado = $estado_devolucion->execute();
    
            if ($resultado) {
                echo '<script>
                // Código JavaScript para mostrar SweetAlert
                Swal.fire({
                    title: "¡Devolución Exitosa!",
                    icon: "success",
                    confirmButtonText: "¡Entendido!"
                }).then((result) => {
                    if(result.isConfirmed){
                        window.location.href = "'.$url_base.'secciones/'.$ventas_link_historia_venta.'";
                    }
                })
                </script>';
            } else {
                echo '<script>
                Swal.fire({
                    title: "Error al devolver Producto",
                    icon: "error",
                    confirmButtonText: "¡Entendido!"
                });
                </script>';
            }
        }
    }
    
    }



?>

<br>
<!-- Main content -->
<div class="invoice p-3 mb-3">
    <!-- title row -->
    <form method="post" enctype="multipart/form-data">
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
                    <strong>Nro. de Factura: </strong><?php echo $venta_id;?><br>
                    <strong>Codigo de Venta: </strong><?php echo $venta_codigo;?><br>
                    <strong>Fecha de la Venta: </strong> <?php echo $venta_fecha;?><br>                                      
                </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <address>
                    <strong>Vendedor: </strong><?php echo $usuario_nombre;?><br>
                    <strong>Caja: </strong><?php echo $caja_id;?><br>
                    <strong>Cliente: </strong><?php echo $cliente_nombre;?> <?php echo $cliente_apellido;?><br>
                    <strong>CC: </strong><?php echo $cliente_numero_documento;?>                    
                </address>
            </div>               
        </div>
        <!-- /.row -->

        <!-- Table row -->
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-striped" style="text-align: center;">
                    <thead>
                        <tr>
                          <th>Qty</th>
                          <th>PRODUCTO</th>
                          <th>CANTIDAD</th>
                          <th>GARANTIA</th>
                          <th>ESTADO</th>
                          <th>SELECCIONAR</th>
                          <th>CANTIDAD A DEVOLVER</th>
                          <th>MOTIVO</th>
                          <th>SERIAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalle_venta as $registro) { 
                            $fecha_actual_str = date('d/m/Y');
                            $fecha_actual = DateTime::createFromFormat('d/m/Y', $fecha_actual_str);
                            $fecha_garantia_str = $registro['producto_fecha_garantia'];
                            $fecha_garantia = DateTime::createFromFormat('d/m/Y', $fecha_garantia_str);
                            ?>
                            <tr>
                                <td scope="row"><?php echo $registro['venta_id']; ?></td>
                                <td><?php echo $registro['venta_detalle_descripcion']; ?></td>
                                <td><?php echo $registro['venta_detalle_cantidad']; ?></td>
                                <td><?php echo $registro['producto_fecha_garantia']; ?></td>
                                <td>
                                <?php
                                    // Comparar las fechas
                                    if ($fecha_actual <= $fecha_garantia) {
                                        if ($registro['estado_devolucion'] == 0) {
                                            echo '<span class="badge bg-success style="font-size: 1.2em;">Garantia Activa</span>';                                      
                                            echo '<td><input type="checkbox" class="producto-checkbox" style="height: 22px; width: 100%;" name="productos_a_devolver[]" value="' . $registro['producto_codigo'].'"></td>';
                                            echo '<td class="cantidad-devolucion"></td>';
                                            echo '<input type="hidden" class="cantidad-devolucion-input" name="cantidad_devolucion[]" value="">';
                                        } else {
                                            echo '<span class="badge bg-info style="font-size: 1.2em;">Garantia Realizada</span>';
                                            echo '<td>♻️</td>';
                                        }
                                    } else {
                                        echo '<span class="badge bg-danger style="font-size: 1.2em;">Garantía expirada</span>';                                     
                                        echo '<td>🚫</td>';
                                    }
                                ?>
                                </td>
                                <?php
                                    if ($fecha_actual <= $fecha_garantia) {
                                        if ($registro['estado_devolucion']==0) {
                                            echo '<td><textarea name="devolucion_motivo[]"  rows="2" ></textarea></td>';
                                            echo '<td><input type="number" placeholder=""  name="devolucion_serial[]"></td>';
                                        }else{
                                             echo ' <td>'. $registro['devolucion_motivo']. '</td>';;
                                             echo ' <td>'. $registro['devolucion_serial']. '</td>';
                                         }
                                    }
                                ?>
                                <input type="hidden" name="venta_codigo" value="<?php echo $registro['venta_codigo']; ?>"> 
                                <input type="hidden" name="producto_id[]" value="<?php echo $registro['producto_id']; ?>">  
                                <input type="hidden" name="producto_codigo[]" value="<?php echo $registro['producto_codigo']; ?>">                                
                            </tr>  
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
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
                            <th>Devuelto:</th>
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
                
                <button type="submit" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fa fa-retweet"></i> Guardar Devolución
                </button>
            </div>
        </div>
    </form>
</div>
<!-- /.invoice -->
</div><!-- /.col -->
</div><!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
    var checkboxes = document.querySelectorAll('.producto-checkbox');

    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                
                var cantidad = prompt('Ingrese la cantidad de productos a devolver:', '1');
                var cantidadCelda = this.parentNode.nextElementSibling;

                cantidadCelda.textContent = 'Cantidad: ' + cantidad;

                var cantidadInput = cantidadCelda.nextElementSibling;

                cantidadInput.value = cantidad;
            }
        });
    });
</script>



<?php include("../templates/footer.php") ?>