<?php include("../templates/header.php") ?>
<?php

    // Contando la cantidad maxima de empresa ya que son 6 
    // 5 locales y 1 bodega
    $sentencia = $conexion->prepare("SELECT COUNT(*) as total_empresas FROM empresa ");
    $sentencia->execute();
    $lista_empresas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $limite_empresas = false;
    if ($lista_empresas) {$total_empresas = $lista_empresas[0]['total_empresas'];
        if ($total_empresas == 5) {$limite_empresas = true;}
    }


if ($_POST) {

    $nit_empresa = isset($_POST['nit_empresa']) ? $_POST['nit_empresa'] : "";
    $nombre_empresa = isset($_POST['nombre_empresa']) ? $_POST['nombre_empresa'] : "";
    $clave_user = isset($_POST['nombre_empresa']) ? str_replace(" ", '', $_POST['nombre_empresa']) : "";
    $telefono_empresa = isset($_POST['telefono_empresa']) ? $_POST['telefono_empresa'] : "";
    $email_empresa = isset($_POST['email_empresa']) ? $_POST['email_empresa'] : "";
    $direccion_empresa = isset($_POST['direccion_empresa']) ? $_POST['direccion_empresa'] : "";
    $responsable = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

    //link_empresa 
    $link_empresa = isset($_POST['link_empresa']) ? $_POST['link_empresa'] : " ";
    $codigo_seguridad = isset($_POST['codigo_seguridad']) ? $_POST['codigo_seguridad'] : " ";

    //  GUARDANDO LA EMPRSA CREADA
    $sql = "INSERT INTO empresa (empresa_nombre, empresa_telefono, empresa_email,
    empresa_direccion, empresa_nit, link, codigo_seguridad , responsable) 
    VALUES (?,?,?,?,?,?,?,?)";

    $sentencia = $conexion->prepare($sql);
    $params = array(
        $nombre_empresa,
        $telefono_empresa,
        $email_empresa,
        $direccion_empresa,
        $nit_empresa,
        $link_empresa,
        $codigo_seguridad,
        $responsable
    );
    $sentencia->execute($params);

    // GUARDAR USUARIO DE LA EMPRESA
    $sql = "INSERT INTO usuario (usuario_nombre, usuario_apellido, usuario_telefono, usuario_cedula, usuario_email, usuario_usuario,
    usuario_clave, rol, usuario_foto, caja_id, link ,responsable) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $sentencia_usuario = $conexion->prepare($sql);
    $params = array(
        "Admin_".$nombre_empresa,
        "N/A",
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
                 window.location.href="'.$url_base.'secciones/index.php"
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
        <?php if($limite_empresas) { ?>
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <h3>Límite de Empresas superado</h3>
                        <article style="padding: 0px 0px 10px;"> 
                            <strong class="text-info"><i class="fa fa-info-circle"></i> 
                                Recuerde: </strong>El maximo de empresas <strong>5</strong> + 1 <strong>Bodega</strong>. 
                                Contacte al Desarrollador. Para ampliar la capacidad de crear <strong>Empresas.</strong>
                        </article>
                    </div>
                </div>
            </div>   
        <?php }else { ?>
            <form action=" " method="post" enctype="multipart/form-data" id="form">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Nit </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nit_empresa" required >
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <!-- <input type="hidden" class="form-control camposTabla" name="txtID" id="nombre_empresa" required value=""> -->
                            <input type="hidden" class="form-control camposTabla" name="txtResponsable_empresa">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nombre_empresa" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
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
                    <div class="col-sm-2">
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
            <?php } ?>
    
    </div>
    <!-- /.card -->
</div>
<?php include("../templates/footer.php") ?>
