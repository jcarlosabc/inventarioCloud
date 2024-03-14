<?php include("../templates/header.php") ?>
<?php 
//Eliminar Elementos
    $sentencia=$conexion->prepare("SELECT * FROM venta WHERE venta_metodo_pago = 2");
    $sentencia->execute();
    $lista_ventas_credito=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

?>

      <div class="card card-primary ">
        <div class="card-header text-center ">
          <h2 class="card-title textTabla">CUENTAS PENDIENTE DE LOS CLIENTES</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_usuario" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Código Venta</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Pagado</th>
              <th>Pago Pendiente</th>
              <th>Método de Pago</th>
              <th>Cuotas</th>
              <th>Abonar</th>

            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
               foreach ($lista_ventas_credito as $registro) {?>
                <tr class="">
                  <td scope="row"><?php $count++; echo $count;  ?></td>
                  <td><?php echo $registro['venta_codigo']; ?></td>
                  <td><?php echo $registro['venta_fecha'] . " " . $registro['venta_hora'] ; ?></td>
                  <td class="tdColor"><?php echo '$'. number_format( $registro['venta_total'] ,0, '.', ','); ?></td>                
                  <td class="tdColor"><?php echo '$'. number_format( $registro['venta_pagado'] ,0, '.', ','); ?></td>                
                  <td class="tdColor"><?php echo $registro['venta_cambio'] < 0 ? '$'.number_format( abs($registro['venta_cambio']) ,0, '.', ',') : "Cuenta Saldada"; ?></td>
                  <td ><?php echo ($registro['venta_metodo_pago']==2)? "Credito": "nad"; ?></td>
                  <td><?php echo $registro['partes']; ?></td>

                  <?php if ($registro['partes']==0) { ?>                    
                  <td><a class="btn btn-success btn-sm" role="button" title="Pagado">
                              <i class="fa fa-check" aria-hidden="true">
                              </i>
                              Pagado
                          </a></td>

                  <?php } else { ?> 
                    <td><a class="btn btn-warning btn-sm" href="<?php echo $url_base ?>secciones/crear_abono.php?txtID=<?php echo $registro['venta_codigo']; ?>" role="button" title="Abonar">
                              <i class="fa fa-credit-card" aria-hidden="true">
                              </i>
                              Abonar
                          </a></td>
                    <?php } ?> 
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>

<?php include("../templates/footer.php") ?>