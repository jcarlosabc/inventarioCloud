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
    // PRINCIPAL PARA EL SELECT 
    $sentencia_venta = $conexion->prepare("SELECT venta.*, venta_detalle.*, producto.*, producto.producto_codigo
    FROM venta 
    JOIN venta_detalle ON venta.venta_codigo = venta_detalle.venta_codigo
    JOIN producto ON venta_detalle.producto_id = producto.producto_id
    WHERE venta_detalle.venta_codigo = :venta_codigo");

    $sentencia_venta->bindParam(":venta_codigo",$venta_codigo);
    $sentencia_venta->execute();
    $sentencia_venta = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);
    $sentencia_venta = $sentencia_venta->fetchAll(PDO::FETCH_ASSOC);
    $detalle_venta = $sentencia_venta;

}


if ($_POST) {
    if(isset($_GET['link'])){
        $link=(isset($_GET['link']))?$_GET['link']:"";
     }
    $venta_codigo = isset($_POST['venta_codigo']) ? $_POST['venta_codigo'] : "";
    $valor_seleccionado = $_POST['producto_id'];
    // Separar los dos valores
    $partes = explode('-', $valor_seleccionado);
    $producto_id = $partes[0];
    error_reporting(E_ERROR | E_PARSE) ;$producto_codigo = $partes[1];

    $devolucion_motivo = isset($_POST['devolucion_motivo']) ? $_POST['devolucion_motivo'] : "";
    $devolucion_serial = isset($_POST['devolucion_serial']) ? $_POST['devolucion_serial'] : "";
    $responsable = $_SESSION['usuario_id'];
    
    $monto_devolucion = isset($_POST['monto_devolucion']) ? $_POST['monto_devolucion'] : "";
    $monto_devolucion = str_replace(array('$','.',','), '', $monto_devolucion); 
    if ($monto_devolucion) {
        $devolucion_motivo = isset($_POST['devolucion_observaciones']) ? $_POST['devolucion_observaciones'] : "";
        $valor_seleccionado = $_POST['producto_id_dinero'];
        // Separar los dos valores
        $partes = explode('-', $valor_seleccionado);
        $producto_id = $partes[0];
        $producto_codigo = $partes[1];

        $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE link = :link ORDER BY id DESC");
        $sentencia->bindParam(":link", $link);
        $sentencia->execute();
        $lista_ultimo_update=$sentencia->fetch(PDO::FETCH_LAZY);
        $id_dinero = $lista_ultimo_update['id'];
        $dinero = $lista_ultimo_update['dinero'];
        $dinero = $dinero - $monto_devolucion;

        $sql = "UPDATE dinero_por_quincena SET dinero = ? WHERE id = ?";
        $sentencia = $conexion->prepare($sql);
        $params = array(
            $dinero,
            $id_dinero
        );
        $sentencia->execute($params);
    }
    
            // Insertar los datos de la devolución en la base de datos para este producto
            $sql = "INSERT INTO devolucion (venta_codigo, producto_id, producto_codigo, devolucion_fecha, 
                devolucion_motivo, devolucion_serial, devolucion_hora, monto_devolucion, link, responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
                $venta_codigo,
                $producto_id,
                $producto_codigo,
                $fecha_actual,
                $devolucion_motivo,
                $devolucion_serial,
                $horaActual,
                $monto_devolucion,
                $link,
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
                        window.location.href = "'.$url_base.'secciones/'.$index_devoluciones_link.'";
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
?>

<br>
<div class="invoice p-4 mb-3">
    <form method="post">
        <input type="hidden" name="venta_codigo" value="<?php echo $venta_codigo ?>">
        <div class="row">
            <div class="col-12">
                <h4>
                    <i class="fas fa-retweet"></i> DEVOLUCIONES
                    <small class="float-right"><?php echo $venta_fecha;?></small>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h4 class="text-info">
                <a href="#" id="cambiar_metodo_btn"><small class="float-right btn btn badge bg-info">CAMBIAR MÉTODO</small></a>
                </h4>
            </div>
        </div>
        <!-- info row -->
        <div class="row invoice-info" style="font-size: 18px;margin-top: -32px;">
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
        <div class="campos_a_ocultar">
        <span class="badge bg-info" style="font-size: 16px;">Devolución de Articulo</span>
        <br><br>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label class="textLabel">Escoger Producto a Devolver</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                    <div class="form-group camposTabla">
                    <select class="form-control select2 camposTabla" style="width: 100%;" name="producto_id">                                    
                        <option value="">Escoger Producto</option> 
                        <?php foreach ($detalle_venta as $registro) { ?>
                            <option value="<?php echo $registro['producto_id'] . '-' . $registro['producto_codigo']?>"><?php echo "Prod: (" . $registro['venta_detalle_descripcion'] . ") Cant: (" . $registro['venta_detalle_cantidad'] . ") Precio x Unid: (" . '$' . number_format($registro['venta_detalle_precio_venta'], 0, '.', ',') . ")" ?></option> 
                        <?php } ?>
                    </select>
                    </div>
                </div>
            </div>
        </div>
       <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label class="textLabel">Motivo</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                    <textarea class="form-control camposTabla" required name="devolucion_motivo" style="height: 48px;" cols="15" rows="2">n/a</textarea>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label class="textLabel">Serial/Referencia</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                    <div class="form-group camposTabla">
                        <input type="text" class="form-control camposTabla" value="n/a" style="width: 83%;" required name="devolucion_serial">
                    </div>
                </div>
            </div>
       </div>
        </div> 

        <!-- Contenedor para los nuevos campos que aparecerán -->
         <div class="campos_adicionales" style="display: none;">
        <span class="badge bg-success" style="font-size: 16px">Devolución de Dinero</span>
        <br><br>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label class="textLabel">Escoger Producto a Devolver</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <div class="form-group camposTabla">
                        <select class="form-control select2 camposTabla" style="width: 100%;" name="producto_id_dinero">                                    
                            <option value="">Escoger Producto</option> 
                            <?php foreach ($detalle_venta as $registro) { ?>
                                <option value="<?php echo $registro['producto_id'] . '-' . $registro['producto_codigo']?>"><?php echo "Prod: (" . $registro['venta_detalle_descripcion'] . ") Cant: (" . $registro['venta_detalle_cantidad'] . ") Precio x Unid: (" . '$' . number_format($registro['venta_detalle_precio_venta'], 0, '.', ',') . ")" ?></option> 
                            <?php } ?>
                        </select>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label class="textLabel">Monto</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <input type="text" class="form-control camposTabla_dinero" value="0" required name="monto_devolucion" id="montoDevolucion">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-7">
                    <div class="form-group">
                        <label class="textLabel">Observaciones</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <textarea class="form-control" required name="devolucion_observaciones" rows="2">n/a</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row no-print">
            <div class="col-12">
                <button type="submit" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fa fa-retweet"></i> Guardar Devolución
                </button>
            </div>
        </div> 
    </form>
<style>
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
</style>
<?php include("../templates/footer.php") ?>