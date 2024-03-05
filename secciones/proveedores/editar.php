<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    // Obtener la categoría actual del producto
    $datos_proveedores = $conexion->prepare("SELECT * FROM proveedores  WHERE id_proveedores = :id_proveedores");

    $datos_proveedores->bindParam(":id_proveedores", $txtID);
    $datos_proveedores->execute();
    $registro = $datos_proveedores->fetch(PDO::FETCH_ASSOC);

    $nit_proveedores = $registro['nit_proveedores'];
    $nombre_proveedores = $registro['nombre_proveedores'];
    $email_proveedores = $registro['email_proveedores'];
    $telefono_proveedores = $registro['telefono_proveedores'];
    $direccion_proveedores = $registro['direccion_proveedores'];

    if ($_POST) {

        $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
        $nit_proveedore = isset($_POST['nit_proveedore']) ? $_POST['nit_proveedore'] : " ";
        $nombre_proveedore = isset($_POST['nombre_proveedore']) ? $_POST['nombre_proveedore'] : " ";
        $email_proveedores = isset($_POST['email_proveedores']) ? $_POST['email_proveedores'] : " ";
        $telefono_proveedores = isset($_POST['telefono_proveedores']) ? $_POST['telefono_proveedores'] : " ";
        $direccion_proveedores = isset($_POST['direccion_proveedores']) ? $_POST['direccion_proveedores'] : " ";

        $sentencia_edit = $conexion->prepare("UPDATE proveedores SET 
        nit_proveedores=:nit_proveedores,
        nombre_proveedores=:nombre_proveedores,
        email_proveedores=:email_proveedores,
        telefono_proveedores=:telefono_proveedores,
        direccion_proveedores=:direccion_proveedores
        WHERE id_proveedores=:id_proveedores");

        $sentencia_edit->bindParam(":id_proveedores", $txtID);
        $sentencia_edit->bindParam(":nit_proveedores", $nit_proveedore);
        $sentencia_edit->bindParam(":nombre_proveedores", $nombre_proveedore);
        $sentencia_edit->bindParam(":email_proveedores", $email_proveedores);
        $sentencia_edit->bindParam(":telefono_proveedores", $telefono_proveedores);
        $sentencia_edit->bindParam(":direccion_proveedores", $direccion_proveedores);

        $resultado_edit = $sentencia_edit->execute();

        if ($resultado_edit) {
            echo '<script>
        Swal.fire({
            title: "Usuario Actualizado Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventarioCloud/secciones/proveedores/";
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
}

?>

<br>

<!-- left column -->
<div class="">
    <!-- general form elements -->
    <div class="card card-warning" style="margin-top:7%">
        <div class="card-header">
            <h2 class="card-title textTabla">EDITAR PROVEEDORES &nbsp; </h2>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action=" " method="post" enctype="multipart/form-data" id="form">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                        <input type="hidden" name="txtID" id="txtID" value="<?= $txtID ?>">
                            <label for="producto_nombre" class="textLabel">Nit</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nit_proveedore" id="nit_proveedore" required value="<?=$nit_proveedores?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Nombre </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="nombre_proveedore" id="nombre_proveedore" required  value="<?=$nombre_proveedores?>">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Email</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="email" class="form-control camposTabla" name="email_proveedore" id="email_proveedore" required value="<?=$email_proveedores?>">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Teléfono </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="telefono_proveedores" id="telefono_proveedores" required value="<?=$telefono_proveedores?>">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="textLabel">Dirección </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="direccion_proveedore" id="direccion_proveedore" required value="<?=$direccion_proveedores?>">
                            <div id="mensaje" class="text-danger"></div>
                        </div>
                    </div>
                </div>

                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" id="guardar" name="guardar">Guardar</button>
                    <a role="button" href="#" class="btn btn-danger btn-lg">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>

<?php include("../../templates/footer_content.php") ?>