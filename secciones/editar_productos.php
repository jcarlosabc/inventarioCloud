<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
    $lista_productos_link  = "index_productos.php";
    
}else{
    $lista_productos_link  = "index_productos.php?link=".$link;
}
$responsable = $_SESSION['usuario_id'];

if(isset($_GET['link']) || $responsable == 1){
    if ($responsable == 1) {
       $txtID_productos=(isset($_GET['txtID']))?$_GET['txtID']:"";
       $sentencia=$conexion->prepare("SELECT * FROM producto WHERE producto_id=:producto_id");
       $sentencia->bindParam(":producto_id",$txtID_productos);
    }else {
        $link=(isset($_GET['link']))?$_GET['link']:"";
        $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
        $sentencia=$conexion->prepare("SELECT * FROM producto WHERE link=:link AND producto_id = :txtID");
        $sentencia->bindParam(":txtID",$txtID);
        $sentencia->bindParam(":link",$link);

    }
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $producto_id=$registro["producto_id"];
    $producto_codigo=$registro["producto_codigo"];
    $producto_nombre=$registro["producto_nombre"];
    $producto_precio_compra=$registro["producto_precio_compra"];  
    $producto_precio_venta=$registro["producto_precio_venta"]; 
    $producto_precio_venta_xmayor=$registro["producto_precio_venta_xmayor"];
    $producto_stock_total=$registro["producto_stock_total"];  
    $producto_marca=$registro["producto_marca"];  
    $producto_modelo=$registro["producto_modelo"];  
    $categoria_id=$registro["categoria_id"];  
    $producto_fecha_garantia=$registro["producto_fecha_garantia"];  
    $link=$registro["link"];  
}
        // Obtener la categoría actual del producto
        $sentencia_categoria = $conexion->prepare("SELECT p.categoria_id, c.categoria_nombre FROM producto p
            JOIN categoria c ON p.categoria_id = c.categoria_id
            WHERE p.producto_id=:producto_id");
        $sentencia_categoria->bindParam(":producto_id", $producto_id);
        
        $sentencia_categoria->execute();
        $categoria_actual = $sentencia_categoria->fetch(PDO::FETCH_ASSOC);


if(isset($_GET['link']) || $responsable == 1){
    if ($responsable == 1) {

        // Obtener todas las categorías disponibles
        if(isset($_GET['data-value'])){ $linkeo=(isset($_GET['data-value']))?$_GET['data-value']:"";}
        $sentencia_todas = $conexion->prepare("SELECT categoria_id, categoria_nombre FROM categoria WHERE link=:linkeo");
        $sentencia_todas->bindParam(":linkeo", $linkeo);
    }else {
        // Obtener todas las categorías disponibles
        if(isset($_GET['link'])){ $linkeo=(isset($_GET['link']))?$_GET['link']:"";}
        $sentencia_todas = $conexion->prepare("SELECT categoria_id, categoria_nombre FROM categoria WHERE link=:linkeo");
        $sentencia_todas->bindParam(":linkeo", $linkeo);
    }
    $sentencia_todas->execute();
    $categorias_disponibles = $sentencia_todas->fetchAll(PDO::FETCH_ASSOC);
}

    
   // Consulta para obtener el proveedor actual del producto
    $sentencia_proveedor = $conexion->prepare("SELECT p.proveedor_id, pro.nombre_proveedores
    FROM producto p
    JOIN proveedores pro ON p.proveedor_id = pro.id_proveedores
    WHERE p.producto_id = :producto_id");
    $sentencia_proveedor->bindParam(":producto_id", $producto_id);
    $sentencia_proveedor->execute();
    $proveedor_actual = $sentencia_proveedor->fetch(PDO::FETCH_ASSOC);

    // Consulta para obtener todos los proveedores disponibles
    $sentencia_todas = $conexion->prepare("SELECT id_proveedores, nombre_proveedores FROM proveedores WHERE link=:linkeo");
    $sentencia_todas->bindParam(":linkeo", $linkeo);
    $sentencia_todas->execute();
    $proveedores_disponibles = $sentencia_todas->fetchAll(PDO::FETCH_ASSOC);



if ($_POST) {
    $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
    $codigo_barra= isset($_POST['producto_codigo']) ? $_POST['producto_codigo'] : "";
    $producto_nombre= isset($_POST['producto_nombre']) ? $_POST['producto_nombre'] : "";
    $producto_stock_total= isset($_POST['producto_stock_total']) ? $_POST['producto_stock_total'] : "";
    $producto_precio_compra= isset($_POST['producto_precio_compra']) ? $_POST['producto_precio_compra'] : "";
    $producto_precio_venta= isset($_POST['producto_precio_venta']) ? $_POST['producto_precio_venta'] : "";
        $producto_precio_venta_xmayor= isset($_POST['producto_precio_venta_xmayor']) ? $_POST['producto_precio_venta_xmayor'] : "";
    $producto_marca= isset($_POST['producto_marca']) ? $_POST['producto_marca'] : "";
    $producto_modelo= isset($_POST['producto_modelo']) ? $_POST['producto_modelo'] : "";
    $categoria_id= isset($_POST['categoria_id']) ? $_POST['categoria_id'] : "";
    $nueva_garantia =  isset($_POST['nueva_garantia']) ? $_POST['nueva_garantia'] : $_POST['producto_fecha_garantiaDB'];
    // echo "nueva_garantia = > " . $nueva_garantia;
    if (!$nueva_garantia) {
        $nueva_garantia = $_POST['producto_fecha_garantiaDB'];
    }
    $proveedor_id= isset($_POST['proveedor_id']) ? $_POST['proveedor_id'] : "";

    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $producto_precio_compra = str_replace(array('$','.', ','), '', $producto_precio_compra);
    $producto_precio_venta = str_replace(array('$','.', ','), '', $producto_precio_venta);
       $producto_precio_venta_xmayor = str_replace(array('$','.', ','), '', $producto_precio_venta_xmayor);
    
    $sentencia_edit = $conexion->prepare("UPDATE producto SET 
    producto_codigo=:producto_codigo,
    producto_fecha_garantia=:producto_fecha_garantia,
    producto_fecha_editado=:producto_fecha_editado,
    producto_nombre=:producto_nombre,
    producto_stock_total=:producto_stock_total,
    producto_precio_compra=:producto_precio_compra,
    producto_precio_venta=:producto_precio_venta,
    producto_precio_venta_xmayor=:producto_precio_venta_xmayor,
    producto_marca=:producto_marca,
    producto_modelo=:producto_modelo,
    categoria_id=:categoria_id,
    proveedor_id=:proveedor_id
    WHERE producto_id =:producto_id");

    $sentencia_edit->bindParam(":producto_id", $txtID);
    $sentencia_edit->bindParam(":producto_codigo", $codigo_barra);
    $sentencia_edit->bindParam(":producto_fecha_garantia", $nueva_garantia);
    $sentencia_edit->bindParam(":producto_fecha_editado", $fechaActual);
    $sentencia_edit->bindParam(":producto_nombre", $producto_nombre);
    $sentencia_edit->bindParam(":producto_stock_total", $producto_stock_total);
    $sentencia_edit->bindParam(":producto_precio_compra", $producto_precio_compra);
    $sentencia_edit->bindParam(":producto_precio_venta", $producto_precio_venta);
       $sentencia_edit->bindParam(":producto_precio_venta_xmayor", $producto_precio_venta_xmayor);
    $sentencia_edit->bindParam(":producto_marca", $producto_marca);
    $sentencia_edit->bindParam(":producto_modelo", $producto_modelo);
    $sentencia_edit->bindParam(":categoria_id", $categoria_id);
    $sentencia_edit->bindParam(":proveedor_id", $proveedor_id);
    $resultado_edit = $sentencia_edit->execute();

    if ($resultado_edit) {
        echo '<script>
        Swal.fire({
            title: "¡Producto Actualizado Correctamente!",
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
            title: "Error al Actualizar el Producto",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}


?>
        <br>
            <!-- general form elements -->
            <div class="card card-warning" style="margin-top:7%">
              <div class="card-header">
                <h3 class="card-title textTabla">EDITE EL PRODUCTO</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
            <form action="" method="POST">
                <br>
                <div class="card-body ">
                    <div class="row" style="justify-content:center">       
                        <input type="hidden" class="form-control" name="txtID" value="<?php echo $producto_id;?>" >
                        <input type="hidden" class="form-control" name="categoria_id" value="<?php echo $categoria_id;?>" >
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="" class="textLabel">Código de Barra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_codigo" required value="<?php echo $producto_codigo;?>">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="" class="textLabel">Nombre del Producto</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text"class="form-control camposTabla" name="producto_nombre" value="<?php echo $producto_nombre;?>">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">     
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Marca</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_marca" value="<?php echo $producto_marca;?>" >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="producto_modelo" value="<?php echo $producto_modelo;?>">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">       
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="" class="textLabel">Categoría</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
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
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="" class="textLabel">Proveedor</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <div class="form-group">
                                <select class="form-control select2 camposTabla" name="proveedor_id" style="width: 100%">
                                    <?php foreach ($proveedores_disponibles as $proveedor) {
                                        $selected = ($proveedor["id_proveedores"] == $proveedor_actual["proveedor_id"]) ? "selected" : "";
                                        echo '<option value="' . $proveedor["id_proveedores"] . '" ' . $selected . '>' . $proveedor["nombre_proveedores"] . '</option>';
                                    } ?>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">     
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Actual Garantía</label>
                                <input type="text" name="producto_fecha_garantiaDB" value="<?php echo $producto_fecha_garantia;?>" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Nueva fecha de Garantía</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="nueva_garantia" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="justify-content:center">       
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Stock o Existencias</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="number" class="form-control camposTabla_stock" name="producto_stock_total"
                                    value="<?php echo $producto_stock_total;?>"  pattern="[0-9]*">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="producto_precio_compra_edit" class="textLabel">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla_dinero" placeholder="$000.00" name="producto_precio_compra" id="producto_precio_compra_edit"
                                value="<?php echo '$' . number_format($producto_precio_compra, 0, '.', ','); ?>">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="producto_precio_venta_edit" class="textLabel">Precio de Venta al Detal</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="texto" class="form-control camposTabla_dinero"placeholder="$000.00" name="producto_precio_venta" id="producto_precio_venta_edit"
                                    value="<?php echo '$' . number_format($producto_precio_venta, 0, '.', ','); ?>">                                 
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="producto_precio_venta_edit" class="textLabel">Precio de Venta al por Mayor</label>
                                <input type="texto" class="form-control camposTabla_dinero"placeholder="$000.00" name="producto_precio_venta_xmayor" id="producto_precio_venta_xmayor_edit"
                                    value="<?php echo '$' . number_format($producto_precio_venta_xmayor, 0, '.', ','); ?>">                                 
                            </div>
                        </div>
                    </div>    
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-success btn-lg"> Actualizar </button>
                    <a class="btn btn-danger btn-lg" href="<?php echo $url_base;?>secciones/<?php echo $lista_productos_link;?>" role="button">Cancelar</a>
                </div>
            </form>
            </div>
            <br>
            <style>
            span.select2-selection.select2-selection--single{
                height: 38px;
            }
            </style>
<?php include("../templates/footer.php") ?>