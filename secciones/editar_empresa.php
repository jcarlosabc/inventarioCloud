<?php include("../templates/header.php") ?>
<?php
if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    $datos_empresa = $conexion->prepare("SELECT * FROM empresa WHERE empresa_id = :empresa_id");

    $datos_empresa->bindParam(":empresa_id", $txtID);
    $datos_empresa->execute();
    $registro = $datos_empresa->fetch(PDO::FETCH_ASSOC);

    $empresa_nombre = $registro['empresa_nombre'];
    $empresa_telefono = $registro['empresa_telefono'];
    $empresa_direccion = $registro['empresa_direccion'];
    $empresa_nit = $registro['empresa_nit'];
    $codigo_seguridad = $registro['codigo_seguridad'];
    $link = $registro['link'];
}

if ($_POST) {
    $txtID = isset($_POST['empresa_id']) ?   $_POST['empresa_id']  : " ";
    $nit_empresa = isset($_POST['nit_empresa']) ?   $_POST['nit_empresa']  : " ";
    $nombre_empresa = isset($_POST['nombre_empresa']) ? $_POST['nombre_empresa'] : "";
    $clave_user = isset($_POST['nombre_empresa']) ? str_replace(" ",'', $_POST['nombre_empresa']) : "";
    $telefono_empresa = isset($_POST['telefono_empresa']) ? $_POST['telefono_empresa'] : "";
    $email_empresa = isset($_POST['email_empresa']) ? $_POST['email_empresa'] : "";
    $direccion_empresa = isset($_POST['direccion_empresa']) ? $_POST['direccion_empresa'] : "";
    $responsable = isset($_SESSION['usuario_id']) ?  $_SESSION['usuario_id']  : 0;

    //link_empresa 
    $link = isset($_POST['link']) ?   $_POST['link']  : " ";
    $codigo_seguridad = isset($_POST['codigo_seguridad']) ? $_POST['codigo_seguridad'] : " ";
    $empresa_logo = "";


    //valiadaciones para la fotos !! Jmendoza
    if(isset($_FILES['logo_empresa']) && $_FILES['logo_empresa']['error'] === UPLOAD_ERR_OK && $_FILES['logo_empresa']['size'] > 0){

        $tipo = $_FILES['logo_empresa']['type'];
        $tamano = $_FILES['logo_empresa']['size'];
        $imagen_temporal = $_FILES['logo_empresa']['tmp_name'];
        $empresa_logo = base64_encode(file_get_contents($imagen_temporal));

    }

    //  GUARDANDO LA EMPRSA CREADA
    $sql = "UPDATE empresa SET empresa_nombre = :nombre, empresa_telefono = :telefono, empresa_email = :email, empresa_direccion = :direccion, empresa_nit = :nit, empresa_logo = :logo, link = :link, codigo_seguridad = :codigo_seguridad, responsable = :responsable WHERE empresa_id = :empresa_id";
$sentencia = $conexion->prepare($sql);
$params = array(
    ':nombre' => $nombre_empresa,
    ':telefono' => $telefono_empresa,
    ':email' => $email_empresa,
    ':direccion' => $direccion_empresa,
    ':nit' => $nit_empresa,
    ':logo' => $empresa_logo,
    ':link' => $link,
    ':codigo_seguridad' => $codigo_seguridad,
    ':responsable' => $responsable,
    ':empresa_id' => $txtID
);
$sentencia->execute($params);

    // GUARDAR USUARIO DE LA EMPRESA
    $sql = "UPDATE usuario SET usuario_nombre = :nombre, usuario_apellido = :apellido, usuario_telefono = :telefono, usuario_email = :email, usuario_usuario = :usuario, usuario_clave = :clave, rol = :rol, usuario_foto = :foto, caja_id = :caja_id, responsable = :responsable WHERE link = :link";
$sentencia_usuario = $conexion->prepare($sql);
$params = array(
    ':nombre' => "Admin_".$nombre_empresa,
    ':apellido' => "N/A",
    ':telefono' => "N/A",
    ':email' => "N/A",
    ':usuario' => $nombre_empresa,
    ':clave' => hash('sha256', $clave_user),
    ':rol' => "1",
    ':foto' => "N/A",
    ':caja_id' => "0",
    ':responsable' => $responsable,
    ':link' => $link
);
$resultado = $sentencia_usuario->execute($params);

    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "Empresa Editada Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href="'.$url_base.'secciones/"
            }
        })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            title: "Error al Editar la Empresa ",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
    
}
?>
<br>
<!-- left column -->
<div class="">
    <!-- general form elements -->
    <div class="card card-primary" style="margin-top:7%">
        <div class="card-header">
            <h2 class="card-title textTabla">EDITAR EMPRESA &nbsp; </h2>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post" enctype="multipart/form-data" id="form">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Nit </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nit_empresa" value="<?=$empresa_nit?>" required >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <!-- <input type="hidden" class="form-control camposTabla" name="txtID" id="nombre_empresa" required value=""> -->
                            <input type="hidden" class="form-control camposTabla" name="txtResponsable_empresa">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nombre_empresa" value="<?=$empresa_nombre?>" required>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Teléfono</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="telefono_empresa" value="<?=$empresa_telefono?>" required >
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Dirección</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="direccion_empresa" value="<?=$empresa_direccion?>" required >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Logo</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="file" class="form-control camposTabla" aria-label="file example" name="logo_empresa" id="logo_empresa" accept=".png" maxlength="600000" >
                            <!-- El valor de maxlength es para 600 KB -->
                            <div class="invalid-feedback">Example invalid form file feedback</div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Clave para Permisos</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla"name="codigo_seguridad" value="<?=$codigo_seguridad?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="text-align:center">
                <input type="hidden" id="link" name="link" value="<?=$link?>">
                <input type="hidden" id="empresa_id" name="empresa_id" value="<?=$txtID?>">
                <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>
<?php include("../templates/footer.php") ?>