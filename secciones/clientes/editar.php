<?php include("../../templates/header_content.php") ?>
<?php
include("../../db.php");

if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    $sentencia=$conexion->prepare("SELECT * FROM cliente WHERE cliente_id=:cliente_id AND cliente_id > 0");
    $sentencia->bindParam(":cliente_id",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $cliente_id=$registro["cliente_id"];
    $cliente_numero_documento=$registro["cliente_numero_documento"];
    $cliente_nombre=$registro["cliente_nombre"];
    $cliente_apellido=$registro["cliente_apellido"];
    $cliente_ciudad=$registro["cliente_ciudad"];
    $cliente_direccion=$registro["cliente_direccion"];
    $cliente_telefono=$registro["cliente_telefono"];
    $cliente_email=$registro["cliente_email"];
}

if ($_POST) {
    $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
    $cliente_numero_documento= isset($_POST['cliente_numero_documento']) ? $_POST['cliente_numero_documento'] : "";
    $cliente_nombre= isset($_POST['cliente_nombre']) ? $_POST['cliente_nombre'] : "";
    $cliente_apellido= isset($_POST['cliente_apellido']) ? $_POST['cliente_apellido'] : "";
    $cliente_ciudad= isset($_POST['cliente_ciudad']) ? $_POST['cliente_ciudad'] : "";
    $cliente_direccion= isset($_POST['cliente_direccion']) ? $_POST['cliente_direccion'] : "";
    $cliente_telefono= isset($_POST['cliente_telefono']) ? $_POST['cliente_telefono'] : "";
    $cliente_email= isset($_POST['cliente_email']) ? $_POST['cliente_email'] : "";
    
    $sentencia_edit = $conexion->prepare("UPDATE cliente SET 
    cliente_numero_documento=:cliente_numero_documento,
    cliente_nombre=:cliente_nombre,
    cliente_apellido=:cliente_apellido,
    cliente_ciudad=:cliente_ciudad,
    cliente_direccion=:cliente_direccion,
    cliente_telefono=:cliente_telefono,
    cliente_email=:cliente_email         
    WHERE cliente_id =:cliente_id");
    
    $sentencia_edit->bindParam(":cliente_id",$txtID);
    $sentencia_edit->bindParam(":cliente_numero_documento",$cliente_numero_documento);
    $sentencia_edit->bindParam(":cliente_nombre",$cliente_nombre);
    $sentencia_edit->bindParam(":cliente_apellido",$cliente_apellido);
    $sentencia_edit->bindParam(":cliente_ciudad",$cliente_ciudad);
    $sentencia_edit->bindParam(":cliente_direccion",$cliente_direccion);
    $sentencia_edit->bindParam(":cliente_telefono",$cliente_telefono);
    $sentencia_edit->bindParam(":cliente_email",$cliente_email);
    $resultado_edit = $sentencia_edit->execute();

    if ($resultado_edit) {
        echo '<script>
        Swal.fire({
            title: "¡Cliente Actualizado Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventariocloud/secciones/clientes/";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Actualizar el Cliente",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>
        <br>
        <div class="card card-warning" style="margin-top:7%">
            <div class="card-header">
                <h3 class="card-title textTabla" >EDITAR CLIENTE</h3>
            </div>
              <!-- /.card-header -->
              <!-- form start --> 
            <form action="" method="post" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="hidden" class="textLabel" name="txtID" id="txtID" value="<?php echo $cliente_id;?>" >
                                <label  for="cliente_numero_documento" class="textLabel">Cédula</label>
                                <input required type="num" class="form-control camposTabla" name="cliente_numero_documento" value="<?php echo $cliente_numero_documento;?>">
                            </div>                       
                        </div> 
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Nombres</label>
                                <input type="text" class="form-control camposTabla" name="cliente_nombre" value="<?php echo $cliente_nombre;?>">
                            </div>
                        </div>                     
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Apellidos</label>
                                <input type="text" class="form-control camposTabla" name="cliente_apellido" value="<?php echo $cliente_apellido;?>">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="cliente_telefono" class="textLabel">Teléfono</label>
                                <input type="num" class="form-control camposTabla" name="cliente_telefono" value="<?php echo $cliente_telefono;?>">
                            </div>
                        </div>                        
                    </div>                        
                    
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="textLabel">Ciudad</label>
                                <input type="text" class="form-control camposTabla"  name="cliente_ciudad" value="<?php echo $cliente_ciudad;?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="textLabel">Dirección</label>
                                <input type="text" class="form-control camposTabla" name="cliente_direccion" value="<?php echo $cliente_direccion;?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="textLabel">Correo</label>
                                <input type="text" class="form-control camposTabla" name="cliente_email" value="<?php echo $cliente_email;?>">
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

<?php include("../../templates/footer_content.php") ?>