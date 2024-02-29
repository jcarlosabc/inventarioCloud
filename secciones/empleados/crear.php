<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");

if ($_POST) {

    $usuario_nombre = isset($_POST['usuario_nombre']) ? $_POST['usuario_nombre'] : "";
    $usuario_apellido = isset($_POST['usuario_apellido']) ? $_POST['usuario_apellido'] : "";
    $usuario_email = isset($_POST['usuario_email']) ? $_POST['usuario_email'] : "";
    $usuario_clave =  hash('sha256', (isset($_POST['usuario_clave_1']) ? $_POST['usuario_clave_1'] : ""));
    $usuario_rol = isset($_POST['usuario_rol']) ? $_POST['usuario_rol'] : "";
    $usuario_caja = isset($_POST['usuario_caja']) ? $_POST['usuario_caja'] : 0;
    $username = "u" . $usuario_apellido;
    $responsable = isset($_SESSION['usuario_id']) ?   $_SESSION['usuario_id']  : 0;

    $sentencia = $conexion->prepare("INSERT INTO usuario (usuario_id,
        usuario_nombre,
        usuario_apellido , 
        usuario_email,
        usuario_usuario,
        usuario_clave,
        rol,
        caja_id,
        responsable) 
        VALUES (NULL, :usuario_nombre , :usuario_apellido, :usuario_email , :usuario_usuario, :usuario_clave, :rol, :caja_id, :responsable)");

    $sentencia->bindParam(":usuario_nombre", $usuario_nombre);
    $sentencia->bindParam(":usuario_apellido", $usuario_apellido);
    $sentencia->bindParam(":usuario_email", $usuario_email);
    $sentencia->bindParam(":usuario_usuario", $username);
    $sentencia->bindParam(":usuario_clave", $usuario_clave);
    $sentencia->bindParam(":rol", $usuario_rol);
    $sentencia->bindParam(":caja_id", $usuario_caja);
    $sentencia->bindParam(":responsable", $responsable);


    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "¡Usuario ha si Creado Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href="http://localhost/inventariocloud/secciones/empleados/"
            }
        })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear Usuario",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}


$sentencia = $conexion->prepare("SELECT * FROM `caja` ");
$sentencia->execute();
$lista_cajas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

echo '';


?>

<br>

<!-- left column -->
<div class="">
    <!-- general form elements -->
    <div class="card card-primary" style="margin-top:7%">
        <div class="card-header" >
            <h2 class="card-title textTabla">REGISTRE NUEVO USUARIO &nbsp; <a href="<?=$url_base?>secciones/empleados/" class="btn btn-warning" style="color:black"> Lista de usuario </a>
            </h2>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post" enctype="multipart/form-data" id="form" >
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="usuario_nombre" id="usuario_nombre" required>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Apellido</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="usuario_apellido" id="usuario_apellido" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Email</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="email" class="form-control camposTabla" name="usuario_email" id="usuario_email" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla" name="usuario_clave_1" id="usuario_clave_1" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Confime la Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla" name="usuario_clave_2" id="usuario_clave_2" required>
                            <div id="mensaje" class="text-danger"></div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Rol de usuario</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <div class="form-group">
                                <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_rol">
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
                                <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_caja"
                                    <?php echo empty($lista_cajas) ? 'disabled' : ''; ?>>
                                    <?php foreach ($lista_cajas as $registro) { ?>
                                        <option value="<?php echo $registro['caja_id']; ?>"><?php echo $registro['caja_nombre']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" id="guardar" name="guardar">Guardar</button>
                </div>
        </form>
    </div>
    <!-- /.card -->
</div>

<script>  
        document.addEventListener("DOMContentLoaded", function() {
            pass1 = document.getElementById("usuario_clave_1");
            pass2 = document.getElementById("usuario_clave_2");
            var mensaje = document.getElementById("mensaje");

            pass1.addEventListener("input", function() {
                if (pass1.value === pass2.value) {
                   // mensaje.textContent = "Las contraseñas coinciden.";
                } else {
                 //   mensaje.textContent = "Las contraseñas no coinciden.";
                }
            });
            pass2.addEventListener("input", function() {
                if (pass1.value === pass2.value) {
                    mensaje.textContent = "";
                    document.getElementById("guardar").disabled = false;

                } else {
                    mensaje.textContent = "Las contraseñas no coinciden.";
                    document.getElementById("guardar").disabled = true;
                }
            });
        });
    </script>

<?php include("../../templates/footer_content.php") ?>