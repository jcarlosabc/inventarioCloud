<?php include("../templates/header.php") ?>
<?php 
    // Mostrando las empresas creadas
    $sentencia=$conexion->prepare("SELECT usuario_usuario, usuario_clave, info_clave, empresa.* FROM usuario 
    JOIN empresa ON empresa.link = usuario.link GROUP BY empresa_id");
    $sentencia->execute();
    $empresa_creadas=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Mostrando la bodega creada
    $sentencia=$conexion->prepare("SELECT usuario_usuario, usuario_clave, info_clave, empresa_bodega.* FROM usuario 
    JOIN empresa_bodega ON empresa_bodega.link = empresa_bodega.link");
    $sentencia->execute();
    $bodega_creada = $sentencia->fetch(PDO::FETCH_LAZY);
    $usuario_usuario = $bodega_creada['usuario_usuario'];
    $usuario_clave = $bodega_creada['usuario_clave'];
    $info_clave = $bodega_creada['info_clave'];
    $link = $bodega_creada['link'];
    $bodega_telefono = $bodega_creada['bodega_telefono'];
    $bodega_direccion = $bodega_creada['bodega_direccion'];
    $codigo_seguridad = $bodega_creada['codigo_seguridad'];
    $bodega_nombre = $bodega_creada['bodega_nombre'];
    
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
           <a href="crear_empresa.php"> <button type="button" class="btn btn-block btn-outline-primary btn-lg">Crear Negocio</button></a>
          </div>
        </div>
        <div class="row mt-4">
          <?php if($bodega_creada) {?>
            <div class="col-sm-4 empresa">
                <a href="../login.php?link=<?php echo $link ?>" target="_blank" onclick="cerrarSesion()">
                    <div class="position-relative p-3" style="height: 180px; background: #3a3b45e0;color:#fff;border-radius: 30px;font-size: 20px;">
                        <div class="ribbon-wrapper ribbon-xl">
                            <div class="ribbon bg-warning text-lg">
                                <strong style="font-size: 14px;"><?php echo $bodega_nombre; ?></strong>
                            </div>
                        </div>
                        <strong>Código del Local: </strong> <span style="color:yellow;"><?php echo $link; ?></span><br>
                        <small><strong>Teléfono: </strong> <span style="color:yellow;"><?php echo $bodega_telefono; ?></span></small><br>
                        <small><strong>Dirección: </strong> <span style="color:yellow;"><?php echo $bodega_direccion; ?></span></small><br>
                        <small><strong>Código de Seguridad: </strong> <span style="color:yellow;"><?php echo $codigo_seguridad; ?></span></small><br>
                        <strong>Usuario: </strong><span style="color:yellow;"><?php echo $usuario_usuario; ?></span> -- 
                        <?php if (!$info_clave) {?>
                          <span>Contraseña: <?php echo $usuario_usuario ?> </span>
                       <?php } else{ ?>
                        <span>Contraseña: <?php echo $info_clave; ?> </span>
                      <?php } ?>
                    </div>
                </a>
                <br>
            </div>
          <?php } ?>
          <?php foreach ($empresa_creadas as $registro) {?>
            <div class="col-sm-4 empresa">
                <a href="../login.php?link=<?php echo $registro['link'] ?>" target="_blank" onclick="cerrarSesion()">
                    <div class="position-relative p-3" style="height: 180px; background: #161aade0;color:#fff;border-radius: 30px;font-size: 20px;">
                        <div class="ribbon-wrapper ribbon-xl">
                            <div class="ribbon bg-warning text-lg">
                                <strong><?php echo $registro['empresa_nombre']; ?></strong>
                            </div>
                        </div>
                        <strong>Código del Local: </strong> <span style="color:yellow;"><?php echo $registro['link']; ?></span><br>
                        <small><strong>Teléfono: </strong> <span style="color:yellow;"><?php echo $registro['empresa_telefono']; ?></span></small><br>
                        <small><strong>Dirección: </strong> <span style="color:yellow;"><?php echo $registro['empresa_direccion']; ?></span></small><br>
                        <small><strong>Código de Seguridad: </strong> <span style="color:yellow;"><?php echo $registro['codigo_seguridad']; ?></span></small><br>
                        <strong>Usuario: </strong><span style="color:yellow;"><?php echo $registro['usuario_usuario']; ?></span> -- 
                        <?php if (!$registro['info_clave']) {?>
                          <span>Contraseña: <?php echo $registro['usuario_usuario']; ?> </span>
                       <?php } else{ ?>
                        <span>Contraseña: <?php echo $registro['info_clave']; ?> </span>
                      <?php } ?>
                    </div>
                </a>
                <br>
            </div>
          <?php } ?>
        </div>
<?php include("../templates/footer.php") ?>