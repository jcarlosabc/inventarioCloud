<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    // Obtener la categoría actual del producto
    $datos_usuario = $conexion->prepare("SELECT usuario.*, caja.*
     FROM usuario
     INNER JOIN caja ON usuario.caja_id = caja.caja_id
     WHERE usuario.usuario_id =:usuario_id");

    $datos_usuario->bindParam(":usuario_id", $txtID);
    $datos_usuario->execute();
    $registro = $datos_usuario->fetch(PDO::FETCH_ASSOC);

    $usuario_nombre = $registro["usuario_nombre"];
    $usuario_apellido = $registro["usuario_apellido"];
    $usuario_email = $registro["usuario_email"];
    $usuario_clave = $registro["usuario_clave"];
    $usuario_tipo = $registro["usuario_usuario"];
    $usuario_caja_actual = $registro["caja_nombre"];

    $usuario_caja_actual_id = $registro["caja_id"];

    if ($_POST) {
        $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
        $usuario_nombre = isset($_POST['usuario_nombre']) ? $_POST['usuario_nombre'] : "";
        $usuario_apellido = isset($_POST['usuario_apellido']) ? $_POST['usuario_apellido'] : "";
        $usuario_email = isset($_POST['usuario_email']) ? $_POST['usuario_email'] : "";
        $usuario_clave = isset($_POST['usuario_clave_1']) ? $_POST['usuario_clave_1'] : "";
        $usuario_rol = isset($_POST["usuario_rol"]) ? $_POST["usuario_rol"] : "";
        $usuario_caja = isset($_POST["usuario_caja"]) ? $_POST["usuario_caja"] : "";

        $username = "u" . $usuario_apellido;
        $responsable = isset($_SESSION['usuario_id']) ?   $_SESSION['usuario_id']  : 0;

        $sentencia_edit = $conexion->prepare("UPDATE usuario SET 
        usuario_nombre=:usuario_nombre,
        usuario_apellido=:usuario_apellido,
        usuario_email=:usuario_email,
        usuario_usuario =:usuario_usuario,
        usuario_clave=:usuario_clave,
        rol=:rol,
        caja_id = :caja_id
        WHERE usuario_id =:usuario_id");

        $sentencia_edit->bindParam(":usuario_id", $txtID);
        $sentencia_edit->bindParam(":usuario_nombre", $usuario_nombre);
        $sentencia_edit->bindParam(":usuario_apellido", $usuario_apellido);
        $sentencia_edit->bindParam(":usuario_email", $usuario_email);
        $sentencia_edit->bindParam(":usuario_usuario", $username);
        $sentencia_edit->bindParam(":usuario_clave", $usuario_clave);
        $sentencia_edit->bindParam(":rol", $usuario_rol);
        $sentencia_edit->bindParam(":caja_id", $usuario_caja);

        $resultado_edit = $sentencia_edit->execute();
        if ($resultado_edit) {
            echo '<script>
        Swal.fire({
            title: "Usuario Actualizado Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventarioCloud/secciones/empleados/";
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
    $sentencia = $conexion->prepare("SELECT * FROM `caja` ");
    $sentencia->execute();
    $lista_cajas = $sentencia->fetchAll(PDO::FETCH_ASSOC);
}

echo '<script>  
        document.addEventListener("DOMContentLoaded", function() {
            pass1 = document.getElementById("usuario_clave_1");
            pass2 = document.getElementById("usuario_clave_2");
            var mensaje = document.getElementById("mensaje");

            pass1.addEventListener("input", function() {
                if (pass1.value === pass2.value) {
                    mensaje.textContent = "Las contraseñas coinciden.";
                } else {
                    mensaje.textContent = "Las contraseñas no coinciden.";
                }
            });
            pass2.addEventListener("input", function() {
                if (pass1.value === pass2.value) {
                    mensaje.textContent = "Las contraseñas coinciden.";
                    document.getElementById("guardar").disabled = false;

                } else {
                    mensaje.textContent = "Las contraseñas no coinciden.";
                    document.getElementById("guardar").disabled = true;
                }
            });
        });
    </script>';

?>

<br>

<!-- left column -->
<div class="">
    <!-- general form elements -->
    <div class="card card-warning" style="margin-top:7%">
        <div class="card-header">
            <h3 class="card-title textTabla">EDITAR USUARIO</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post" enctype="multipart/form-data">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="hidden" name="txtID" id="txtID" value="<?= $txtID ?>">
                            <input type="text" class="form-control camposTabla" name="usuario_nombre" id="usuario_nombre" value="<?= $usuario_nombre ?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Apellido</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="usuario_apellido" id="usuario_apellido" value="<?= $usuario_apellido ?>">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Email</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="email" class="form-control camposTabla" name="usuario_email" id="usuario_email" value="<?= $usuario_email ?>">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla" name="usuario_clave_1" id="usuario_clave_1" value="">
                        </div>
                    </div>


                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Confime la Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla" name="usuario_clave_2" id="usuario_clave_2" value="">
                            <div id="mensaje" class="text-danger"></div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Rol de usuario</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <div class="form-group">
                                <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_rol">

                                    <option value="<?= $usuario_tipo ?>" selected><?= $usuario_tipo ?></option>
                                    <option value="1">Empleado</option>
                                    <option value="0">Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Caja</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <div class="form-group">
                                <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_caja">
                                    <?php foreach ($lista_cajas as $registro) {
                                        $selected = ($usuario_caja_actual_id == $registro["caja_id"]) ? "selected" : "";
                                        echo '<option value="' . $registro["caja_id"] . '" ' . $selected . '>' . $registro["caja_nombre"] . '</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" id="guardar">Guardar</button>
                </div>
        </form>
    </div>
    <!-- /.card -->
</div>

<?php include("../../templates/footer_content.php") ?>