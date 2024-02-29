<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM carrito WHERE id=:id");
  
  $sentencia->bindParam(":id",$txtID);
  $sentencia->execute();
}
// Mostrar datos de la tabla 
$sentencia=$conexion->prepare("SELECT * FROM `producto`");
$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia=$conexion->prepare("SELECT * FROM `categoria`");
$sentencia->execute();
$lista_categoria=$sentencia->fetchAll(PDO::FETCH_ASSOC);
// ===========================================================

// Guardar productos escogidos al carrito
if(isset($_POST['producto_seleccionado'])) {
    $producto_seleccionado_encoded = $_POST['producto_seleccionado'];
    $producto_seleccionado = unserialize(base64_decode($producto_seleccionado_encoded));

    $producto_id = $producto_seleccionado['producto_id'];
    $producto_codigo = $producto_seleccionado['producto_codigo'];
    $producto_nombre = $producto_seleccionado['producto_nombre'];
    $producto_precio_venta = $producto_seleccionado['producto_precio_venta'];
    $producto_marca = $producto_seleccionado['producto_marca'];
    $producto_modelo = $producto_seleccionado['producto_modelo']; 
       
    $sentencia = $conexion->prepare("INSERT INTO carrito(
        id, producto_codigo,producto_id,
        producto, precio, marca, modelo ) 
        VALUES (NULL,:producto_codigo,:producto_id,:producto_nombre,:producto_precio_venta,:producto_marca,:producto_modelo)");

    $sentencia->bindParam(":producto_id", $producto_id);
    $sentencia->bindParam(":producto_codigo", $producto_codigo);
    $sentencia->bindParam(":producto_nombre", $producto_nombre);
    $sentencia->bindParam(":producto_precio_venta", $producto_precio_venta);
    $sentencia->bindParam(":producto_marca", $producto_marca);
    $sentencia->bindParam(":producto_modelo", $producto_modelo);

    $sentencia->execute();

    // Mostrar el carrito
    $sentencia=$conexion->prepare("SELECT id, producto_codigo, cantidad, producto_id, producto, precio, marca, modelo FROM carrito WHERE estado=0");
    $sentencia->execute();
    $lista_carrito=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    $total = 0; 

    foreach ($lista_carrito as $item) {
        $precio = $item['precio'];
        // Sumar al total
        $total += $precio;
    }
}

if ($_POST) {
    $cantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : array();

    foreach ($cantidades as $id => $cantidad) {
        echo "id: $id, cantidad: $cantidad<br>";

        $sentencia_edit = $conexion->prepare("UPDATE carrito SET cantidad= :cantidad WHERE id=:id");

        $sentencia_edit->bindParam("cantidad", $cantidad);
        $sentencia_edit->bindParam("id", $id);
        
        $sentencia_edit->execute();
        
    }
}

?>
    <br>
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title textTabla" >REALIZAR VENTA</h3>
            </div>
                <div class="card-body ">
                    <div class="card card-info">
                        <div class="card-body">
                            <table id="vBuscar" class="table table-bordered table-striped" style="text-align:center">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Existencias</th>
                                        <th>Precio</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Escoger</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <form action="crear.php" method="POST">
                                        <?php error_reporting(E_ERROR | E_PARSE); foreach ($lista_producto as $registro) {?>
                                            <tr class="">
                                                <input type="hidden" value="<?php echo $registro['producto_id']; ?>">
                                                <input type="hidden" value="<?php echo $registro['producto_codigo']; ?>">
                                                <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                                                <td><?php echo $registro['producto_nombre']; ?></td>
                                                <td><?php echo $registro['producto_stock_total']; ?></td>
                                                <td><?php echo $registro['producto_precio_venta']; ?></td>
                                                <td><?php echo $registro['producto_marca']; ?></td>
                                                <td><?php echo $registro['producto_modelo']; ?></td>
                                                <td>
                                                    <button type="submit" name="producto_seleccionado" value="<?php echo base64_encode(serialize($registro)); ?>" class="btn btn-primary">Escoger <i class="fas fa-chevron-right"></i></button>
                                                </td>
                                            </tr>  
                                        <?php } ?>
                                    </form>
                                </tbody>                  
                            </table>
                        </div>
                    </div>
                    <br>
                    <!-- formulario -->
                    <form method="post" action="">
                        <div class="row">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">PRODUCTOS A COMPRAR</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped" style="text-align:center">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Marca</th>
                                                <th>Modelo</th>
                                                <th>Cantidad</th>
                                                <th>X</th>
                                                <th>Precio</th>
                                                <th>=</th>
                                                <th>Total</th>
                                                <th>Remover</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($lista_carrito as $registro) {?>
                                                <tr class="">
                                                    <input type="hidden" value="<?php echo $registro['id']; ?>">
                                                    <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                                                    <td><?php echo $registro['producto']; ?></td>
                                                    <td><?php echo $registro['marca']; ?></td>
                                                    <td><?php echo $registro['modelo']; ?></td>
                                                    <td><input type="number" name="cantidad[<?php echo $registro['id']; ?>]" value="<?php echo $registro['cantidad']; ?>"></td>
                                                    <td>X</td>
                                                    <td><?php echo $registro['precio']; ?></td>
                                                    <td>=</td>
                                                    <td>TOTAL</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a class="btn btn-danger" href="crear.php?txtID=<?php echo $registro['id']; ?>" role="button"><i class="far fa-trash-alt"></i></a>                    
                                                        </div>
                                                    </td>
                                                </tr>  
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <br>    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <input type="text" class="form-control camposTabla_dinero" value="<?php echo $total ?>" >
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control camposTabla_dinero" >
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control camposTabla_dinero" >
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                    <!-- <div class="card-footer" style="text-align:center">
                    </div> -->
                </div>
            </div>

<?php include("../../templates/footer_content.php") ?>