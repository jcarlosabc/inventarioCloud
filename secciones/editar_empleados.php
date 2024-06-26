<?php include("../templates/header.php") ?>
<?php

if ($_SESSION['valSudoAdmin']) {
    $lista_empleados_link  = "index_empleados.php";
  
 }else{
    $lista_empleados_link  = "index_empleados.php?link=".$link;
 }
if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    // Obtener la categoría actual del producto
    $datos_usuario = $conexion->prepare("SELECT * FROM usuario WHERE usuario.usuario_id =:usuario_id");

    $datos_usuario->bindParam(":usuario_id", $txtID);
    $datos_usuario->execute();
    $registro = $datos_usuario->fetch(PDO::FETCH_ASSOC);

    $usuario_nombre = $registro["usuario_nombre"];
    $usuario_apellido = $registro["usuario_apellido"];
    $usuario_telefono = $registro["usuario_telefono"];
    $usuario_email = $registro["usuario_email"];
    $usuario_clave = $registro["usuario_clave"];
    $usuario_tipo = $registro["rol"];
    $quincena_empleado = $registro["quincena_empleado"];
    $usuario_caja_actual_id = isset($registro["caja_id"])?$registro["caja_id"]:0;

    if ($_POST) {
        $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
        $usuario_nombre = isset($_POST['usuario_nombre']) ? $_POST['usuario_nombre'] : "";
        $usuario_apellido = isset($_POST['usuario_apellido']) ? $_POST['usuario_apellido'] : "";
        $usuario_telefono = isset($_POST['usuario_telefono']) ? $_POST['usuario_telefono'] : "";
        $usuario_email = isset($_POST['usuario_email']) ? $_POST['usuario_email'] : "";
        if ($_POST['usuario_clave_1']) {$usuario_clave = hash('sha256', $_POST['usuario_clave_1']);}else{$usuario_clave =  $_POST['usuario_clave_db'];}
        // $usuario_rol = isset($_POST["usuario_rol"]) ? $_POST["usuario_rol"] : "";
        // $usuario_caja = isset($_POST["usuario_caja"]) ? $_POST["usuario_caja"] : "";
        $username = "u" . $usuario_apellido;
        $responsable = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id']  : 0;
        $quincena_empleado = isset($_POST['quincena_empleado']) ? $_POST['quincena_empleado'] : "";
        $quincena_empleado = str_replace(array('$','.', ','), '', $quincena_empleado);

        if ($_SESSION['valSudoAdmin']) {
            if ($txtID == 1) {
                $admin = "admin";
            }else {
                $admin = $username;
            }
            $sentencia_edit = $conexion->prepare("UPDATE usuario SET 
            usuario_nombre=:usuario_nombre, usuario_apellido=:usuario_apellido,usuario_telefono=:usuario_telefono,
            usuario_email=:usuario_email, usuario_usuario =:usuario_usuario,
            usuario_clave=:usuario_clave, quincena_empleado=:quincena_empleado, responsable = :responsable
            WHERE usuario_id =:usuario_id");
    
            $sentencia_edit->bindParam(":usuario_id", $txtID);
            $sentencia_edit->bindParam(":usuario_nombre", $usuario_nombre);
            $sentencia_edit->bindParam(":usuario_apellido", $usuario_apellido);
            $sentencia_edit->bindParam(":usuario_telefono", $usuario_telefono);
            $sentencia_edit->bindParam(":usuario_email", $usuario_email);
            $sentencia_edit->bindParam(":usuario_usuario", $admin);
            $sentencia_edit->bindParam(":usuario_clave", $usuario_clave);
            $sentencia_edit->bindParam(":quincena_empleado", $quincena_empleado);
            // $sentencia_edit->bindParam(":rol", $usuario_rol);
            // $sentencia_edit->bindParam(":caja_id", $usuario_caja);
            $sentencia_edit->bindParam(":responsable", $responsable);
           
        }else{
            $sentencia_edit = $conexion->prepare("UPDATE usuario SET 
            usuario_nombre=:usuario_nombre, usuario_apellido=:usuario_apellido,usuario_telefono=:usuario_telefono,
            usuario_email=:usuario_email, usuario_usuario =:usuario_usuario,
            usuario_clave=:usuario_clave, quincena_empleado=:quincena_empleado, responsable = :responsable
            WHERE usuario_id =:usuario_id");
    
            $sentencia_edit->bindParam(":usuario_id", $txtID);
            $sentencia_edit->bindParam(":usuario_nombre", $usuario_nombre);
            $sentencia_edit->bindParam(":usuario_apellido", $usuario_apellido);
            $sentencia_edit->bindParam(":usuario_telefono", $usuario_telefono);
            $sentencia_edit->bindParam(":usuario_email", $usuario_email);
            $sentencia_edit->bindParam(":usuario_usuario", $username);
            $sentencia_edit->bindParam(":usuario_clave", $usuario_clave);
            $sentencia_edit->bindParam(":quincena_empleado", $quincena_empleado);
            // $sentencia_edit->bindParam(":rol", $usuario_rol);
            // $sentencia_edit->bindParam(":caja_id", $usuario_caja);
            $sentencia_edit->bindParam(":responsable", $responsable);

        }
        $resultado_edit = $sentencia_edit->execute();

        if ($resultado_edit) {
            echo '<script>
            Swal.fire({
                title: "Usuario Actualizado Correctamente!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = "'.$url_base.'secciones/'.$lista_empleados_link.'";
                }
            })
        </script>';
        } else {
            echo '<script>
            Swal.fire({
                title: "Error al Actualizar el Usuario",
                icon: "error",
                confirmButtonText: "¡Entendido!"
            });
            </script>';
            }
    }
    // Obtener todas las cajas disponibles
    $sentencia_cajas = $conexion->prepare("SELECT * FROM caja");
    $sentencia_cajas->execute();
    $lista_cajas = $sentencia_cajas->fetchAll(PDO::FETCH_ASSOC);
}
?>
    <br>
    <div class="card card-warning" style="margin-top:7%">
        <div class="card-header text-center">
        <?php if ($_SESSION['valSudoAdmin']) { ?>
            <h2 class="card-title textTabla">EDITAR DATOS &nbsp;</h2>
            <?php } else{ ?>
            <h2 class="card-title textTabla">EDITAR EMPLEADOS &nbsp;</h2>            
            <?php }?>
           
        </div>
        <form action=" " method="post">
            <div class="card-body">
            <div class="row" style="justify-content:center">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="producto_nombre" class="textLabel">Nombres</label>
                        <input type="hidden" name="txtID" value="<?= $txtID ?>">
                        <input type="text" class="form-control camposTabla" name="usuario_nombre" value="<?= $usuario_nombre ?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="textLabel">Apellidos</label>
                        <input type="text" class="form-control camposTabla" name="usuario_apellido" value="<?= $usuario_apellido ?>">
                    </div>
                </div>
            </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Teléfono</label>
                            <input type="text" class="form-control camposTabla" name="usuario_telefono" value="<?= $usuario_telefono ?>">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Email</label>
                            <input type="email" class="form-control camposTabla" name="usuario_email" value="<?= $usuario_email ?>">
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-2">
                        <div class="form-group">
                            <label class="textLabel">Nómina</label>
                            <input type="text" class="form-control camposTabla_dinero" id="quincenaEmpleado" name="quincena_empleado" value="<?php echo '$ ' . number_format($quincena_empleado, 0, '.', ','); ?>">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="hidden" class="form-control camposTabla" name="usuario_clave_db" value="<?php echo $usuario_clave ?>" >
                            <input type="password" class="form-control camposTabla" name="usuario_clave_1" id="usuario_clave_1E" >
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Confime la Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla" name="usuario_clave_2" id="usuario_clave_2E" >
                            <div id="mensajeEdit" class="text-danger"></div>
                        </div>
                    </div>
                    <!-- <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Caja</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <div class="form-group">
                                <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_caja">
                                    <php
                                    if (!empty($lista_cajas)) {
                                        foreach ($lista_cajas as $caja) {
                                            // Comprobar si la caja actual es la caja del usuario
                                            $selected = ($usuario_caja_actual_id == $caja["caja_id"]) ? "selected" : "";
                                            echo '<option value="' . $caja["caja_id"] . '" ' . $selected . '>' . $caja["caja_nombre"] . '</option>';
                                        }
                                    } else {
                                        echo '<option value="" disabled>No hay cajas disponibles</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div> -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" id="guardarEdit">Guardar</button>
                </div>
        </form>
    </div>

<style>
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
</style>
<?php include("../templates/footer.php") ?>