<?php include("../templates/header.php") ?>
<?php 

    // Validanco existencia de bodega 
    $sentencia=$conexion->prepare("SELECT * FROM empresa_bodega");
    $sentencia->execute();
    $empresa_bodega=$sentencia->fetchAll(PDO::FETCH_ASSOC);
    $val_bodega = false;
    if ($empresa_bodega) { $val_bodega = true;}

    if ($_POST) {

      $bodega_nit	= isset($_POST['bodega_nit']) ? $_POST['bodega_nit'] : "";
      $bodega_nombre = isset($_POST['bodega_nombre']) ? $_POST['bodega_nombre'] : "";
      $clave_user = isset($_POST['bodega_nombre']) ? str_replace(" ", '', $_POST['bodega_nombre']) : "";
      $bodega_telefono = isset($_POST['bodega_telefono']) ? $_POST['bodega_telefono'] : "";
      $bodega_email = isset($_POST['bodega_email']) ? $_POST['bodega_email'] : "";
      $bodega_direccion = isset($_POST['bodega_direccion']) ? $_POST['bodega_direccion'] : "";
      $responsable = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;
      $codigo_seguridad = isset($_POST['codigo_seguridad']) ? $_POST['codigo_seguridad'] : " ";
      //link_empresa 
      $link_empresa = "sudo_bodega";
      if (isset($_FILES["bodega_logo"])) {
        $nombre = $_FILES["bodega_logo"]["name"];
        $ruta_temporal = $_FILES["bodega_logo"]["tmp_name"];
        $ruta_destino = "../dist/img/logo_bodega/" . uniqid() . "_" . $nombre;
        
      }
      // Verificar si el archivo es una imagen
      if (exif_imagetype($ruta_temporal) !== false) {
        // Mover la imagen a la carpeta de destino
        if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
            $ruta_destino = $ruta_destino;
        }
      }
  
      //  GUARDANDO LA EMPRSA CREADA
      $sql = "INSERT INTO empresa_bodega (bodega_id,bodega_nombre, bodega_telefono, bodega_email,
      bodega_direccion, bodega_nit, bodega_logo, link, codigo_seguridad , responsable) 
      VALUES (?,?,?,?,?,?,?,?,?,?)";
      $sentencia = $conexion->prepare($sql);
      $params = array(
        '9000',
          $bodega_nombre,
          $bodega_telefono,
          $bodega_email,
          $bodega_direccion,
          $bodega_nit,
          $ruta_destino,
          $link_empresa,
          $codigo_seguridad,
          $responsable
      );
      $sentencia->execute($params);
  
      // GUARDAR USUARIO DE LA BODEGA
      $sql = "INSERT INTO usuario (usuario_nombre, usuario_apellido, usuario_telefono, usuario_cedula, usuario_email, usuario_usuario,
      usuario_clave, rol, usuario_foto, caja_id, link ,responsable) 
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
      $sentencia_usuario = $conexion->prepare($sql);
      $params = array(
          "Admin_Bodega ".$bodega_nombre,
          "N/A",
          "N/A",
          "N/A",
          "N/A",
          $bodega_nombre,
          hash('sha256', $clave_user),
          "3",
          "N/A",
          "0",
          $link_empresa,
          $responsable
      );
      $resultado = $sentencia_usuario->execute($params);
      if ($resultado) {
          echo '<script>
           Swal.fire({
            title: "Bodega Creada Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href="'.$url_base.'secciones/bodega.php"
            }
        })
          </script>';
      } else {
          echo '<script>
          Swal.fire({
              title: "Error al Crear la Empresa ",
              icon: "error",
              confirmButtonText: "¡Entendido!"
          });
          </script>';
      }
      
  }

        
?>
<style>
  .empresa {
    transition: transform 0.5s ease;
}

.empresa:hover {
    transform: scale(0.9); 
}
</style> 
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
          <img class="animation__shake" src="../dist/img/logos/logo_nube.png" alt="AdminLTELogo" height="60" width="80">
        </div>
        <br>
        <div class="row" style="justify-content: center;">
            <div class="col-3">
                <h3 style="display: inline-block;"><strong>Bienvenido a Bodega</strong></h3>
                <span style="display: inline-block; vertical-align: middle; margin-left: 10px;"><img src="../dist/img/bodega.png" alt="" style="width: 72px;"></span>
            </div>
        </div>
        <br>
        <br>
        <?php if(!$val_bodega) {?>
          <form action=" " method="post" enctype="multipart/form-data" id="crearBodega">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Nit </label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="bodega_nit" required >
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="producto_nombre" class="textLabel">Nombre</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="bodega_nombre" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Teléfono</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="bodega_telefono" required >
                        </div>
                    </div>
                </div>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Dirección</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla" name="bodega_direccion" required >
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Logo</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="file" id="logoBodega" class="form-control camposTabla" name="bodega_logo" accept="image/*" required>
                        </div>
                    </div>
                    
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Clave para Permisos</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla"name="codigo_seguridad" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="text-align:center">
                <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
            </div>
        </form>     
        <?php } else { ?>
          <div class="row" style="justify-content: center;">
            <div class="col-2">
              <a href="<?php echo $url_base;?>secciones/<?php echo $crear_producto_link?>"> <button type="button" class="btn btn-block btn-outline-primary btn-lg" ><i class="nav-icon fas fa-shopping-basket fa-lg mr-2"></i>Crear Producto</button></a>
            </div>
            <div class="col-2 ">
             <a href="<?php echo $url_base;?>secciones/<?php echo $producto_bodega_link?>"> <button type="button" class="btn btn-block btn-outline-warning btn-lg" > <i class="nav-icon fa fa-tasks fa-lg mr-2"></i>Trasladar Producto</button></a>
            </div>
            <!-- <div class="col-2">
             <a href="<?php echo $url_base;?>secciones/<?php echo $crear_venta_bodega?>"> <button type="button" class="btn btn-block btn-outline-success btn-lg" ><i class="nav-icon fas fa-cart-plus fa-lg mr-2"></i> Comprar</button></a>
            </div> -->
            <div class="col-2">
             <a href="<?php echo $url_base;?>secciones/<?php echo $crear_venta_bodega?>"> <button type="button" class="btn btn-block btn-outline-success btn-lg" ><i class="nav-icon fas fa-cart-plus fa-lg mr-2"></i> Vender</button></a>
            </div>
            <div class="col-1">
             <a href="<?php echo $url_base;?>secciones/<?php echo $venta_bodega?>"> <button type="button" class="btn btn-block btn-outline-success btn-lg" >➡️Ventas</button></a>
            </div>
          </div>
        <?php } ?>
<?php include("../templates/footer.php") ?>