<?php include("../templates/header.php") ?>
<?php 
    // $sentencia=$conexion->prepare("SELECT usuario_usuario, usuario_clave, empresa.* FROM usuario 
    // INNER JOIN empresa ON empresa.link = usuario.link");
    // $sentencia->execute();
    // $empresa_creadas=$sentencia->fetchAll(PDO::FETCH_ASSOC);
    
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
        <div class="row" style="justify-content: center;">
          <div class="col-3">
           <a href="crear_producto.php"> <button type="button" class="btn btn-block btn-outline-primary btn-lg" >Crear Producto</button></a>
          </div>
          <div class="col-3">
           <a href="producto_bodega.php"> <button type="button" class="btn btn-block btn-outline-primary btn-lg" >Crear Negocio</button></a>
          </div>
          <div class="col-3">
           <a href="crear_empresa.php"> <button type="button" class="btn btn-block btn-outline-primary btn-lg" >Crear Negocio</button></a>
          </div>
        </div>
<?php include("../templates/footer.php") ?>