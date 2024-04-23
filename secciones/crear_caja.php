<?php include("../templates/header.php") ?>
<?php 

if ($_SESSION['valSudoAdmin']) {
    $index_cajas_link  = "index_cajas.php";
  
 }else{
    $index_cajas_link  = "index_cajas.php?link=".$link;
 }

 if(isset($_GET['link'])){
    $link=(isset($_GET['link']))?$_GET['link']:"";
 }
if ($_POST) {
    $caja_numero = isset($_POST['caja_numero']) ? $_POST['caja_numero'] : "";
    $caja_nombre = isset($_POST['caja_nombre']) ? $_POST['caja_nombre'] : "";
    $caja_efectivo = isset($_POST['caja_efectivo']) ? $_POST['caja_efectivo'] : "";  
    $responsable = $_SESSION['usuario_id'];
    $link =  isset($_POST['link']) ? $_POST['link'] : "";
    if ($responsable == 1) {
        $link = "sudo_admin";
    }
    // Eliminar el signo "$" y el separador de miles "." del valor del campo de entrada
    $caja_efectivo = str_replace(array('$','.',','), '', $caja_efectivo); 

    $sentencia = $conexion->prepare("INSERT INTO caja(caja_numero, caja_nombre, caja_efectivo, link, responsable) 
        VALUES (:caja_numero, :caja_nombre,:caja_efectivo, :link, :responsable)");
    
    $sentencia->bindParam(":caja_numero", $caja_numero);
    $sentencia->bindParam(":caja_nombre", $caja_nombre);
    $sentencia->bindParam(":caja_efectivo", $caja_efectivo);
    $sentencia->bindParam(":link", $link);
    $sentencia->bindParam(":responsable",$responsable);
    $resultado = $sentencia->execute();

    $sql = "UPDATE dtpmp SET efectivo = ?";
    $sentencia_dinero = $conexion->prepare($sql);
    $params = array($caja_efectivo);
    $sentencia_dinero->execute($params);
    
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "Caja Creada Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$index_cajas_link.'";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear nueva Caja",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>
    <br>
    <div class="card card-primary" style="margin-top:7%">
        <div class="card-header">
            <h3 class="card-title textTabla">REGISTRE UNA NUEVA CAJA &nbsp;&nbsp;<a class="btn btn-warning"  style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $index_cajas_link;?>" role="button">Lista de Caja</a></h3>
        </div>
        <!-- /.card-header -->
        <!-- form start --> 
        <form action="" method="POST">
            <input type="hidden" name="link" value="<?php echo $link ?>">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Código</label> 
                            <input type="text" class="form-control camposTabla" name="caja_numero" required>
                        </div>                               
                        </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Nombre</label> 
                            <input type="text" class="form-control camposTabla" name="caja_nombre" required>
                        </div>                                
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="cajaEfectivo" class="textLabel">Efectivo</label> 
                            <input type="text" class="form-control camposTabla_dinero" id="cajaEfectivo" name="caja_efectivo" required>
                        </div>                                
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer" style="text-align:center">
                <button type="submit"  class="btn btn-primary btn-lg">Guardar</button>
                <a role="button" href="<?php echo $url_base;?>secciones/<?php echo $index_cajas_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
<?php include("../templates/footer.php") ?>