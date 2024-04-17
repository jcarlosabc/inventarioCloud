<?php include("../templates/header.php") ?>
<?php
if (isset($_GET['txtID'])) {
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";

    $datos_empresa = $conexion->prepare("SELECT *, 'empresa' AS origen FROM empresa WHERE empresa_id = :empresa_id
                                         UNION
                                         SELECT *, 'empresa_bodega' AS origen FROM empresa_bodega WHERE bodega_id = :empresa_id");

    $datos_empresa->bindParam(":empresa_id", $txtID);
    $datos_empresa->execute();
    $registro = $datos_empresa->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        $empresa_nombre = $registro['empresa_nombre'];
        $empresa_telefono = $registro['empresa_telefono'];
        $empresa_direccion = $registro['empresa_direccion'];
        $empresa_nit = $registro['empresa_nit'];
        $codigo_seguridad = $registro['codigo_seguridad'];
        $link = $registro['link'];
        $origen = $registro['origen'];
    }
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
    $nueva_clave = $_POST['nueva_clave'];
    $clave_info = $nueva_clave;

    //link_empresa 
    $link = isset($_POST['link']) ?   $_POST['link']  : " ";
    $codigo_seguridad = isset($_POST['codigo_seguridad']) ? $_POST['codigo_seguridad'] : " ";   

    if ($txtID == 9000) {
        $sql = "UPDATE empresa_bodega SET bodega_nombre = :nombre, bodega_telefono = :telefono, bodega_email = :email, bodega_direccion = :direccion, bodega_nit = :nit, link = :link, codigo_seguridad = :codigo_seguridad, responsable = :responsable WHERE bodega_id = :empresa_id";
$sentencia = $conexion->prepare($sql);
$params = array(
    ':nombre' => $nombre_empresa,
    ':telefono' => $telefono_empresa,
    ':email' => $email_empresa,
    ':direccion' => $direccion_empresa,
    ':nit' => $nit_empresa,
    ':link' => $link,
    ':codigo_seguridad' => $codigo_seguridad,
    ':responsable' => $responsable,
    ':empresa_id' => $txtID
);
$sentencia->execute($params);
    }else {
        //  GUARDANDO LA EMPRSA CREADA
        $sql = "UPDATE empresa SET empresa_nombre = :nombre, empresa_telefono = :telefono, empresa_email = :email, empresa_direccion = :direccion, empresa_nit = :nit, link = :link, codigo_seguridad = :codigo_seguridad, responsable = :responsable WHERE empresa_id = :empresa_id";
    $sentencia = $conexion->prepare($sql);
    $params = array(
        ':nombre' => $nombre_empresa,
        ':telefono' => $telefono_empresa,
        ':email' => $email_empresa,
        ':direccion' => $direccion_empresa,
        ':nit' => $nit_empresa,
        ':link' => $link,
        ':codigo_seguridad' => $codigo_seguridad,
        ':responsable' => $responsable,
        ':empresa_id' => $txtID
    );
    $sentencia->execute($params);

    }

    // GUARDAR USUARIO DE LA EMPRESA
    $sql = "UPDATE usuario SET usuario_nombre = :nombre, usuario_apellido = :apellido, usuario_telefono = :telefono, usuario_email = :email, usuario_usuario = :usuario, usuario_clave = :clave, info_clave = :clave_info, rol = :rol, usuario_foto = :foto, caja_id = :caja_id, responsable = :responsable WHERE link = :link";
$sentencia_usuario = $conexion->prepare($sql);
$params = array(
    ':nombre' => "Admin_".$nombre_empresa,
    ':apellido' => "N/A",
    ':telefono' => "N/A",
    ':email' => "N/A",
    ':usuario' => $nombre_empresa,
    ':clave' => hash('sha256', $nueva_clave),
    ':clave_info' => $clave_info,
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
                            <label class="textLabel">Contraseña de Usuario</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="password" class="form-control camposTabla"name="nueva_clave">
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