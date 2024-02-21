<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");

if ($_POST) {
    
    $producto_codigo = isset($_POST['producto_codigo']) ? $_POST['producto_codigo'] : "";
    $producto_nombre = isset($_POST['producto_nombre']) ? $_POST['producto_nombre'] : "";
    $producto_stock_total = isset($_POST['producto_stock_total']) ? $_POST['producto_stock_total'] : "";
    $producto_precio_compra = isset($_POST['producto_precio_compra']) ? $_POST['producto_precio_compra'] : "";
    $producto_precio_venta = isset($_POST['producto_precio_venta']) ? $_POST['producto_precio_venta'] : "";
    $producto_marca = isset($_POST['producto_marca']) ? $_POST['producto_marca'] : "";
    $producto_modelo = isset($_POST['producto_modelo']) ? $_POST['producto_modelo'] : "";
    $categoria_id = isset($_POST['categoria_id']) ? $_POST['categoria_id'] : "";   
        

    
    $sentencia = $conexion->prepare("INSERT INTO producto(
    producto_id,
    producto_codigo, 
    producto_nombre,
    producto_stock_total,
    producto_precio_compra,
    producto_precio_venta,
    producto_marca,
    producto_modelo,
    categoria_id) 
    VALUES (NULL,:producto_codigo, :producto_nombre,:producto_stock_total,:producto_precio_compra,:producto_precio_venta,:producto_marca,:producto_modelo,:categoria_id)");
   
   $sentencia->bindParam(":producto_codigo", $producto_codigo);
    $sentencia->bindParam(":producto_nombre", $producto_nombre);
    $sentencia->bindParam(":producto_stock_total", $producto_stock_total);
    $sentencia->bindParam(":producto_precio_compra", $producto_precio_compra);
    $sentencia->bindParam(":producto_precio_venta", $producto_precio_venta);
    $sentencia->bindParam(":producto_marca", $producto_marca);
    $sentencia->bindParam(":producto_modelo", $producto_modelo);
    $sentencia->bindParam(":categoria_id", $categoria_id);
    
    
    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "¡Producto Creado Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href="http://localhost/inventariocloud/secciones/productos/"
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
?>

<br>

          <!-- left column -->
          <div class="">
            <!-- general form elements -->
            <div class="card card-primary" style="margin-top:7%">
              <div class="card-header">
                <h3 class="card-title textTabla" >REGISTRE EL NUEVO PRODUCTO</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form action="" method="post" enctype="multipart/form-data">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Nombre del Producto</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" 
                                class="form-control"
                                name="producto_nombre"  
                                id="producto_nombre"                                
                                >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Codigo de Barra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-default">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <input type="text" class="form-control camposTabla"  name="producto_codigo"  id="producto_codigo">
                                <div class="modal fade" id="modal-default">
                                    <div class="modal-dialog">
                                    <div class="modal-content bg-default" style="width: 115%;">
                                        <div class="modal-header" style="text-align:center">
                                            <h4 class="modal-title">Escanear Codigo</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="barcode">
                                                <video id="barcodevideo" autoplay></video>
                                                <canvas id="barcodecanvasg" ></canvas>
                                            </div>
                                            <canvas id="barcodecanvas" ></canvas>
                                            <div id="result"></div> 
                                            <a class="btn btn-app" id="miBoton" onclick="copiarContenido()">
                                                <i class="far fa-clipboard"></i> Copiar
                                            </a>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cerrar</button>
                                            <button type="button" class="btn btn-outline-light">Siguiente</button>
                                        </div>
                                    </div>
                                <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Categoria</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <div class="form-group">
                                <select class="form-control select2 camposTabla" style="width: 100%;" name="categoria_id">                                    
                                    <?php foreach ($lista_categoria as $registro) {?>                 
                        <option value="<?php echo $registro['categoria_id']; ?>"><?php echo $registro['categoria_nombre']; ?></option> 
                         <?php } ?>
                                
                                  
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control" placeholder="000.000" name="producto_precio_compra" id="producto_precio_compra">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Precio de Venta</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control" placeholder="000.000" name="producto_precio_venta" id="producto_precio_venta">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Stock o Existencias</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control" name="producto_stock_total" id="producto_stock_total">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Marca</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control" name="producto_marca" id="producto_marca">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control"  name="producto_modelo" id="producto_modelo">
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                  <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>


<?php include("../../templates/footer_content.php") ?>