<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
    $lista_gasto_link = 'crear_gastos.php';
} else {
    $lista_gasto_link = 'crear_gastos.php?link=' . $link;
}

if (isset($_GET['link'])) {
    $link = (isset($_GET['link'])) ? $_GET['link'] : "";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $detalle_gasto = isset($_POST['detalle_gasto']) ? $_POST['detalle_gasto'] : "";
    $valor_gasto = isset($_POST['valor_gasto']) ? $_POST['valor_gasto'] : "";
    $responsable = $_SESSION['usuario_id'];
    $link = isset($_POST['link']) ? $_POST['link'] : "";

    $valor_gasto_db = str_replace(array('$','.', ','), '', $valor_gasto);

    try {
        $sentencia = $conexion->prepare("INSERT INTO gastos (gasto_detalle,
        gasto_valor,
        link,
        responsable)
        VALUES (?, ?, ?, ?)");
        
        $sentencia->bindParam(1, $detalle_gasto);
        $sentencia->bindParam(2, $valor_gasto_db);
        $sentencia->bindParam(3, $link);
        $sentencia->bindParam(4, $responsable);
        
        $resultado =  $sentencia->execute();
        
        //echo "El gasto se registró correctamente.";
    } catch (PDOException $e) {
        //echo "Error al registrar el gasto: " . $e->getMessage();
    }

    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "Gasto creado Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$lista_gasto_link.'";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear Gasto",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }

}

?>


<script>
</script>
<br>
<!-- general form elements -->
<br>
<div class="card card-primary" style="margin-top:3%">
    <div class="card-header">
        <h2 class="card-title textTabla">REGISTRE LOS GASTOS DEL LOCAL </h2>
    </div>
    <br>

    <!-- form start -->
    <!-- onsubmit="return validarFormulario(1)" -->
    <form action="" method="post" id="formProducto">
        <div class="card-body ">
            <div class="row" style="justify-content:center">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="textLabel">Detalle de gasto</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <input type="text" class="form-control camposTabla" name="detalle_gasto" required>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="producto_precio_compra" class="textLabel">valor de gasto</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="valor_gasto" id="producto_precio_compra" required>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <!-- /.card-body -->
        <input type="hidden" name="link" value="<?php echo $link ?>">
        <div class="card-footer" style="text-align:center">
            <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
            <a class="btn btn-danger btn-lg" href="<?php echo $url_base; ?>secciones/<?php echo $lista_producto_link; ?>" role="button">Cancelar</a>
        </div>
    </form>
</div>
<?php include("../templates/footer.php") ?>