<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");

if ($_POST) {

    $nombre_confi = isset($_POST['nombre_confi']) ? $_POST['nombre_confi'] : "";
    $telefono_confi = isset($_POST['telefono_confi']) ? $_POST['telefono_confi'] : "";
    $email_confi = isset($_POST['email_confi']) ? $_POST['email_confi'] : "";
    $direccion_confi = isset($_POST['direccion_confi']) ? $_POST['direccion_confi'] : "";

    $nit_confi = isset($_SESSION['nit_confi']) ?   $_SESSION['nit_confi']  : " ";
    $responsable = isset($_SESSION['usuario_id']) ?   $_SESSION['usuario_id']  : 0;

    $empresa_logo = "";

    if(isset($_FILES['logo_confi'])){

        //$empresa_logo = $_FILES['logo_confi']['name'];
        $tipo = $_FILES['logo_confi']['type'];
        $tamano = $_FILES['logo_confi']['size'];
        $imagen_temporal = $_FILES['logo_confi']['tmp_name'];

        $empresa_logo = base64_encode(file_get_contents($imagen_temporal));

    }

    $sentencia = $conexion->prepare("INSERT INTO empresa (empresa_id,
        empresa_nombre,
        empresa_telefono , 
        empresa_email,
        empresa_direccion,
        empresa_nit,
        empresa_logo,
        responsable) 
        VALUES (NULL,:empresa_nombre, :empresa_telefono, :empresa_email, :empresa_direccion, :empresa_nit , :empresa_logo, :responsable)");

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
                window.location.href="http://localhost/inventariocloud/secciones/configuracion/crear.php"
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
            <h2 class="card-title textTabla">Configuración de la Empresa &nbsp; </h2>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post" enctype="multipart/form-data" id="form">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nombre_confi" id="nombre_confi" required>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Telefono</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="telefono_confi" id="telefono_confi" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Email</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="email" class="form-control camposTabla" name="email_confi" id="email_confi" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Direccion</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="direccion_confi" id="direccion_confi" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">logo</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="file" class="form-control camposTabla" aria-label="file example" name="logo_confi" id="logo_confi" >
                            <div class="invalid-feedback">Example invalid form file feedback</div>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Nit </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nit_confi" id="nit_confi" >
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer" style="text-align:center">
                <button type="submit" class="btn btn-primary btn-lg" id="guardar" name="guardar">Guardar</button>
                <a role="button" href="#" class="btn btn-danger btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>



<?php include("../../templates/footer_content.php") ?>