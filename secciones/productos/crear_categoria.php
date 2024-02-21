<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");
if ($_POST) {
    $nombre_categoria = isset($_POST['nueva_categoria']) ? $_POST['nueva_categoria'] : "";
    
    $sentencia = $conexion->prepare("INSERT INTO categoria(categoria_id, categoria_nombre) VALUES (null, :nueva_categoria)");
    $sentencia->bindParam(":nueva_categoria", $nombre_categoria);
    
    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Categoria Creada Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
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

          <!-- left column -->
          <div class="">
            <!-- general form elements -->
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">REGISTRE NUEVA CATEGORIA</h3>
                </div>
              <!-- /.card-header -->
              <!-- form start --> 
                <form action="" method="POST" id="formCategoria">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="nueva_categoria">Nueva Categoria</label> 
                                    <input type="text" class="form-control" name="nueva_categoria" required id="nuevaCategoria">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer" style="text-align:center">
                        <button type="submit"  class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
          </div>


<?php include("../../templates/footer_content.php") ?>