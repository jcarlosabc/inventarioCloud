<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

$sentencia=$conexion->prepare("SELECT * FROM venta WHERE venta_metodo_pago = 2 and estado_venta = 0;");
$sentencia->execute();
$lista_ventas_credito=$sentencia->fetchAll(PDO::FETCH_ASSOC); 
?>
        <br>    
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
              <th>Total Ventas</th>
              <th>Venta pagado</th>
              <th>Venta cambio</th>
              <th>Metodo Pago</th>
              <th>Cuotas</th>
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
                  <td class="text-danger"><?php echo '$'.number_format( abs($registro['venta_cambio']) ,0, '.', ','); ?></td>
                  <td><?php echo ($registro['venta_metodo_pago']==2)? "Credito": "nad"; ?></td>
                  <td><?php echo $registro['partes']; ?></td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>

<?php include("../../templates/footer_content.php") ?>