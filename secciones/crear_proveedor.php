<?php include("../templates/header.php") ?>
<?php

if ($_POST) {
    $nit_proveedores = isset($_POST['nit_proveedore']) ? $_POST['nit_proveedore'] : " ";
    $nombre_proveedores = isset($_POST['nombre_proveedore']) ? $_POST['nombre_proveedore'] : " ";
    $email_proveedores = isset($_POST['email_proveedore']) ? $_POST['email_proveedore'] : " ";
    $telefono_proveedores =  isset($_POST['telefono_proveedores']) ? $_POST['telefono_proveedores'] : " ";
    $direccion_proveedores = isset($_POST['direccion_proveedore']) ? $_POST['direccion_proveedore'] : " ";
    $responsable = $_SESSION['usuario_id'];

    $link =  isset($_POST['link']) ? $_POST['link'] : "";
    if ($responsable == 1) {
        $link =  "sudo_admin";
    }
    $sentencia = $conexion->prepare("INSERT INTO proveedores (id_proveedores ,
        nit_proveedores, nombre_proveedores, email_proveedores, telefono_proveedores, direccion_proveedores, link) 
        VALUES (NULL, :nit_proveedores , :nombre_proveedores, :email_proveedores , :telefono_proveedores, :direccion_proveedores, :link)");

    $sentencia->bindParam(":nit_proveedores", $nit_proveedores);
    $sentencia->bindParam(":nombre_proveedores", $nombre_proveedores);
    $sentencia->bindParam(":email_proveedores", $email_proveedores);
    $sentencia->bindParam(":telefono_proveedores", $telefono_proveedores);
    $sentencia->bindParam(":direccion_proveedores", $direccion_proveedores);
    $sentencia->bindParam(":link", $link);
    $resultado = $sentencia->execute();

    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "Proveedor creada Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href= "'.$url_base.'secciones/'.$lista_proveedore_link.'"
            }
        })
        </script>';
    } else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear ¡Proveedor",
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
            <h2 class="card-title textTabla">REGISTRE PROVEEDORES &nbsp;  <a href="<?php echo $url_base;?>secciones/<?php echo $lista_proveedor_link;?>" class="btn btn-warning" style="color:black"> Lista proveedores </a></h2>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="producto_nombre" class="textLabel">Nit</label> &nbsp;<i class="nav-icon fas fa-edit">  </i>
                            <input type="text" class="form-control camposTabla" name="nit_proveedore" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nombre_proveedore" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Correo</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="email" class="form-control camposTabla" name="email_proveedore" required>
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Teléfono </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="telefono_proveedores" required>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Dirección </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="direccion_proveedore" required>
                            <div id="mensaje" class="text-danger"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="link" value="<?php echo $link ?>">
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
                    <a role="button" href="<?php echo $url_base;?>secciones/<?php echo $lista_proveedore_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>
<?php include("../templates/footer.php") ?>