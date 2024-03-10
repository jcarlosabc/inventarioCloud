<?php include("../templates/header.php") ?>
<?php
  
$sentencia=$conexion->prepare("SELECT * FROM empresa LIMIT 1");
$sentencia->execute();
$lista_empresa=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

$txtID  =  "";
$txtNombre_empresa = "";
$txtTelefono_empresa  = "";
$txtEmail_empreesa = "";
$txtDireccion_empresa = "";
$txtNit_empresa= "";
$txtLogo_empresa = "";
$txtResposable_empresa ="";

if($lista_empresa){
    
   $txtID  =  $lista_empresa[0]['empresa_id'];
   $txtNombre_empresa = $lista_empresa[0] ['empresa_nombre'];
   $txtTelefono_empresa  = $lista_empresa [0]['empresa_telefono'];
   $txtEmail_empreesa = $lista_empresa[0]['empresa_email'];
   $txtDireccion_empresa = $lista_empresa[0]['empresa_direccion'];
   $txtNit_empresa= $lista_empresa[0]['empresa_nit'];
   $txtLogo_empresa = $lista_empresa[0]['empresa_logo'];
   $txtResposable_empresa =   $lista_empresa[0]['responsable'];
}

if ($_POST &&  $_POST['txtID'] == "") {
    $nombre_confi = isset($_POST['nombre_confi']) ? $_POST['nombre_confi'] : "";
    $telefono_confi = isset($_POST['telefono_confi']) ? $_POST['telefono_confi'] : "";
    $email_confi = isset($_POST['email_confi']) ? $_POST['email_confi'] : "";
    $direccion_confi = isset($_POST['direccion_confi']) ? $_POST['direccion_confi'] : "";
    $nit_confi = isset($_POST['nit_confi']) ?   $_POST['nit_confi']  : " ";
    $responsable = isset($_SESSION['usuario_id']) ?   $_SESSION['usuario_id']  : 0;
    $empresa_logo = "";
    if(isset($_FILES['logo_confi'])){
        $tipo = $_FILES['logo_confi']['type'];
        $tamano = $_FILES['logo_confi']['size'];
        $imagen_temporal = $_FILES['logo_confi']['tmp_name'];
        $empresa_logo = base64_encode(file_get_contents($imagen_temporal));
    }
    $sentencia = $conexion->prepare("INSERT INTO empresa (empresa_id,
        empresa_nombre, empresa_telefono, empresa_email,
        empresa_direccion, empresa_nit, empresa_logo,  responsable) 
        VALUES (NULL,:empresa_nombre, :empresa_telefono, :empresa_email, :empresa_direccion, :empresa_nit, :empresa_logo, :responsable)");

    $sentencia->bindParam(":empresa_nombre", $nombre_confi);
    $sentencia->bindParam(":empresa_telefono", $telefono_confi);
    $sentencia->bindParam(":empresa_email", $email_confi);
    $sentencia->bindParam(":empresa_direccion", $direccion_confi);
    $sentencia->bindParam(":empresa_nit", $nit_confi);
    $sentencia->bindParam(":empresa_logo", $empresa_logo);
    $sentencia->bindParam(":responsable", $responsable);

    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "Configuración realizada correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href="http://localhost/inventariocloud/secciones/crear_config.php"
            }
        })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear la configuración ",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
    

} else if($_POST &&  $_POST['txtID'] != ""){
    
    $nombre_confi = isset($_POST['nombre_confi']) ? $_POST['nombre_confi'] : "";
    $telefono_confi = isset($_POST['telefono_confi']) ? $_POST['telefono_confi'] : "";
    $email_confi = isset($_POST['email_confi']) ? $_POST['email_confi'] : "";
    $direccion_confi = isset($_POST['direccion_confi']) ? $_POST['direccion_confi'] : "";
    $nit_confi = isset($_POST['nit_confi']) ?   $_POST['nit_confi']  : " ";
    $empresa_ID = isset($_POST['txtID']) ? $_POST['txtID'] : "";
    $responsable = isset($_SESSION['usuario_id']) ?   $_SESSION['usuario_id']  : 0;
    $empresa_logo = "";

    if(isset($_FILES['logo_confi']) && $_FILES['logo_confi']['error'] === UPLOAD_ERR_OK) {
        $tipo = $_FILES['logo_confi']['type'];
        $tamano = $_FILES['logo_confi']['size'];
        $imagen_temporal = $_FILES['logo_confi']['tmp_name'];
        $empresa_logo = base64_encode(file_get_contents($imagen_temporal));
        // Aquí puedes procesar el archivo de imagen
    }

    $sentencia_edit = $conexion->prepare("UPDATE empresa SET 
    empresa_nombre=:empresa_nombre, empresa_telefono=:empresa_telefono,
    empresa_email=:empresa_email, empresa_direccion =:empresa_direccion,
    empresa_nit=:empresa_nit, empresa_logo=:empresa_logo,
    responsable = :responsable
    WHERE empresa_id  =:empresa_id ");

    $sentencia_edit->bindParam(":empresa_nombre", $nombre_confi);
    $sentencia_edit->bindParam(":empresa_telefono", $telefono_confi);
    $sentencia_edit->bindParam(":empresa_email", $email_confi);
    $sentencia_edit->bindParam(":empresa_direccion", $direccion_confi);
    $sentencia_edit->bindParam(":empresa_nit", $nit_confi);
    $sentencia_edit->bindParam(":empresa_logo", $empresa_logo);
    $sentencia_edit->bindParam(":responsable", $responsable);
    $sentencia_edit->bindParam(":empresa_id", $txtID);
    $resultado_edit = $sentencia_edit->execute();

    $resultado = $sentencia_edit->execute();
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "Configuración Actualizada Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href=http://localhost/inventariocloud/secciones/crear_config.php"
            }
        })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear la configuración ",
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
            <h2 class="card-title textTabla">CONFIGURAR EMPRESA &nbsp; </h2>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post" enctype="multipart/form-data" id="form">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Nit </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nit_confi" value="<?=$txtNit_empresa?>" >
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <input type="hidden" class="form-control camposTabla" name="txtID" id="nombre_confi" required value="<?=$txtID?>">
                            <input type="hidden" class="form-control camposTabla" name="txtResponsable_empresa" id="nombre_confi" required value="<?=$txtResposable_empresa?>">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nombre_confi"  value="<?=$txtNombre_empresa?>">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Teléfono</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="telefono_confi" value="<?=$txtTelefono_empresa?>">
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Dirección</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="direccion_confi" id="direccion_confi" value="<?=$txtDireccion_empresa?>">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Logo</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="file" class="form-control camposTabla" aria-label="file example" name="logo_confi" id="logo_confi" accept=".png" maxlength="600000" >
                            <!-- El valor de maxlength es para 600 KB -->
                            <div class="invalid-feedback">Example invalid form file feedback</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="text-align:center">
                <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>
<?php include("../templates/footer.php") ?>