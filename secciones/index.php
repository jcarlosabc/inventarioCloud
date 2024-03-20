<?php include("../templates/header.php") ?>
<?php 
    $sentencia=$conexion->prepare("SELECT usuario_usuario, usuario_clave, empresa.* FROM usuario 
    INNER JOIN empresa ON empresa.link = usuario.link");
    $sentencia->execute();
    $empresa_creadas=$sentencia->fetchAll(PDO::FETCH_ASSOC);
    
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
        <div class="row">
          <div class="col-3">
           <a href="crear_empresa.php"> <button type="button" class="btn btn-block btn-outline-primary btn-lg" >Crear Negocio</button></a>
          </div>
        </div>
        <div class="row mt-4">
          <?php foreach ($empresa_creadas as $registro) {?>
            <div class="col-sm-4 empresa">
                <a href="../login.php?link=<?php echo $registro['link'] ?>" target="_blank" onclick="cerrarSesion()">
                    <div class="position-relative p-3" style="height: 180px; background: #161aade0;color:#fff;border-radius: 30px;font-size: 20px;">
                        <div class="ribbon-wrapper ribbon-xl">
                            <div class="ribbon bg-warning text-lg">
                                <strong><?php echo $registro['empresa_nombre']; ?></strong>
                            </div>
                        </div>
                        <strong>Código del Local: </strong> <?php echo $registro['link'] ?><br>
                        <small>Teléfono: <?php echo $registro['empresa_telefono']; ?></small><br>
                        <small>Dirección: <?php echo $registro['empresa_direccion']; ?></small><br>
                        <small>Código de Permisos: <?php echo $registro['codigo_seguridad']; ?></small><br>
                        Usuario: <?php echo $registro['usuario_usuario']; ?>--
                        <span>Contraseña: Es el mismo usuario</span>
                    </div>
                </a>
                <br>
            </div>
          <?php } ?>
        </div>
<?php include("../templates/footer.php") ?>