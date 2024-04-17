<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
    $lista_productos_link  = "index_productos.php";
    
}else{
    $lista_productos_link  = "index_productos.php?link=".$link;
}

date_default_timezone_set('America/Bogota'); 
$fechaActual = date("d-m-Y");

// Mostrar los datos del producto a trasladar
if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $link=(isset($_GET['link']))?$_GET['link']:"";   


    $sentencia=$conexion->prepare("SELECT * FROM producto WHERE producto_id=:id AND link = :link");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->bindParam(":link",$link);
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
    $proveedor_id=$registro["proveedor_id"];   
    $categoria_id=$registro["categoria_id"];
    $producto_fecha_garantia=$registro["producto_fecha_garantia"];
}

// Obtener la categoría actual del producto
$sentencia_categoria = $conexion->prepare("SELECT c.categoria_nombre FROM producto p JOIN categoria c ON p.categoria_id = c.categoria_id WHERE p.producto_id = :id");
$sentencia_categoria->bindParam(":id", $producto_id);
$sentencia_categoria->execute();
$registro_categoria = $sentencia_categoria->fetch(PDO::FETCH_LAZY);
$categoria_actual =  isset($registro_categoria["categoria_nombre"]) ? $registro_categoria["categoria_nombre"] : "";

// Obtener proveedor actual del producto
$sentencia_proveedor = $conexion->prepare("SELECT p.nombre_proveedores FROM producto pr JOIN proveedores p ON pr.proveedor_id = p.id_proveedores WHERE pr.producto_id=:id");
$sentencia_proveedor->bindParam(":id", $producto_id);
$sentencia_proveedor->execute();
$registro_proveedor = $sentencia_proveedor->fetch(PDO::FETCH_LAZY);
$proveedor_actual =  isset($registro_proveedor["nombre_proveedores"]) ? $registro_proveedor["nombre_proveedores"] : "";

//Lista de Locales    
$sentencia_empresas = $conexion->prepare("SELECT empresa_id, empresa_nombre, link FROM empresa WHERE link != ?");
$sentencia_empresas->execute([$link]);
$lista_empresas = $sentencia_empresas->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {   
    $producto_fecha_garantia = (isset($_POST['producto_fecha_garantia'])) ? $_POST['producto_fecha_garantia'] : "";
    $producto_nombre = (isset($_POST['producto_nombre'])) ? $_POST['producto_nombre'] : "";
    $producto_stock_total = (isset($_POST['producto_stock_total'])) ? $_POST['producto_stock_total'] : "";
    $producto_precio_compra = (isset($_POST['producto_precio_compra'])) ? $_POST['producto_precio_compra'] : "";
    $producto_precio_venta = (isset($_POST['producto_precio_venta'])) ? $_POST['producto_precio_venta'] : "";
    $producto_marca = (isset($_POST['producto_marca'])) ? $_POST['producto_marca'] : "";
    $producto_modelo = (isset($_POST['producto_modelo'])) ? $_POST['producto_modelo'] : "";

    $cantidad_enviada = (isset($_POST['productoSend'])) ? $_POST['productoSend'] : "";
    $producto_codigo = isset($_POST['producto_codigo']) ? $_POST['producto_codigo'] : "";
    $valor_seleccionado = $_POST['empresa_destino'];
    // Separar los dos valores
    $partes = explode('-', $valor_seleccionado);
    $empresa_id = $partes[0];
    $link_empresa = $partes[1];

    $lista_producto_buscado = $conexion->prepare("SELECT link FROM producto WHERE producto_codigo = :producto_codigo AND link=:link_empresa");
    $lista_producto_buscado->bindParam(":producto_codigo", $producto_codigo);
    $lista_producto_buscado->bindParam(":link_empresa", $link_empresa);
    $lista_producto_buscado->execute();
    $lista_producto_buscado = $lista_producto_buscado->fetch(PDO::FETCH_LAZY);

    if ($lista_producto_buscado) {
        echo " SI hay un producto con ese codigo... ";
        echo "<br>";
        $traslado= "tl";
        // Actualizando la cantidad en el stock del LOCAL que le pasamos de bodega
        $sql = "UPDATE producto SET producto_fecha_ingreso = ?, producto_stock_total = producto_stock_total + ? , traslado = ? WHERE producto_codigo = ? AND link= ?";
        $sentencia_envio = $conexion->prepare($sql);
        $params = array($fechaActual,$cantidad_enviada,$traslado,$producto_codigo, $link_empresa);
        $resultado = $sentencia_envio->execute($params);
        echo "...| Realizando actualizacion al estock del local |...";
        echo "<br>";

        echo "...| Actualizando nueva cantidad en el stock de local enviante |...";
        // Actualizando nueva cantidad en el stock de BODEGA
        $sql = "UPDATE producto SET producto_stock_total = producto_stock_total - ? WHERE producto_codigo = ? AND link = ?" ; 
        $sentencia_bodega = $conexion->prepare($sql);
        $params = array($cantidad_enviada, $producto_codigo,$link);
        $resultado_bodega = $sentencia_bodega->execute($params);
        
    }else{
        echo "No hay producto con ese codigo...aqui va insert";
        echo "<br>";
        echo "INSERTANDO NUEVO PRODUCTO";
     $sql = "INSERT INTO producto (producto_codigo, producto_fecha_creacion, producto_fecha_garantia,producto_nombre, producto_stock_total,
        producto_precio_compra,producto_precio_venta,producto_marca,producto_modelo, categoria_id,proveedor_id, link, traslado )         
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $sentencia = $conexion->prepare($sql);
    $params = array(
        $producto_codigo, 
        $fechaActual, 
        $producto_fecha_garantia, 
        $producto_nombre,
        $cantidad_enviada, 
        $producto_precio_compra,
        $producto_precio_venta, 
        $producto_marca, 
        $producto_modelo,
        0,
        0,
        $link_empresa,
        $traslado
    );
    $resultado = $sentencia->execute($params);

    echo "...| Actualizando nueva cantidad en el stock de local enviante |...";
    // Actualizando nueva cantidad en el stock de BODEGA
    $sql = "UPDATE producto SET producto_stock_total = producto_stock_total - ? WHERE producto_codigo = ? AND link = ?" ; 
    $sentencia_bodega = $conexion->prepare($sql);
    $params = array($cantidad_enviada, $producto_codigo,$link);
    $resultado_bodega = $sentencia_bodega->execute($params);

    }

    if ($resultado_bodega) {
        echo '<script>
        Swal.fire({
            title: "¡Se envio el producto Correctamente!",
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
            title: "Error al enviar producto",
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
                <h3 class="card-title textTabla">ENVIO DE PRODUCTOS</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form action="" method="POST">
                <div class="card-body ">
                    <div class="row" style="justify-content:center">
                        <input type="hidden" name="txtID" value="<?php echo $producto_id;?>" >
                        <input type="hidden" name="categoria_id" value="<?php echo $categoria_id;?>" >
                        <input type="hidden" name="producto_fecha_garantia" value="<?php echo $producto_fecha_garantia;?>" >
                        <input type="hidden" name="producto_marca" value="<?php echo $producto_marca;?>" >
                        <input type="hidden" name="producto_modelo" value="<?php echo $producto_modelo;?>" >
                        
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Código de Barra</label>
                                <input type="text" class="form-control camposTabla" name="producto_codigo" readonly value="<?php echo $producto_codigo;?>">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Nombre</label>
                                <input type="text"class="form-control camposTabla" name="producto_nombre" readonly value="<?php echo $producto_nombre;?>">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Marca</label>
                                <input type="text" class="form-control camposTabla" name="producto_marca" readonly value="<?php echo $producto_marca;?>" >
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_modelo" readonly value="<?php echo $producto_modelo;?>">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Categoría</label>
                                <div class="form-group">
                                    <input type="text" class="form-control camposTabla" name="categoria_actual" readonly value="<?php echo $categoria_actual; ?>" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Proveedor</label>
                                <div class="form-group">
                                    <input type="text" class="form-control camposTabla" name="proveedor_actual" readonly value="<?php echo $proveedor_actual; ?>" >
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Stock</label>
                                <div class="form-group">
                                    <input type="text" class="form-control camposTabla" name="producto_stock_total" readonly value="<?php echo $producto_stock_total; ?>" >
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="texto" class="form-control camposTabla_dinero" readonly value="<?php echo $producto_precio_compra; ?>" id="precio_compra_stock" name="producto_precio_compra">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Precio de Venta</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="texto" class="form-control camposTabla_dinero " readonly value="<?php echo $producto_precio_venta; ?>" id="precio_venta_stock" name="producto_precio_venta">                                 
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-2">
                        <div class="form-group">
                            <label class="textLabel">Enviar a:</label> &nbsp;<i class="nav-icon fas fa-share"></i> 
                            <div class="form-group camposTabla">
                                <select class="form-control select2 camposTabla"name="empresa_destino">                                    
                                    <option value="">Escoger Local</option> 
                                    <?php foreach ($lista_empresas as $registro) {?>   
                                        <option value="<?php echo $registro['empresa_id'] . '-' . $registro['link']?>"><?php echo $registro['empresa_nombre']; ?></option> 
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-2">    
                        <div class="form-group">
                            <label class="textLabel">Cantidad a Enviar</label>
                            <div class="form-group">
                                <input type="text" class="form-control camposTabla" required name="productoSend" >
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-success btn-lg"> Enviar </button>
                    <a class="btn btn-danger btn-lg" href="<?php echo $url_base;?>secciones/<?php echo $lista_productos_link;?>" role="button">Cancelar</a>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>
          <style>
            span.select2-selection.select2-selection--single{
            height: 38px;
            }
          </style>
          <?php include("../templates/footer.php") ?>