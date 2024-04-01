<?php include("../templates/header.php") ?>
<?php 
date_default_timezone_set('America/Bogota');
$fecha_actual = date("d/m/Y");
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

    // PRINCIPAL PARA EL SELECT 
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*, producto.*, producto.producto_codigo
    FROM venta 
    JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo
    JOIN producto ON venta_detalle.producto_id = producto.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo");

    $sentencia_venta->bindParam(":venta_codigo",$venta_codigo);
    $sentencia_venta->execute();
    $sentencia_venta = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);
    $detalle_venta = $sentencia_venta;

}

if ($_POST) {
    $devolucion_motivos = isset($_POST['devolucion_motivo']) ? $_POST['devolucion_motivo'] : array();
    $devolucion_seriales = isset($_POST['devolucion_serial']) ? $_POST['devolucion_serial'] : array();
    $productos_a_devolver = isset($_POST['productos_a_devolver']) ? $_POST['productos_a_devolver'] : array();
    $producto_ids = isset($_POST['producto_id']) ? $_POST['producto_id'] : array();
    $venta_codigo = isset($_POST['venta_codigo']) ? $_POST['venta_codigo'] : "";
    
    $responsable = $_SESSION['usuario_id'];
    
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
    
?>

<br>
<!-- Main content -->
<div class="invoice p-3 mb-3">
    <!-- title row -->
    <form method="post">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fa fa-shopping-basket"></i> Detalles de la Venta
                    <small class="float-right"><?php echo $venta_fecha;?></small>
                </h4>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12">
                <h4 class="text-info">
                    <a href="#" id="cambiar_metodo_btn"><small class="float-right btn btn badge bg-info">CAMBIAR MÉTODO</small></a>
                </h4>
            </div>
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
        <br>
        <br>
        <br>
<!-- Contenedor para los campos a ocultar -->
<div class="campos_a_ocultar">
<span class="badge bg-success style=font-size: 1.2em;">Devolucion de Articulo</span>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label class="textLabel">Escoger Producto a Devolver</label>
                <div class="form-group camposTabla">
                    <select class="form-control select2 camposTabla" style="width: 100%;" name="producto_id">                                    
                        <option value="">Escoger Producto</option> 
                        <?php foreach ($detalle_venta as $registro) {?>   
                            <option value="<?php echo $registro['producto_id']; ?>"><?php echo "Prod: (" . $registro['venta_detalle_descripcion'] . ") Cant: (" . $registro['venta_detalle_cantidad'] . ") Precio x Unid: (" . '$' . number_format($registro['venta_detalle_precio_venta'], 0, '.', ',') . ")" ?></option> 
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label class="textLabel">Motivo</label>
                <textarea class="form-control camposTabla" style="height: 48px;" name="devolucion_motivo" cols="15" rows="2"></textarea>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label class="textLabel">Serial/Referencia</label>
                <div class="form-group camposTabla">
                    <input type="text" class="form-control camposTabla" name="devolucion_serial">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenedor para los nuevos campos que aparecerán -->
<div class="campos_adicionales" style="display: none;">
<span class="badge bg-success" style="font-size: 1.2em;">Devolucion de Dinero</span>
<br><br>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <label class="textLabel">Nuevo Input</label>
                <input type="text" class="form-control" name="nuevo_input">
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <label class="textLabel">Nuevo Select</label>
                <select class="form-control" name="nuevo_select">
                    <option value="opcion1">Opción 1</option>
                    <option value="opcion2">Opción 2</option>
                    <option value="opcion3">Opción 3</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <div class="form-group">
                <label class="textLabel">Nuevo Textarea</label>
                <textarea class="form-control" name="nuevo_textarea" rows="2"></textarea>
            </div>
        </div>
    </div>
</div>

        <!-- /.row -->
        <!-- this row will not appear when printing -->
        <!-- <div class="row no-print">
            <div class="col-12">
                <button type="submit" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fa fa-retweet"></i> Guardar Devolución
                </button>
            </div>
        </div> -->
    </form>
</div>
<!-- /.invoice -->
</div><!-- /.col -->
</div><!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var cambiarMetodoBtn = document.getElementById('cambiar_metodo_btn');
        var camposAOcultar = document.querySelector('.campos_a_ocultar');
        var camposAdicionales = document.querySelector('.campos_adicionales');

        cambiarMetodoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (camposAOcultar.style.display === 'none') {
                camposAOcultar.style.display = 'block';
                camposAdicionales.style.display = 'none';
            } else {
                camposAOcultar.style.display = 'none';
                camposAdicionales.style.display = 'block';
            }
        });
    });
</script>
<style>
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
</style>
<?php include("../templates/footer.php") ?>