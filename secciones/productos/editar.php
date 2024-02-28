<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT * FROM producto WHERE producto_id=:producto_id");
    $sentencia->bindParam(":producto_id",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $producto_id=$registro["producto_id"];
    $producto_codigo=$registro["producto_codigo"];
    $producto_nombre=$registro["producto_nombre"];
    $producto_precio_compra=$registro["producto_precio_compra"];  
    $producto_precio_venta=$registro["producto_precio_venta"];  
    $producto_stock_total=$registro["producto_stock_total"];  
    $producto_marca=$registro["producto_marca"];  
    $producto_modelo=$registro["producto_modelo"];  
    $categoria_id=$registro["categoria_id"];  
}

    // Obtener la categoría actual del producto
    $sentencia_categoria = $conexion->prepare("SELECT p.categoria_id, c.categoria_nombre 
                                            FROM producto p
                                           JOIN categoria c ON p.categoria_id = c.categoria_id
                                           WHERE p.producto_id=:producto_id");
    $sentencia_categoria->bindParam(":producto_id", $producto_id);
    $sentencia_categoria->execute();
    $categoria_actual = $sentencia_categoria->fetch(PDO::FETCH_ASSOC);

    // Obtener todas las categorías disponibles
    $sentencia_todas = $conexion->prepare("SELECT categoria_id, categoria_nombre FROM categoria");
    $sentencia_todas->execute();
    $categorias_disponibles = $sentencia_todas->fetchAll(PDO::FETCH_ASSOC);



if ($_POST) {
    $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
    $codigo_barra= isset($_POST['producto_codigo']) ? $_POST['producto_codigo'] : "";
    $producto_nombre= isset($_POST['producto_nombre']) ? $_POST['producto_nombre'] : "";
    $producto_stock_total= isset($_POST['producto_stock_total']) ? $_POST['producto_stock_total'] : "";
    $producto_precio_compra= isset($_POST['producto_precio_compra']) ? $_POST['producto_precio_compra'] : "";
    $producto_precio_venta= isset($_POST['producto_precio_venta']) ? $_POST['producto_precio_venta'] : "";
    $producto_marca= isset($_POST['producto_marca']) ? $_POST['producto_marca'] : "";
    $producto_modelo= isset($_POST['producto_modelo']) ? $_POST['producto_modelo'] : "";
    $categoria_id= isset($_POST['categoria_id']) ? $_POST['categoria_id'] : "";

    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $producto_precio_compra = str_replace(array('$', ','), '', $producto_precio_compra);
    $producto_precio_venta = str_replace(array('$', ','), '', $producto_precio_venta);
     print_r($_POST);
     
    $sentencia_edit = $conexion->prepare("UPDATE producto SET 
    producto_codigo=:producto_codigo,
    producto_nombre=:producto_nombre,
    producto_stock_total=:producto_stock_total,
    producto_precio_compra=:producto_precio_compra,
    producto_precio_venta=:producto_precio_venta,
    producto_marca=:producto_marca,
    producto_modelo=:producto_modelo,
    categoria_id=:categoria_id
    WHERE producto_id =:producto_id");

    $sentencia_edit->bindParam(":producto_id", $txtID);
    $sentencia_edit->bindParam(":producto_codigo", $codigo_barra);
    $sentencia_edit->bindParam(":producto_nombre", $producto_nombre);
    $sentencia_edit->bindParam(":producto_stock_total", $producto_stock_total);
    $sentencia_edit->bindParam(":producto_precio_compra", $producto_precio_compra);
    $sentencia_edit->bindParam(":producto_precio_venta", $producto_precio_venta);
    $sentencia_edit->bindParam(":producto_marca", $producto_marca);
    $sentencia_edit->bindParam(":producto_modelo", $producto_modelo);
    $sentencia_edit->bindParam(":categoria_id", $categoria_id);
    
    $resultado_edit = $sentencia_edit->execute();
    if ($resultado_edit) {
        echo '<script>
        Swal.fire({
            title: "¡Producto Actualizado Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventariocloud/secciones/productos/";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Actualizar el Producto",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>
<br>
<script>

</script>

          <!-- left column -->
          <div class="">
            <!-- general form elements -->
            <div class="card card-warning" style="margin-top:7%">
              <div class="card-header">
                <h3 class="card-title textTabla">EDITE EL PRODUCTO</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form action="" method="POST">
                <div class="card-body ">
                    <div class="row">
                    <input type="hidden" class="form-control" name="txtID"id="txtID"value="<?php echo $producto_id;?>" >
                    <input type="hidden" class="form-control" name="categoria_id" id="categoria_id" value="<?php echo $categoria_id;?>" >
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="textLabel">Nombre del Producto</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text"class="form-control camposTabla" name="producto_nombre" id="producto_nombre"value="<?php echo $producto_nombre;?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="" class="textLabel">Codigo de Barra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal_edit_qr">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <input type="text" class="form-control camposTabla" name="producto_codigo" id="producto_codigo" required value="<?php echo $producto_codigo;?>">
                                <div class="modal fade" id="modal_edit_qr">
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
                                <label for="" class="textLabel">Categoria</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <div class="form-group">
                                    <select class="form-control select2 camposTabla" name="categoria_id" style="width: 100%">
                                    <?php
                                        foreach ($categorias_disponibles as $categoria) {
                                            $selected = ($categoria["categoria_id"] == $categoria_actual["categoria_id"]) ? "selected" : "";
                                            echo '<option value="' . $categoria["categoria_id"] . '" ' . $selected . '>' . $categoria["categoria_nombre"] . '</option>';
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="" class="textLabel">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="texto" class="form-control camposTabla_dinero" placeholder="$000.00" name="producto_precio_compra" id="producto_precio_compra"
                                value="<?php echo '$' . number_format($producto_precio_compra, 2, '.', ','); ?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="" class="textLabel">Precio de Venta</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="texto" class="form-control camposTabla_dinero"placeholder="$000.00" name="producto_precio_venta"id="producto_precio_venta"
                                    value="<?php echo '$' . number_format($producto_precio_venta, 2, '.', ','); ?>">                                 
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="" class="textLabel">Stock o Existencias</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="number" class="form-control camposTabla_stock" name="producto_stock_total" id="producto_stock_total"
                                    value="<?php echo $producto_stock_total;?>" >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="" class="textLabel">Marca</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_marca" id="producto_marca" value="<?php echo $producto_marca;?>" >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="" class="textLabel">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_modelo" id="producto_modelo" value="<?php echo $producto_modelo;?>">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-success btn-lg"> Actualizar </button>
                    <a class="btn btn-danger btn-lg" href="index.php" role="button">Cancelar</a>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
<?php include("../../templates/footer_content.php") ?>