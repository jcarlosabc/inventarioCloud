<?php include("../templates/header.php") ?>
<?php 
if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("SELECT * FROM caja WHERE caja_id=:caja_id");
    $sentencia->bindParam(":caja_id",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    
    $caja_id=$registro["caja_id"];
    $caja_numero=$registro["caja_numero"];
    $caja_nombre=$registro["caja_nombre"];
    $caja_efectivo=$registro["caja_efectivo"];
}

if ($_POST) {
    $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
    $caja_numero= isset($_POST['caja_numero']) ? $_POST['caja_numero'] : "";
    $caja_nombre= isset($_POST['caja_nombre']) ? $_POST['caja_nombre'] : "";
    $caja_efectivo= isset($_POST['caja_efectivo']) ? $_POST['caja_efectivo'] : "";

    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $caja_efectivo = str_replace(array('$','.', ','), '', $caja_efectivo);
     
    $sentencia_edit = $conexion->prepare("UPDATE caja SET caja_numero=:caja_numero, caja_nombre=:caja_nombre, caja_efectivo=:caja_efectivo 
        WHERE caja_id =:caja_id");   

    $sentencia_edit->bindParam(":caja_id", $txtID);
    $sentencia_edit->bindParam(":caja_numero", $caja_numero);
    $sentencia_edit->bindParam(":caja_nombre", $caja_nombre);
    $sentencia_edit->bindParam(":caja_efectivo", $caja_efectivo);
    $resultado_edit = $sentencia_edit->execute();

    if ($resultado_edit) {
        echo '<script>
        Swal.fire({
            title: "¡Caja Actualizada Exitosamente!",
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
            title: "Error al Actualizar la Caja",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }

}

?>
    <br>
    <div class="card card-warning" style="margin-top:7%">
        <div class="card-header">
            <h3 class="card-title textTabla">EDITAR CAJA</h3>
        </div>
        <form action="" method="POST" id="formCaja">
            <div class="card-body ">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="hidden" class="textLabel" name="txtID" id="txtID" value="<?php echo $caja_id;?>" >
                            <label class="textLabel">Codigo</label> 
                            <input type="text" class="form-control camposTabla" name="caja_numero" value="<?php echo $caja_numero;?>">
                        </div>                               
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Nombre</label> 
                            <input type="text" class="form-control camposTabla" name="caja_nombre" value="<?php echo $caja_nombre;?>">
                        </div>                                
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="cajaEfectivo_edit" class="textLabel">Efectivo</label> 
                            <input type="text" class="form-control camposTabla" name="caja_efectivo" id="cajaEfectivo_edit" value="<?php echo '$' . number_format($caja_efectivo, 0, '.', ','); ?>">
                        </div>                                                               
                    </div>
                </div>
            </div>
            <div class="card-footer" style="text-align:center">
                <button type="submit"  class="btn btn-primary btn-lg">Guardar</button>
                <a role="button" href="<?php echo $url_base;?>secciones/<?php echo $index_cajas_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
    <?php include("../templates/footer.php") ?>