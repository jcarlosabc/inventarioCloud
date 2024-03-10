<?php include("../templates/header.php") ?>
<?php
if ($_POST) {

    $producto_codigo = isset($_POST['producto_codigo']) ? $_POST['producto_codigo'] : "";
    $fechaGarantia =  isset($_POST['fechaGarantia']) ? $_POST['fechaGarantia'] : "";
    $producto_nombre = isset($_POST['producto_nombre']) ? $_POST['producto_nombre'] : "";
    $producto_stock_total = isset($_POST['producto_stock_total']) ? $_POST['producto_stock_total'] : "";
    $producto_precio_compra = isset($_POST['producto_precio_compra']) ? $_POST['producto_precio_compra'] : "";
    $producto_precio_venta = isset($_POST['producto_precio_venta']) ? $_POST['producto_precio_venta'] : "";
    $producto_marca = isset($_POST['producto_marca']) ? $_POST['producto_marca'] : "";
    $producto_modelo = isset($_POST['producto_modelo']) ? $_POST['producto_modelo'] : "";
    $categoria_id = isset($_POST['categoria_id']) ? $_POST['categoria_id'] : "";  
    $proveedor_id = isset($_POST['proveedor_id']) ? $_POST['proveedor_id'] : "";  

    $idResponsable = isset($_POST['idResponsable']) ? $_POST['idResponsable'] : "";  
    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $producto_precio_compra = str_replace(array('$','.', ','), '', $producto_precio_compra);
    $producto_precio_venta = str_replace(array('$','.', ','), '', $producto_precio_venta);

    $sentencia = $conexion->prepare("INSERT INTO producto(
    producto_id, producto_codigo, producto_fecha_creacion, producto_fecha_garantia, 
    producto_nombre, producto_stock_total, producto_precio_compra,
    producto_precio_venta, producto_marca, producto_modelo,
    categoria_id, proveedor_id, responsable) 
    VALUES (NULL,:producto_codigo,:producto_fecha_creacion,:fechaGarantia, :producto_nombre,:producto_stock_total,:producto_precio_compra,:producto_precio_venta,:producto_marca,:producto_modelo,:categoria_id, :proveedor_id ,:responsable)");
   
    $sentencia->bindParam(":producto_codigo", $producto_codigo);
    $sentencia->bindParam(":producto_fecha_creacion", $fechaActual);
    $sentencia->bindParam(":fechaGarantia", $fechaGarantia);
    $sentencia->bindParam(":producto_nombre", $producto_nombre);
    $sentencia->bindParam(":producto_stock_total", $producto_stock_total);
    $sentencia->bindParam(":producto_precio_compra", $producto_precio_compra);
    $sentencia->bindParam(":producto_precio_venta", $producto_precio_venta);
    $sentencia->bindParam(":producto_marca", $producto_marca);
    $sentencia->bindParam(":producto_modelo", $producto_modelo);
    $sentencia->bindParam(":categoria_id", $categoria_id);
    $sentencia->bindParam(":proveedor_id", $proveedor_id);
    $sentencia->bindParam(":responsable", $idResponsable);
    
    $resultado = $sentencia->execute();    
    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Producto creado Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventariocloud/secciones/index_productos.php";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear Producto",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}

$sentencia=$conexion->prepare("SELECT * FROM `categoria`");
$sentencia->execute();
$lista_categoria=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia_prove = $conexion->prepare("SELECT * FROM proveedores");
$sentencia_prove->execute();
$lista_proveedores = $sentencia_prove->fetchAll(PDO::FETCH_ASSOC);


?>
<script>
</script>
        <br>
        <!-- general form elements -->
        <div class="card card-primary" style="margin-top:7%">
            <div class="card-header">
                <h2 class="card-title textTabla" >REGISTRE EL NUEVO PRODUCTO &nbsp;<a style="color:black" class="btn btn-warning" href="<?php echo $url_base;?>secciones/index_productos.php">Lista de Productos</a></h2>
            </div>
            <br>
              <!-- form start --> 
            <form action="" method="post" id="formProducto" onsubmit="return validarFormulario(1)">
                <input type="hidden" value="<?php $_SESSION['usuario_id'] ?>" name="idResponsable">
                <div class="card-body ">
                    <div class="row" style="justify-content:center">                        
                        <div class="col-2">
                            <div class="form-group">
                                <label class="textLabel">Código de Barra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-crearProducto"><i class="fas fa-barcode"></i></button>
                                <input type="text" class="form-control camposTabla" name="producto_codigo">
                                <div class="modal fade" id="modal-crearProducto">
                                    <div class="modal-dialog">
                                        <div class="modal-content bg-default" style="width: 115%;">
                                            <div class="modal-header" style="text-align:center">
                                                <h4 class="modal-title">Escanear Codigo</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span> </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="barcode">
                                                    <video id="barcodevideo" autoplay></video>
                                                    <canvas id="barcodecanvasg" ></canvas>
                                                </div>
                                                <canvas id="barcodecanvas" ></canvas>
                                                <div id="result"></div> 
                                                <a class="btn btn-app" onclick="copiarContenido()">
                                                    <i class="far fa-clipboard"></i> Copiar
                                                </a>
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cerrar</button>
                                                <button type="button" class="btn btn-outline-light">Siguiente</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_nombre" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Marca</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_marca" required>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_modelo">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Categoría</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <div class="form-group camposTabla">
                                    <select class="form-control select2 camposTabla" style="width: 100%;" name="categoria_id">                                    
                                        <option value="">Escoger Categoría</option> 
                                        <?php foreach ($lista_categoria as $registro) {?>   
                                            <option value="<?php echo $registro['categoria_id']; ?>"><?php echo $registro['categoria_nombre']; ?></option> 
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Proveedor</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <div class="form-group">
                                    <select class="form-control select2 camposTabla" style="width: 100%;" name="proveedor_id">                                    
                                        <option value="">Escoger tu proveedor</option> 
                                        <?php foreach ($lista_proveedores as $registro) {?>   
                                            <option value="<?php echo $registro['id_proveedores']; ?>"><?php echo $registro['nombre_proveedores']; ?></option> 
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Fecha de Garantía</label>
                                <div class="input-group date" id="fechaGarantia" data-target-input="nearest">
                                    <input name ="fechaGarantia" type="text" class="form-control datetimepicker-input camposTabla" data-target="#fechaGarantia" />
                                    <div class="input-group-append" data-target="#fechaGarantia" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Stock o Existencias</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                            <input type="number" class="form-control camposTabla_stock" name="producto_stock_total" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="producto_precio_compra" class="textLabel">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                            <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="producto_precio_compra" id="producto_precio_compra" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="producto_precio_venta" class="textLabel">Precio de Venta</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                            <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="producto_precio_venta" id="producto_precio_venta" required>
                        </div>
                    </div>
                </div>
                <br>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                  <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
                  <a class="btn btn-danger btn-lg" href="index_productos.php" role="button">Cancelar</a>
                </div>
            </form>
        </div>
        <style>
            span.select2-selection.select2-selection--single{
                height: 38px;
            }
        </style>
<?php include("../templates/footer.php") ?>