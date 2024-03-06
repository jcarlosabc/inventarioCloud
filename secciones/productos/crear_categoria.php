<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");
$fechaActual = date("d/m/Y");

if ($_POST) {
    $nombre_categoria = isset($_POST['nueva_categoria']) ? $_POST['nueva_categoria'] : "";
    $idResponsable = isset($_POST['idResponsable']) ? $_POST['idResponsable'] : "";

    $sentencia = $conexion->prepare("INSERT INTO categoria(categoria_id, categoria_nombre,categoria_fecha_creacion, responsable) VALUES (null, :nueva_categoria,:categoria_fecha_creacion,:responsable)");
    $sentencia->bindParam(":nueva_categoria", $nombre_categoria);
    $sentencia->bindParam(":categoria_fecha_creacion", $fechaActual);
    $sentencia->bindParam(":responsable", $idResponsable);
    
    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Categoria creada Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventariocloud/secciones/productos/lista_categoria.php";
            }
        })
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
          <!-- left column -->
          <div class="">
            <!-- general form elements -->
            <div class="card card-primary" style="margin-top:7%">
                <div class="card-header">
                    <h3 class="card-title textTabla">REGISTRE NUEVA CATEGORIA</h3>
                </div>
              <!-- /.card-header -->
              <!-- form start --> 
                <form action="" method="POST" id="formCategoria">
                    <div class="card-body ">
                        <div class="row" style="justify-content:center">
                            <div class="col-sm-4" style="justify-content:center">
                                <div class="form-group">
                                    <!-- <label for="nuevaCategoria" class="textLabel">Nueva Categoria</label>  -->
                                    <input type="text" class="form-control camposTabla" name="nueva_categoria" required id="nuevaCategoria">
                                    <input type="hidden" value="<?php echo $_SESSION['usuario_id'] ?>" name="idResponsable">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer" style="text-align:center">
                        <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
                        <a role="button"  href="index.php" class="btn btn-danger btn-lg">Cancelar</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
          </div>


<?php include("../../templates/footer_content.php") ?>