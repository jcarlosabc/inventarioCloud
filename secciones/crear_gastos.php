<?php include("../templates/header.php") ?>
<?php
$responsable = $_SESSION['usuario_id'];
if(isset($_GET['link'])){
    $link=(isset($_GET['link']))?$_GET['link']:"";
 }

$sentencia = $conexion->prepare("SELECT * FROM empresa ");
$sentencia->execute();
$lista_empresas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d-m-Y");
    $hora_actual = date("h:i:s A");

    $gasto_producto = isset($_POST['gasto_producto']) ? $_POST['gasto_producto'] : "";
    $gasto_motivo = isset($_POST['gasto_motivo']) ? $_POST['gasto_motivo'] : "";
    $gasto_precio = isset($_POST['gasto_precio']) ? $_POST['gasto_precio'] : "";
    $gasto_precio = str_replace(array('$','.', ','), '', $gasto_precio);
    $usuario_empresa_gastos = isset($_POST['usuario_empresa_gastos']) ? $_POST['usuario_empresa_gastos'] : "";

    if (!$usuario_empresa_gastos) {
        $usuario_empresa_gastos = $link;
    }
    $link =  isset($_POST['link']) ? $_POST['link'] : "";
     if ($responsable == 1) {
         $link = $usuario_empresa_gastos;
     }
    $sql = "INSERT INTO gastos (gasto_producto, gasto_motivo, gasto_precio, gasto_fecha, gasto_hora, link, responsable) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($sql);
            $params = array(
            $gasto_producto, 
            $gasto_motivo, 
            $gasto_precio,
            $fechaActual,
            $hora_actual,
            $link,
            $responsable 
            );
          $resultado = $sentencia->execute($params);
          
    //Restar el Gasto en la quincena
     $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE link = :link ORDER BY id DESC");
        $sentencia->bindParam(":link", $link);
        $sentencia->execute();
        $lista_ultimo_update=$sentencia->fetch(PDO::FETCH_LAZY);
        $id = $lista_ultimo_update['id'];
        $dinero = $lista_ultimo_update['dinero'];
        $gasto_precio = $dinero - $gasto_precio;
        
        $sql = "UPDATE dinero_por_quincena SET dinero = ? WHERE id = ?";
            $sentencia = $conexion->prepare($sql);
            $params = array(
                $gasto_precio, 
                $id  
            );
        $resultado = $sentencia->execute($params);

    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Gasto creado Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$index_gastos_link.'";
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
        <h2 class="card-title textTabla">REGISTRE LOS GASTOS DEL LOCAL &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $index_gastos_link;?>" role="button">Lista de Gastos</a></h2>
    </div>
    <br>
    <form action="" method="post" id="formGastos" onsubmit="return validarFormulario(3)">
        <div class="card-body ">
            <div class="row" style="justify-content:center">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="textLabel">Producto Adquirido</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <input type="text" class="form-control camposTabla" name="gasto_producto" required>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="textLabel">Motivo de la Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <textarea name="gasto_motivo" style="height: 50px;" class="form-control camposTabla" cols="20" rows="2" required></textarea>
                    </div>
                </div>
            </div>
            <div class="row" style="justify-content:center">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="producto_precio_compra" class="textLabel">Precio</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="gasto_precio" id="gastoPrecio" required>
                    </div>
                </div>
                <?php if ($responsable == 1) { ?>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Negocio</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <div class="form-group">
                                <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_empresa_gastos">
                                    <option value="">Escoger Negocio</option>
                                    <?php foreach ($lista_empresas as $registro) { ?>
                                        <option value="<?php echo $registro['link']; ?>"><?php echo $registro['empresa_nombre']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <br>
        <!-- /.card-body -->
        <div class="card-footer" style="text-align:center">
            <input type="hidden" name="link" value="<?php echo $link ?> ">
            <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
            <a class="btn btn-danger btn-lg" href="<?php echo $url_base; ?>secciones/<?php echo $index_gastos_link; ?>" role="button">Cancelar</a>
        </div>
    </form>
</div>
<style>
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
</style>
<?php include("../templates/footer.php") ?>