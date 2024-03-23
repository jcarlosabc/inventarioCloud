<?php include("../templates/header.php") ?>
<?php

if ($_POST) {

    $nit_empresa = isset($_POST['nit_empresa']) ?   $_POST['nit_empresa']  : " ";
    $nombre_empresa = isset($_POST['nombre_empresa']) ? $_POST['nombre_empresa'] : "";
    $clave_user = isset($_POST['nombre_empresa']) ? str_replace(" ",'', $_POST['nombre_empresa']) : "";
    $telefono_empresa = isset($_POST['telefono_empresa']) ? $_POST['telefono_empresa'] : "";
    $email_empresa = isset($_POST['email_empresa']) ? $_POST['email_empresa'] : "";
    $direccion_empresa = isset($_POST['direccion_empresa']) ? $_POST['direccion_empresa'] : "";
    $responsable = isset($_SESSION['usuario_id']) ?  $_SESSION['usuario_id']  : 0;

    //link_empresa 
    $link_empresa = isset($_POST['link_empresa']) ?  $_POST['link_empresa']  : " ";
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
    $sql = "INSERT INTO empresa (empresa_nombre, empresa_telefono, empresa_email,
    empresa_direccion, empresa_nit, empresa_logo, link, codigo_seguridad , responsable) 
    VALUES (?,?,?,?,?,?,?,?,?)";

    $sentencia = $conexion->prepare($sql);
    $params = array(
        $nombre_empresa,
        $telefono_empresa,
        $email_empresa,
        $direccion_empresa,
        $nit_empresa,
        $empresa_logo,
        $link_empresa,
        $codigo_seguridad,
        $responsable
    );
    $sentencia->execute($params);

    // GUARDAR USUARIO DE LA EMPRESA
    $sql = "INSERT INTO usuario (usuario_nombre, usuario_apellido, usuario_telefono, usuario_email, usuario_usuario,
    usuario_clave, rol, usuario_foto, caja_id, link ,responsable) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $sentencia_usuario = $conexion->prepare($sql);
    $params = array(
        "Admin_".$nombre_empresa,
        "N/A",
        "N/A",
        "N/A",
        $nombre_empresa,
        hash('sha256', $clave_user),
        "1",
        "N/A",
        "0",
        $link_empresa,
        $responsable
    );
    $resultado = $sentencia_usuario->execute($params);
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "Empresa Creada Correctamente!",
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
            title: "Error al Crear la Empresa ",
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
            <h2 class="card-title textTabla">CREAR EMPRESA &nbsp; </h2>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post" enctype="multipart/form-data" id="form">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Nit </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nit_empresa" required >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <!-- <input type="hidden" class="form-control camposTabla" name="txtID" id="nombre_empresa" required value=""> -->
                            <input type="hidden" class="form-control camposTabla" name="txtResponsable_empresa">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nombre_empresa" required>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Teléfono</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="telefono_empresa" required >
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Dirección</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="direccion_empresa" required >
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
                            <input type="text" class="form-control camposTabla"name="codigo_seguridad" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="text-align:center">
                <input type="hidden" id="linkEmpresa" name="link_empresa">
                <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>
<?php include("../templates/footer.php") ?>