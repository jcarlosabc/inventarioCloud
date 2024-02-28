<?php include("../../templates/header_content.php") ?>

<?php
include("../../db.php");
if ($_POST) {
    
    $cliente_id = isset($_POST['cliente_id']) ? $_POST['cliente_id'] : "";
    $cliente_numero_documento = isset($_POST['cliente_numero_documento']) ? $_POST['cliente_numero_documento'] : "";
    $cliente_nombre = isset($_POST['cliente_nombre']) ? $_POST['cliente_nombre'] : "";
    $cliente_apellido = isset($_POST['cliente_apellido']) ? $_POST['cliente_apellido'] : "";
    $cliente_ciudad = isset($_POST['cliente_ciudad']) ? $_POST['cliente_ciudad'] : "";
    $cliente_provincia = isset($_POST['cliente_provincia']) ? $_POST['cliente_provincia'] : "";
    $cliente_direccion = isset($_POST['cliente_direccion']) ? $_POST['cliente_direccion'] : "";
    $cliente_telefono = isset($_POST['cliente_telefono']) ? $_POST['cliente_telefono'] : "";
    $cliente_email = isset($_POST['cliente_email']) ? $_POST['cliente_email'] : "";
    $responsable = $_SESSION['usuario_id'];
    
    
    
    $sentencia = $conexion->prepare("INSERT INTO cliente(
        cliente_id,
        cliente_numero_documento, 
        cliente_nombre,
        cliente_apellido,
        cliente_ciudad,
        cliente_provincia,
        cliente_direccion,
        cliente_telefono,
        cliente_email,
        responsable) 
        VALUES (NULL,:cliente_numero_documento, :cliente_nombre,:cliente_apellido,:cliente_ciudad,:cliente_provincia,:cliente_direccion,:cliente_telefono,:cliente_email,:responsable)");
    
    $sentencia->bindParam(":cliente_numero_documento", $cliente_numero_documento);
    $sentencia->bindParam(":cliente_nombre", $cliente_nombre);
    $sentencia->bindParam(":cliente_apellido", $cliente_apellido);
    $sentencia->bindParam(":cliente_ciudad", $cliente_ciudad);
    $sentencia->bindParam(":cliente_provincia", $cliente_provincia);
    $sentencia->bindParam(":cliente_direccion", $cliente_direccion);
    $sentencia->bindParam(":cliente_telefono", $cliente_telefono);
    $sentencia->bindParam(":cliente_email", $cliente_email);
    $sentencia->bindParam(":responsable",$responsable);
        
    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "¡Cliente Creado Exitosamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href="http://localhost/inventariocloud/secciones/clientes/"
            }
        })

        </script>';
        
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear Cliente",
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
                <h3 class="card-title textTabla" >REGISTRE NUEVO CLIENTE &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="index.php" role="button">Lista de Clientes</a></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form action="" method="post" enctype="multipart/form-data">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label  for="cliente_numero_documento" class="textLabel">Cedula de Ciudadania</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input required type="num" class="form-control camposTabla" name="cliente_numero_documento" id="cliente_numero_documento">
                            </div>                       
                        </div> 
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="cliente_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="cliente_nombre" id="cliente_nombre">
                            </div>
                        </div>                     
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="cliente_apellido" class="textLabel">Apellidos</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="cliente_apellido" id="cliente_apellido">
                            </div>
                        </div>                        
                    
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="cliente_ciudad" class="textLabel">Ciudad</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla"  name="cliente_ciudad" id="cliente_ciudad">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="cliente_provincia" class="textLabel">Providencia</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla"  name="cliente_provincia" id="cliente_provincia">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="cliente_direccion" class="textLabel">Direccion</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="cliente_direccion" id="cliente_direccion">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="cliente_telefono" class="textLabel">Telefono</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control camposTabla" name="cliente_telefono" id="cliente_telefono">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="cliente_email" class="textLabel">Correo Electronico</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla"  name="cliente_email" id="cliente_email">
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                  <button type="submit" class="btn btn-primary">Guardar</button>
                  <a role="button"  href="index.php" class="btn btn-danger">Cancelar</a>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>


<?php include("../../templates/footer_content.php") ?>