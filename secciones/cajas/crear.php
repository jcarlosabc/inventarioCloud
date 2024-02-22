<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");
if ($_POST) {
    $caja_numero = isset($_POST['caja_numero']) ? $_POST['caja_numero'] : "";
    $caja_nombre = isset($_POST['caja_nombre']) ? $_POST['caja_nombre'] : "";
    $caja_efectivo = isset($_POST['caja_efectivo']) ? $_POST['caja_efectivo'] : "";
    
    $sentencia = $conexion->prepare("INSERT INTO caja(
        caja_numero, 
        caja_nombre,
        caja_efectivo
        ) VALUES (:caja_numero, :caja_nombre,:caja_efectivo)");
    
    $sentencia->bindParam(":caja_numero", $caja_numero);
    $sentencia->bindParam(":caja_nombre", $caja_nombre);
    $sentencia->bindParam(":caja_efectivo", $caja_efectivo);
    
    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Caja Creada Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear nueva Caja",
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
                    <h3 class="card-title">REGISTRE UNA NUEVA CAJA</h3>
                </div>
              <!-- /.card-header -->
              <!-- form start --> 
                <form action="" method="POST" id="formCaja">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="numeroCaja">Numero de la Caja</label> 
                                    <input type="text" class="form-control" name="caja_numero" required id="caja_numero">
                                </div>                               
                             </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                 <label for="nombreCaja">Nombre de la Caja</label> 
                                <input type="text" class="form-control" name="caja_nombre" required id="caja_nombre">
                            </div>                                
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                 <label for="efectivoCaja">Efectivo de la Caja</label> 
                                <input type="text" class="form-control" name="caja_efectivo" required id="caja_efectivo">
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