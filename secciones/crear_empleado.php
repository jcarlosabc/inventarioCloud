<?php include("../templates/header.php") ?>
<?php
 
 if(isset($_GET['link'])){
    $link=(isset($_GET['link']))?$_GET['link']:"";
 }
 $responsable = $_SESSION['usuario_id'] ;

if ($_POST) {
    $usuario_nombre = isset($_POST['usuario_nombre']) ? $_POST['usuario_nombre'] : "";
    $usuario_apellido = isset($_POST['usuario_apellido']) ? $_POST['usuario_apellido'] : "";
    $username = "u" . $usuario_apellido;
    $usuario_telefono = isset($_POST['usuario_telefono']) ? $_POST['usuario_telefono'] : "";
    $usuario_cedula = isset($_POST['usuario_cedula']) ? $_POST['usuario_cedula'] : "";
    $usuario_email = isset($_POST['usuario_email']) ? $_POST['usuario_email'] : "";
    $usuario_clave =  hash('sha256', (isset($_POST['usuario_clave_1']) ? $_POST['usuario_clave_1'] : ""));
    $responsable = isset($_POST['responsable']) ? $_POST['responsable']  : $responsable ;
    $usuario_rol = 2;
    $usuario_empresa = isset($_POST['usuario_empresa']) ? $_POST['usuario_empresa'] : "";
    $quincena_empleado = isset($_POST['quincena_empleado']) ? $_POST['quincena_empleado'] : "";
    $quincena_empleado = str_replace(array('$','.',','), '', $quincena_empleado); 
    if (!$usuario_empresa) { $usuario_empresa = $link;  }
    $link =  isset($_POST['link']) ? $_POST['link'] : "";
     if ($responsable == 1) {$link = $usuario_empresa; }
    $usuario_caja = isset($_POST['usuario_caja']) ? $_POST['usuario_caja'] : 0;
    $usuario_caja = str_replace(array('$','.',','), '', $usuario_caja); 

    $sentencia_caja = $conexion->prepare("SELECT * FROM caja WHERE link=:link AND caja_id =:caja_id");
    $sentencia_caja->bindParam(":link", $usuario_empresa);
    $sentencia_caja->bindParam(":caja_id", $usuario_caja);
    $sentencia_caja->execute();
    $listas_cajas = $sentencia_caja->fetchAll(PDO::FETCH_ASSOC);

    // if (empty($listas_cajas)) {
        // echo '<script>
        // Swal.fire({
        //     title: "Esta Caja no Pertenece a la empresa seleccionada",
        //     icon: "info",
        //     confirmButtonText: "¡Entendido!"
        // })
        // </script>';
    // } else {
        $sentencia = $conexion->prepare("INSERT INTO usuario (usuario_id,
                usuario_nombre, usuario_apellido, usuario_telefono, usuario_cedula, usuario_email, usuario_usuario,
                usuario_clave, quincena_empleado, rol, caja_id, link, responsable) 
            VALUES (NULL, :usuario_nombre , :usuario_apellido, :usuario_telefono, :usuario_cedula, :usuario_email , :usuario_usuario, :usuario_clave, :quincena_empleado, :rol, :caja_id, :link, :responsable)");

        $sentencia->bindParam(":usuario_nombre", $usuario_nombre);
        $sentencia->bindParam(":usuario_apellido", $usuario_apellido);
        $sentencia->bindParam(":usuario_telefono", $usuario_telefono);
        $sentencia->bindParam(":usuario_cedula", $usuario_cedula);
        $sentencia->bindParam(":usuario_email", $usuario_email);
        $sentencia->bindParam(":usuario_usuario", $username);
        $sentencia->bindParam(":usuario_clave", $usuario_clave);
        $sentencia->bindParam(":quincena_empleado", $quincena_empleado);
        $sentencia->bindParam(":rol", $usuario_rol);
        $sentencia->bindParam(":caja_id", $usuario_caja);
        $sentencia->bindParam(":link", $link);
        $sentencia->bindParam(":responsable", $responsable);
        $resultado = $sentencia->execute();
        if ($resultado) {
            echo '<script>
            Swal.fire({
                title: "Empleado Creado Exitosamente!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href= "'.$url_base.'secciones/'.$index_empleados_link.'"
                }
            })
            </script>';
        } else {
            echo '<script>
            Swal.fire({
                title: "Error al Crear Empleado",
                icon: "error",
                confirmButtonText: "¡Entendido!"
            });
            </script>';
        }
    // }
}

$responsable = $_SESSION['usuario_id'];
if ($responsable == 1) {
    $sentencia = $conexion->prepare("SELECT c.*, e.empresa_nombre FROM caja c JOIN empresa e ON c.link = e.link");
}else{
    $sentencia = $conexion->prepare("SELECT c.*, e.empresa_nombre FROM caja c JOIN empresa e ON c.link = e.link WHERE c.link =:link");
  $sentencia->bindParam(":link",$link);
 }
$sentencia->execute();
$lista_cajas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT * FROM empresa ");
$sentencia->execute();
$lista_empresas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

?>

<br>
    <!-- <article> <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Primero debe crear <strong>Cajas</strong> para asignar a un <strong>Empleado</strong>.  -->
        <!-- <span><a href="<?php echo $url_base;?>secciones/<?php echo $crear_caja_link;?>"><button type="button" class="btn btn-outline-primary">Crear Caja</button></a></span> -->
    <!-- </article> -->
    <article> <strong class="text-info"><i class="fa fa-info-circle"></i> Nota: </strong>La misma <strong> Caja</strong> puede ser <strong>Asignada </strong>a varios empleados.</article>
    <div class="card card-primary" style="margin-top:7%">
        <div class="card-header" >
            <h2 class="card-title textTabla">REGISTRE NUEVO EMPLEADO &nbsp; <a href="<?php echo $url_base;?>secciones/<?php echo $index_empleados_link;?>" class="btn btn-warning" style="color:black"> Lista de Empleados </a>
            </h2>
        </div>
        <form action=" " method="post" id="formEmpleado" onsubmit="return validarFormulario(2)">
            <input type="hidden" name="link" value="<?php echo $link ?>">
            <input type="hidden" name="responsable" value="<?php echo $responsable?>">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Nombres</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="usuario_nombre" required>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Apellidos</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="usuario_apellido" required>
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Teléfono</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="number" class="form-control camposTabla" name="usuario_telefono" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Cédula</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="usuario_cedula" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Correo</label>
                            <input type="email" class="form-control camposTabla" name="usuario_email">
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Quincena del Empleado</label>
                            <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="quincena_empleado" id="quincenaEmpleado">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla" name="usuario_clave_1" id="usuario_clave_1" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Confirme la Clave</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla" name="usuario_clave_2" id="usuario_clave_2" required>
                            <div id="mensaje" class="text-danger"></div>
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <?php if ($responsable == 1) { ?>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Negocio</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                                <div class="form-group">
                                    <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_empresa">
                                        <option value="">Escoger Negocio</option>
                                        <?php foreach ($lista_empresas as $registro) { ?>
                                            <option value="<?php echo $registro['link']; ?>"><?php echo $registro['empresa_nombre']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Caja</label> 
                            <div class="form-group">
                                <!-- php if($lista_cajas){ ?>  -->
                                    <select class="form-control select2 camposTabla" style="width: 100%;" name="usuario_caja">
                                        <option value="">Escoger Caja</option>
                                        <?php foreach ($lista_cajas as $registro) { ?>
                                            <option value="<?php echo $registro['caja_id']; ?>"><?php echo $registro['caja_nombre']; ?> - <?php echo $registro['empresa_nombre']; ?></option>
                                        <?php } ?>
                                    </select>
                                        <!-- ?php } else { ?> -->
                                           <!-- <strong class="text-warning"><i class="fa fa-info-circle"></i> Recuerde: </strong>Debe tener minimo una caja para asignar al <strong>Empleado</strong></article> -->
                                <!-- <php } ?> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
                    <a role="button" href="<?php echo $url_base;?>secciones/<?php echo $index_empleados_link; ?>" class="btn btn-danger btn-lg">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
<style>
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
</style>
<?php include("../templates/footer.php") ?>