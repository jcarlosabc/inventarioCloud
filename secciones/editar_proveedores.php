<?php include("../templates/header.php") ?>
<?php

if ($_SESSION['valSudoAdmin']) {
    $lista_proveedor_link  = "index_proveedores.php";
  
 }else{
    $lista_proveedor_link  = "index_proveedores.php?link=".$link;
 }

if (isset($_GET['txtID'])) {

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
   
    // Obtener la categoría actual del producto
    $datos_proveedores = $conexion->prepare("SELECT * FROM proveedores WHERE id_proveedores = :id_proveedores");

    $datos_proveedores->bindParam(":id_proveedores", $txtID);
    $datos_proveedores->execute();
    $registro = $datos_proveedores->fetch(PDO::FETCH_ASSOC);

    $nit_proveedores = $registro['nit_proveedores'];
    $nombre_proveedores = $registro['nombre_proveedores'];
    $email_proveedores = $registro['email_proveedores'];
    $telefono_proveedores = $registro['telefono_proveedores'];
    $direccion_proveedores = $registro['direccion_proveedores'];
    $link_proveedores = $registro['link'];

    if ($_POST) {
        $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
        $nit_proveedore = isset($_POST['nit_proveedores']) ? $_POST['nit_proveedores'] : " ";
        $nombre_proveedore = isset($_POST['nombre_proveedores']) ? $_POST['nombre_proveedores'] : " ";
        $email_proveedores = isset($_POST['email_proveedores']) ? $_POST['email_proveedores'] : " ";
        $telefono_proveedores = isset($_POST['telefono_proveedores']) ? $_POST['telefono_proveedores'] : " ";
        $direccion_proveedores = isset($_POST['direccion_proveedores']) ? $_POST['direccion_proveedores'] : " ";
        $link = isset($_POST['link']) ? $_POST['link'] : " ";

        $sentencia_edit = $conexion->prepare("UPDATE proveedores SET 
        nit_proveedores=:nit_proveedores, nombre_proveedores=:nombre_proveedores, email_proveedores=:email_proveedores,
        telefono_proveedores=:telefono_proveedores, direccion_proveedores=:direccion_proveedores, link=:link WHERE id_proveedores=:id_proveedores");

        $sentencia_edit->bindParam(":id_proveedores", $txtID);
        $sentencia_edit->bindParam(":nit_proveedores", $nit_proveedore);
        $sentencia_edit->bindParam(":nombre_proveedores", $nombre_proveedore);
        $sentencia_edit->bindParam(":email_proveedores", $email_proveedores);
        $sentencia_edit->bindParam(":telefono_proveedores", $telefono_proveedores);
        $sentencia_edit->bindParam(":direccion_proveedores", $direccion_proveedores);
        $sentencia_edit->bindParam(":link", $link);
        $resultado_edit = $sentencia_edit->execute();

        if ($resultado_edit) {
            echo '<script>
            Swal.fire({
                title: "¡Proveedor creado Exitosamente!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href = "'.$url_base.'secciones/'.$lista_proveedore_link.'";
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
    <div class="card card-warning" style="margin-top:7%">
        <div class="card-header">
            <h2 class="card-title textTabla">EDITAR PROVEEDORES</h2>
        </div>
        <form action=" " method="post">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                        <input type="hidden" name="txtID" value="<?= $txtID ?>">
                        <input type="hidden" name="link" value="<?= $link_proveedores ?>">
                            <label for="producto_nombre" class="textLabel">Nit</label>
                            <input type="text" class="form-control camposTabla" name="nit_proveedores" value="<?=$nit_proveedores?>">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Nombre </label>
                            <input type="text" class="form-control camposTabla" name="nombre_proveedores" value="<?=$nombre_proveedores?>">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Teléfono </label> 
                            <input type="text" class="form-control camposTabla" name="telefono_proveedores" value="<?=$telefono_proveedores?>">
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Correo</label>
                            <input type="email" class="form-control camposTabla" name="email_proveedores" value="<?=$email_proveedores?>">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Dirección </label> 
                            <input type="text" class="form-control camposTabla" name="direccion_proveedores" value="<?=$direccion_proveedores?>">
                            <div id="mensaje" class="text-danger"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
                    <a role="button" href="<?php echo $url_base; ?>secciones/<?php echo $lista_proveedor_link; ?>" class="btn btn-danger btn-lg">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
    <?php include("../templates/footer.php") ?>