<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
    $lista_categoria_link  = "lista_categoria.php";
    
}else{
    $lista_categoria_link  = "lista_categoria.php?link=".$link;
}
if(isset($_GET['link'])){
   $link=(isset($_GET['link']))?$_GET['link']:"";
}
if ($_POST) {
    $nombre_categoria = isset($_POST['nueva_categoria']) ? $_POST['nueva_categoria'] : "";
    $idResponsable = isset($_POST['idResponsable']) ? $_POST['idResponsable'] : "";
    $link =  isset($_POST['link']) ? $_POST['link'] : "";
    if ($idResponsable == 1) {
      $link = "sudo_admin";
    }

    $sentencia = $conexion->prepare("INSERT INTO categoria(categoria_id, categoria_nombre,categoria_fecha_creacion, link, responsable) VALUES (null, :nueva_categoria,:categoria_fecha_creacion, :link, :responsable)");
    $sentencia->bindParam(":nueva_categoria", $nombre_categoria);
    $sentencia->bindParam(":categoria_fecha_creacion", $fechaActual);
    $sentencia->bindParam(":link", $link);
    $sentencia->bindParam(":responsable", $idResponsable);
    $resultado = $sentencia->execute();

    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Categoría creada Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$lista_categoria_link.'";
            }
        });
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear Categoria",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>
    <br>
    <div class="card card-primary" style="margin-top:7%">
        <div class="card-header">
            <h2 class="card-title textTabla">REGISTRE NUEVA CATEGORÍA  &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $lista_categoria_link;?>">Lista Categorías</a></h2>
        </div>
        <form action="" method="POST" id="formCategoria">
            <input type="hidden" name="link" value="<?php echo $link ?>">
            <div class="card-body ">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3" style="justify-content:center">
                        <div class="form-group">
                            <input type="text" class="form-control camposTabla" name="nueva_categoria" required >
                            <input type="hidden" value="<?php echo $_SESSION['usuario_id'] ?>" name="idResponsable">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer" style="text-align:center">
                <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
                <a role="button"  href="<?php echo $url_base;?>secciones/<?php echo $lista_categoria_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
<?php include("../templates/footer.php") ?>