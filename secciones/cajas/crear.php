<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");
if ($_POST) {
    $caja_numero = isset($_POST['caja_numero']) ? $_POST['caja_numero'] : "";
    $caja_nombre = isset($_POST['caja_nombre']) ? $_POST['caja_nombre'] : "";
    $caja_efectivo = isset($_POST['caja_efectivo']) ? $_POST['caja_efectivo'] : "";  
    $responsable = $_SESSION['usuario_id']; 
            
    $sentencia = $conexion->prepare("INSERT INTO caja(
        caja_numero, 
        caja_nombre,
        caja_efectivo,
        responsable
        ) VALUES (:caja_numero, :caja_nombre,:caja_efectivo,:responsable)");
    
    $sentencia->bindParam(":caja_numero", $caja_numero);
    $sentencia->bindParam(":caja_nombre", $caja_nombre);
    $sentencia->bindParam(":caja_efectivo", $caja_efectivo);
    $sentencia->bindParam(":responsable",$responsable);
    
    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Caja Creada Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventariocloud/secciones/cajas/";
            }
        })
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
            <div class="card card-primary" style="margin-top:7%">
                <div class="card-header">
                    <h3 class="card-title textTabla">REGISTRE UNA NUEVA CAJA &nbsp;&nbsp;<a class="btn btn-warning"  style="color:black" href="index.php" role="button">Lista de Caja</a></h3>
                </div>
              <!-- /.card-header -->
              <!-- form start --> 
                <form action="" method="POST" id="formCaja">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="caja_numero" class="textLabel">Código</label> 
                                    <input type="text" class="form-control camposTabla" name="caja_numero" required id="caja_numero">
                                </div>                               
                             </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                 <label for="caja_nombre" class="textLabel">Nombre</label> 
                                <input type="text" class="form-control camposTabla" name="caja_nombre" required id="caja_nombre">
                            </div>                                
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                 <label for="caja_efectivo" class="textLabel">Efectivo</label> 
                                <input type="text" class="form-control camposTabla" name="caja_efectivo" required id="caja_efectivo">
                            </div>                                
                        </div>

                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer" style="text-align:center">
                        <button type="submit"  class="btn btn-primary btn-lg">Guardar</button>
                        <a role="button"  href="index.php" class="btn btn-danger btn-lg">Cancelar</a>

                    </div>
                </form>
            </div>
            <!-- /.card -->
          </div>









<?php include("../../templates/footer_content.php") ?>