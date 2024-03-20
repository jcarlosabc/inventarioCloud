<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
    $lista_producto_link  = "index_productos.php";
  
 }else{
    $lista_producto_link  = "index_productos.php?link=".$link;
 }

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

    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d/m/Y");

    if ($idResponsable == 1) {
        $sql = "INSERT INTO bodega (producto_codigo, producto_fecha_creacion,
        producto_fecha_garantia,producto_nombre, producto_stock_total,producto_precio_compra,producto_precio_venta,producto_marca,producto_modelo,
    categoria_id,proveedor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $sentencia = $conexion->prepare($sql);
    $params = array(
        $producto_codigo, 
        $fechaActual, 
        $fechaGarantia, 
        $producto_nombre,
        $producto_stock_total, 
        $producto_precio_compra,
        $producto_precio_venta, 
        $producto_marca, 
        $producto_modelo,
        $categoria_id,
        $proveedor_id
    );
    $resultado = $sentencia->execute($params);
    } else { 

        $sql = "INSERT INTO producto (producto_codigo, producto_fecha_creacion,
            producto_fecha_garantia,producto_nombre, producto_stock_total,producto_precio_compra,producto_precio_venta,producto_marca,producto_modelo,
        categoria_id,proveedor_id,responsable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $sentencia = $conexion->prepare($sql);
        $params = array(
            $producto_codigo, 
            $fechaActual, 
            $fechaGarantia, 
            $producto_nombre,
            $producto_stock_total, 
            $producto_precio_compra,
            $producto_precio_venta, 
            $producto_marca, 
            $producto_modelo,
            $categoria_id,
            $proveedor_id,
            $idResponsable 
        );
        $resultado = $sentencia->execute($params);
    }
    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Producto creado Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$lista_producto_link.'";
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

$user_id = $_SESSION['usuario_id'];
$sudo_admin = "sudo_admin";
if ($user_id == 1) {
    $sentencia_categoria = $conexion->prepare("SELECT * FROM categoria WHERE link = :link");
    $sentencia_categoria->bindParam(":link", $sudo_admin);

    $sentencia_prove = $conexion->prepare("SELECT * FROM proveedores WHERE link = :link");
    $sentencia_prove->bindParam(":link", $sudo_admin);
} else {
    $sentencia_categoria = $conexion->prepare("SELECT * FROM categoria");
    $sentencia_prove = $conexion->prepare("SELECT * FROM proveedores");
}
    $sentencia_categoria->execute();
    $lista_categoria = $sentencia_categoria->fetchAll(PDO::FETCH_ASSOC);

    $sentencia_prove->execute();
    $lista_proveedores = $sentencia_prove->fetchAll(PDO::FETCH_ASSOC);
?>
<script>
</script>
        <br>
        <!-- general form elements -->
        <article> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Primero crear <strong>categoría</strong> y registrar <strong>proveedor</strong> del producto a crear. </article>
        <br>
        <div class="row no-gutters">
            <div class="col-1">
                <a href="crear_categoria.php"><button type="button" class="btn btn-outline-primary">Crear Categoría</button></a>
            </div>
            <div class="col-2">
                <a href="crear_proveedor.php"><button type="button" class="btn btn-outline-info">Registrar Proveedor</button></a>
            </div>
        </div>
        <div class="card card-primary" style="margin-top:3%">
            <div class="card-header">
                <h2 class="card-title textTabla" >REGISTRE EL NUEVO PRODUCTO &nbsp;<a style="color:black" class="btn btn-warning" href="<?php echo $url_base;?>secciones/<?php echo $lista_producto_link;?>">Lista de Productos</a></h2>
            </div>
            <br>
              <!-- form start --> 
            <form action="" method="post" id="formProducto" onsubmit="return validarFormulario(1)">
                <input type="hidden" value="<?php echo $_SESSION['usuario_id'] ?>" name="idResponsable">
                <div class="card-body ">
                    <div class="row" style="justify-content:center">                        
                        <div class="col-2">
                            <div class="form-group">
                                <label class="textLabel">Código de Barra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-crearProducto"><i class="fas fa-barcode"></i></button>
                                <input type="text" class="form-control camposTabla" name="producto_codigo" required>
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
                      <a class="btn btn-danger btn-lg" href="<?php echo $url_base;?>secciones/<?php echo $lista_producto_link;?>" role="button">Cancelar</a>
                </div>
            </form>
        </div>
        <style>
            span.select2-selection.select2-selection--single{
                height: 38px;
            }
        </style>
<?php include("../templates/footer.php") ?>