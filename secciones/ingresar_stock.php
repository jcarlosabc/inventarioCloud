<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
    $lista_productos_link  = "index_productos.php";
    
}else{
    $lista_productos_link  = "index_productos.php?link=".$link;
}

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
    $sentencia_categoria = $conexion->prepare("SELECT c.categoria_nombre FROM producto p
        JOIN categoria c ON p.categoria_id = c.categoria_id WHERE p.producto_id=:producto_id");

    $sentencia_categoria->bindParam(":producto_id", $producto_id);
    $sentencia_categoria->execute();
    $registro_categoria = $sentencia_categoria->fetch(PDO::FETCH_LAZY);
    $categoria_actual =  isset($registro_categoria["categoria_nombre"]) ? $registro_categoria["categoria_nombre"] : "";


if ($_POST) {
   
    $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
    $producto_stock_total= isset($_POST['producto_stock_total']) ? $_POST['producto_stock_total'] : "";
    $producto_precio_compra= isset($_POST['producto_precio_compra']) ? $_POST['producto_precio_compra'] : "";
    $producto_precio_venta= isset($_POST['producto_precio_venta']) ? $_POST['producto_precio_venta'] : "";

    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $producto_precio_compra = str_replace(array('$','.', ','), '', $producto_precio_compra);
    $producto_precio_venta = str_replace(array('$','.', ','), '', $producto_precio_venta);
     
    $sentencia_edit = $conexion->prepare("UPDATE producto SET 
    producto_fecha_ingreso=:producto_fecha_ingreso,
    producto_stock_total=producto_stock_total+:producto_stock_total,
    producto_precio_compra=:producto_precio_compra, 
    producto_precio_venta=:producto_precio_venta
    WHERE producto_id =:producto_id");

    $sentencia_edit->bindParam(":producto_id", $txtID);
    $sentencia_edit->bindParam(":producto_fecha_ingreso", $fechaActual);
    $sentencia_edit->bindParam(":producto_stock_total", $producto_stock_total);
    $sentencia_edit->bindParam(":producto_precio_compra", $producto_precio_compra);
    $sentencia_edit->bindParam(":producto_precio_venta", $producto_precio_venta);
    $resultado_edit = $sentencia_edit->execute();

    if ($resultado_edit) {
        echo '<script>
        Swal.fire({
            title: "¡Se Añadio Stock Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$lista_productos_link.'";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Añadir Stock",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>
<br>

          <!-- left column -->
          <div class="">
            <!-- general form elements -->
            <div class="card card-purple" style="margin-top:7%">
              <div class="card-header">
                <h3 class="card-title textTabla">AÑADIR STOCK AL PRODUCTO</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form action="" method="POST">
                <div class="card-body ">
                    <div class="row" style="justify-content:center">
                        <input type="hidden" class="form-control" name="txtID" value="<?php echo $producto_id;?>" >
                        <input type="hidden" class="form-control" name="categoria_id" value="<?php echo $categoria_id;?>" >
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Código de Barra</label>
                                <input type="text" class="form-control camposTabla" readonly value="<?php echo $producto_codigo;?>">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Nombre</label>
                                <input type="text"class="form-control camposTabla" readonly value="<?php echo $producto_nombre;?>">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Marca</label>
                                <input type="text" class="form-control camposTabla" readonly value="<?php echo $producto_marca;?>" >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" readonly value="<?php echo $producto_modelo;?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Categoría</label>
                                <div class="form-group">
                                    <input type="text" class="form-control camposTabla" readonly value="<?php echo $categoria_actual; ?>" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Stock o Existencias</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="number" class="form-control camposTabla_stock" name="producto_stock_total" id="producto_stock_total" required >
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="texto" class="form-control camposTabla_dinero" placeholder="$000.00" value="<?php echo $producto_precio_compra; ?>" id="precio_compra_stock" name="producto_precio_compra">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Precio de Venta</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="texto" class="form-control camposTabla_dinero "placeholder="$000.00" value="<?php echo $producto_precio_venta; ?>" id="precio_venta_stock" name="producto_precio_venta">                                 
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-success btn-lg"> Añadir </button>
                    <a class="btn btn-danger btn-lg" href="<?php echo $url_base;?>secciones/<?php echo $lista_productos_link;?>" role="button">Cancelar</a>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <?php include("../templates/footer.php") ?>