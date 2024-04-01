<?php include("../templates/header.php") ?>
<?php 
if ($_SESSION['valSudoAdmin']) {
  $crear_venta_link  = "crear_venta.php";
  $devolucion_venta_link  = "devolucion_venta.php";

}else{
  $crear_venta_link  = "crear_venta.php?link=".$link;
  $devolucion_venta_link  = "devolucion_venta.php?link=".$link;
}
//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM venta WHERE venta_id=:venta_id");
  $sentencia->bindParam(":venta_id",$txtID);
  $sentencia->execute();
  
}
if($_SESSION['rolSudoAdmin']){
$sentencia=$conexion->prepare("SELECT venta.*, usuario.*, cliente.*, empresa.empresa_nombre
FROM venta 
INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id
INNER JOIN empresa ON venta.link = empresa.link;");

}else{
$link=(isset($_GET['link']))?$_GET['link']:"";
$sentencia=$conexion->prepare("SELECT venta.*, usuario.*, cliente.*, empresa.empresa_nombre, empresa.codigo_seguridad
FROM venta 
INNER JOIN usuario ON venta.responsable = usuario.usuario_id 
INNER JOIN cliente ON venta.cliente_id = cliente.cliente_id
INNER JOIN empresa ON venta.link = empresa.link
WHERE venta.link = :link");
$sentencia->bindParam(":link",$link);

}

$sentencia->execute();
$lista_ventas=$sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

      <div class="card card-success">
        <div class="card-header">
          <h2 class="card-title textTabla">HISTORIAL DE VENTAS &nbsp;<a class="btn btn-warning" style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $crear_venta_link;?>" class="btn btn-warning">Crear Venta</a></h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="historialVentas" class="table table-bordered table-striped" style="text-align:center"> 
            <thead>
            <tr>
              <th>Código</th>
              <th>Fecha / Hora </th>
              <th>Total</th>
              <th>Pagado</th>
              <th>Cambio</th>
              <th>Cliente</th>
              <th>responsable</th>
              <?php if ($_SESSION['rolSudoAdmin']) { ?>
              <th>Negocio</th>
              <?php } ?>            
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php foreach ($lista_ventas as $registro) {?>
                <tr>
                <?php if (!$_SESSION['rolSudoAdmin']) { ?>
                  <input type="text" name="codigo_seguridad" value="<?php echo $registro['codigo_seguridad']; ?>">
                <?php } ?>     
                  <td scope="row"><?php echo $registro['venta_codigo']; ?></td>
                  <td><?php echo $registro['venta_fecha']; ?> / <?php echo $registro['venta_hora']; ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['venta_total'], 0, '.', ','); ?></td>
                  <td class="tdColor"><?php echo '$' . number_format($registro['venta_pagado'], 0, '.', ','); ?></td> 
                  <td class="tdColor"><?php echo ($registro['venta_metodo_pago'] == 0 || $registro['venta_metodo_pago'] == 1) ? '$' . number_format($registro['venta_cambio'], 0, '.', ',') : "Credito Saldado"; ?></td>
                  <td><a  <?php if($registro['cliente_id'] != 0 ){ ?> href="../editar_clientes.php?txtID=<?php echo $registro['cliente_id']; ?>" <?php }?>    ><?php echo $registro['cliente_nombre']; ?></a></td>
                  <td><?php echo  $registro['usuario_nombre']; ?></td>
                  <?php if ($_SESSION['rolSudoAdmin']) { ?>
                    <td><?php echo  $registro['empresa_nombre']; ?></td>
                  <?php } ?>  
                  <td>

                  <?php if ($_SESSION['rolSudoAdmin']) { ?>
                    <a class="btn btn-primary btn-sm" href="detalles.php?txtID=<?php echo $registro['venta_id']; ?>" role="button" title="Detalles">
                      <i class="fas fa-eye"></i> Ver
                    </a>
                    <?php } else { ?>
                      <a class="btn btn-primary btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $ventas_detalles_link . '&txtID=' . $registro['venta_id']; ?>" role="button" title="Detalles">
                      <i class="fas fa-eye"></i> Ver
                    </a>
                   <?php } ?>

                   <?php if ($_SESSION['rolSudoAdmin']) { ?>
                    <a class="btn btn-warning btn-sm" href="devolucion_venta.php?txtID=<?php echo $registro['venta_id']; ?>" role="button" title="Devolucion">
                      <i class="fas fa-retweet"></i> Devolución
                    </a>
                    <?php } else { ?>
                            <a class="btn btn-warning btn-sm" href="javascript:void(0);" onclick="mostrarPrompt(<?php echo $registro['venta_id']; ?>)" title="Devolucion">
                                <i class="fas fa-retweet"></i> Devolución
                            </a>
                        <?php } ?>

                    <?php if ($_SESSION['rolSudoAdmin']) { ?>
                      <a class="btn btn-danger btn-sm" href="index_ventas.php?txtID=<?php echo $registro['venta_id']; ?>" role="button" title="Eliminar">
                        <i class="fas fa-trash-alt"></i> Eliminar 
                      </a>
                    <?php } ?>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <script>
function mostrarPrompt(venta_id) {
    Swal.fire({
        title: 'Ingrese el código de seguridad:',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (codigoIngresado) => {
            var codigoAlmacenado = document.querySelector('input[name="codigo_seguridad"]').value;
            if (!codigoIngresado || codigoIngresado.trim() === '') {
                Swal.showValidationMessage('Debe ingresar un código de seguridad');
            } else if (codigoIngresado === codigoAlmacenado) {
                window.location.href = "<?php echo $url_base;?>secciones/<?php echo $devolucion_venta_link; ?>&txtID=" + venta_id;
            } else {
                Swal.showValidationMessage('El código de seguridad ingresado no es válido.');
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
}
</script>
      <?php include("../templates/footer.php") ?>