<?php include("../templates/header.php") ?>
<?php
if ($_SESSION['valSudoAdmin']) {
    $lista_cliente_link  = "index_clientes.php";
  
 }else{
    $lista_cliente_link  = "index_clientes.php?link=".$link;
 }
 
 if(isset($_GET['link'])){
    $link=(isset($_GET['link']))?$_GET['link']:"";
 }

if ($_POST) {
    
    $cliente_id = isset($_POST['cliente_id']) ? $_POST['cliente_id'] : "";
    $cliente_numero_documento = isset($_POST['cliente_numero_documento']) ? $_POST['cliente_numero_documento'] : "";
    $cliente_nombre = isset($_POST['cliente_nombre']) ? $_POST['cliente_nombre'] : "";
    $cliente_apellido = isset($_POST['cliente_apellido']) ? $_POST['cliente_apellido'] : "";
    $cliente_ciudad = isset($_POST['cliente_ciudad']) ? $_POST['cliente_ciudad'] : "";
    $cliente_direccion = isset($_POST['cliente_direccion']) ? $_POST['cliente_direccion'] : "";
    $cliente_telefono = isset($_POST['cliente_telefono']) ? $_POST['cliente_telefono'] : "";
    $cliente_email = isset($_POST['cliente_email']) ? $_POST['cliente_email'] : "";
    $cliente_empresa = isset($_POST['cliente_empresa']) ? $_POST['cliente_empresa'] : "";
    $cliente_nit = isset($_POST['cliente_nit']) ? $_POST['cliente_nit'] : "";
    $responsable = $_SESSION['usuario_id'];
    $link =  isset($_POST['link']) ? $_POST['link'] : "";
    if ($responsable == 1) {
        $link = "sudo_admin";
    }

    if ($_SESSION['rolBodega']) {
        $sentencia = $conexion->prepare("INSERT INTO cliente(
            cliente_id,
            cliente_numero_documento, 
            cliente_nombre,
            cliente_apellido,
            cliente_ciudad,
            cliente_direccion,
            cliente_telefono,
            cliente_email,
            cliente_empresa,
            cliente_nit,
            link,
            responsable) 
            VALUES (NULL,:cliente_numero_documento, :cliente_nombre,:cliente_apellido,:cliente_ciudad, :cliente_direccion,:cliente_telefono,:cliente_email,:cliente_empresa,:cliente_nit, :link,:responsable)");
        
        $sentencia->bindParam(":cliente_numero_documento", $cliente_numero_documento);
        $sentencia->bindParam(":cliente_nombre", $cliente_nombre);
        $sentencia->bindParam(":cliente_apellido", $cliente_apellido);
        $sentencia->bindParam(":cliente_ciudad", $cliente_ciudad);
        $sentencia->bindParam(":cliente_direccion", $cliente_direccion);
        $sentencia->bindParam(":cliente_telefono", $cliente_telefono);
        $sentencia->bindParam(":cliente_email", $cliente_email);
        $sentencia->bindParam(":cliente_empresa", $cliente_empresa);
        $sentencia->bindParam(":cliente_nit", $cliente_nit);
        $sentencia->bindParam(":link", $link);
        $sentencia->bindParam(":responsable",$responsable);
        
    }else{
        $sentencia = $conexion->prepare("INSERT INTO cliente(
            cliente_id,
            cliente_numero_documento, 
            cliente_nombre,
            cliente_apellido,
            cliente_ciudad,
            cliente_direccion,
            cliente_telefono,
            cliente_email,
            link,
            responsable) 
            VALUES (NULL,:cliente_numero_documento, :cliente_nombre,:cliente_apellido,:cliente_ciudad, :cliente_direccion,:cliente_telefono,:cliente_email, :link,:responsable)");
        
        $sentencia->bindParam(":cliente_numero_documento", $cliente_numero_documento);
        $sentencia->bindParam(":cliente_nombre", $cliente_nombre);
        $sentencia->bindParam(":cliente_apellido", $cliente_apellido);
        $sentencia->bindParam(":cliente_ciudad", $cliente_ciudad);
        $sentencia->bindParam(":cliente_direccion", $cliente_direccion);
        $sentencia->bindParam(":cliente_telefono", $cliente_telefono);
        $sentencia->bindParam(":cliente_email", $cliente_email);
        $sentencia->bindParam(":link", $link);
        $sentencia->bindParam(":responsable",$responsable);

    }
        
    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "¡Cliente Creado Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$lista_cliente_link.'";
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
        <div class="card card-primary" style="margin-top:7%">
            <div class="card-header">
                <h3 class="card-title textTabla" >REGISTRE NUEVO CLIENTE &nbsp;&nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $lista_cliente_link;?>" role="button">Lista de Clientes</a></h3>
            </div>
              <!-- form start --> 
            <form action="" method="post" enctype="multipart/form-data">
                <br>
                <div class="card-body ">
                    <div class="row" style="justify-content:center">
                        <?php if ($_SESSION['rolBodega']) { ?>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="cliente_empresa" class="textLabel">Empresa</label>&nbsp;<i class="nav-icon fas fa-edit"></i> 
                                    <input required type="text" class="form-control camposTabla"  name="cliente_empresa" id="cliente_empresa">
                                </div>
                            </div>                          
                       <?php } ?>
                       <?php if ($_SESSION['rolBodega']) { ?>                            
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="cliente_nit" class="textLabel">Nit</label>
                                    <input type="text" class="form-control camposTabla"  name="cliente_nit" id="cliente_nit">
                                </div>
                            </div>                            
                       <?php } ?>   
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label  for="cliente_numero_documento" class="textLabel">Cédula</label> 
                                <input type="num" class="form-control camposTabla" name="cliente_numero_documento">
                                <input type="hidden" name="link" value="<?php echo $link ?>">

                            </div>                       
                        </div> 
                        <div class="col-2">
                            <div class="form-group">
                                <label for="cliente_nombre" class="textLabel">Nombres</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="cliente_nombre" required>
                            </div>
                        </div>                     
                        <div class="col-2">
                            <div class="form-group">
                                <label for="cliente_apellido" class="textLabel">Apellidos</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="cliente_apellido" required>
                            </div>
                        </div>                        
                    </div> 
                    <div class="row" style="justify-content:center">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="cliente_telefono" class="textLabel">Teléfono</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control camposTabla" name="cliente_telefono" id="cliente_telefono">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label for="cliente_ciudad" class="textLabel">Ciudad</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla"  name="cliente_ciudad" id="cliente_ciudad">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="cliente_direccion" class="textLabel">Dirección</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" name="cliente_direccion" id="cliente_direccion">
                            </div>
                        </div>                        
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="cliente_email" class="textLabel">Correo</label>
                                <input type="text" class="form-control camposTabla"  name="cliente_email" id="cliente_email">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                  <button type="submit" class="btn btn-primary btn-lg">Guardar</button>
                  <a role="button"  href="<?php echo $url_base;?>secciones/<?php echo $lista_cliente_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
                </div>
            </form>
        </div>
<?php include("../templates/footer.php") ?>