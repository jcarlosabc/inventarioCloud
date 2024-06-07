<?php include("../templates/header.php") ?>
<?php 

if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";
}

if ($_SESSION['valSudoAdmin']) {
    $detalles_traslado_local = "detalles_traslado_local.php?txtID";
    $index_traslados = "index_traslados.php?txtID";
 }else{
    $detalles_traslado_local = "detalles_traslado_local.php?link=".$link.'&txtID';
    $index_traslados = "index_traslados.php?link=".$link."&txtID";
 }
$_SESSION['link_remitente_array'] = array();

//Eliminar Elementos
if(isset($_GET['txtID'])){
  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $borrarProducto = false;
  $actualizarProducto = false;

  $sentencia_delete_traslado = $conexion->prepare("SELECT p.*, ht.cantidad FROM producto p 
    JOIN historial_traslados ht ON p.producto_id = ht.producto_id WHERE ht.traslado = :txtID");
    $sentencia_delete_traslado->bindParam(":txtID", $txtID);
    $sentencia_delete_traslado->execute();
    $buscando_producto_traslado = $sentencia_delete_traslado->fetchAll(PDO::FETCH_ASSOC);

    foreach ($buscando_producto_traslado as $key) {
      $cantidadHt = $key['cantidad'];
      $producto_codigoProducto = $key['producto_codigo'];

      $sentencia_delete_traslado = $conexion->prepare("SELECT * FROM producto WHERE traslado IS NOT NULL");
      $sentencia_delete_traslado->execute();
      $buscando_tblProducto_traslado = $sentencia_delete_traslado->fetchAll(PDO::FETCH_ASSOC);
      
      foreach ($buscando_tblProducto_traslado as $value) {
        $traslado = json_decode($value['traslado'], true); // Decodificar el valor de 'traslado'
        // Verificar si $txtID está en el array decodificado
        // if (is_array($traslado) && in_array($txtID, $traslado)) {
        //     echo " *** Producto encontrado ***";
        //     echo "<br>";
        //     echo "Producto id =>: " . $value['producto_id'] . "\n";
        //     echo "<br>";
        //     echo "Codigo Traslado =>: " . $value['traslado'] . "\n";
        // }
      
        $cantidadProducto = $value['producto_stock_total'];
        if ($cantidadHt >= $cantidadProducto) {
          $borrarProducto = true;
          break;
        }else if($cantidadHt < $cantidadProducto) {
          $actualizarProducto = true;
          break;
        }
      }
      if ($borrarProducto) {
        if ($_SESSION['rolBodega']) { 
          // Devolviendo los productos del local remitente
          $sql = "UPDATE bodega SET producto_stock_total = producto_stock_total + ? WHERE producto_codigo = ?";
          $sentencia_producto = $conexion->prepare($sql);
          $params = array($cantidadHt, $producto_codigoProducto);
          $respuesta = $sentencia_producto->execute($params);

        }else{
          // Devolviendo los productos del local remitente
          $sql = "UPDATE producto SET producto_stock_total = producto_stock_total + ? WHERE producto_id = ?";
          $sentencia_producto = $conexion->prepare($sql);
          $params = array($cantidadHt, $key['producto_id']);
          $respuesta = $sentencia_producto->execute($params);
        }
      }
      
      if ($actualizarProducto) {
        if ($_SESSION['rolBodega']) { 
          // Devolviendo los productos del local remitente
          $sql = "UPDATE bodega SET producto_stock_total = producto_stock_total + ? WHERE producto_codigo = ?";
          $sentencia_producto = $conexion->prepare($sql);
          $params = array($cantidadHt, $producto_codigoProducto);
          $respuesta = $sentencia_producto->execute($params);
        }else {
          $sql = "UPDATE producto SET producto_stock_total = producto_stock_total + ? WHERE producto_id = ?";
          $sentencia_producto = $conexion->prepare($sql);
          $params = array($cantidadHt, $key['producto_id']);
          $respuesta = $sentencia_producto->execute($params);
        }
      }
    }
    
    if ($borrarProducto) {
      // Consulta para obtener los productos donde 'traslado' contiene el txtID
      $sentencia_select = $conexion->prepare(
          "SELECT * FROM producto WHERE JSON_CONTAINS(traslado, :json_id, '$')"
      );
      $sentencia_select->bindValue(":json_id", json_encode($txtID));
      $sentencia_select->execute();
      $productos = $sentencia_select->fetchAll(PDO::FETCH_ASSOC);
  
      foreach ($productos as $resultado) {
          $traslado = json_decode($resultado['traslado'], true);
  
          // Verificar el número de elementos en el array 'traslado'
          if (is_array($traslado) && count($traslado) === 1) {
  
            // Eliminar el registro completo si solo hay un código de traslado
            $sentencia_delete = $conexion->prepare("DELETE FROM producto WHERE producto_id = ?");
            $sentencia_delete->execute([$resultado['producto_id']]);
          } else if (is_array($traslado) && count($traslado) > 1) {

            // Eliminar el código específico del array 'traslado'
            $new_traslado = array_diff($traslado, [$txtID]);

            // Re-encode el array a JSON
            $new_traslado_json = json_encode(array_values($new_traslado)); // array_values reindexa el array

            // Actualizar el registro con el nuevo array 'traslado'
            $sentencia_update = $conexion->prepare("UPDATE producto SET traslado = ? WHERE producto_id = ?");
            $sentencia_update->execute([$new_traslado_json, $resultado['producto_id']]);
        }
      }
    }
  
    if ($actualizarProducto) {
      // Consulta para obtener los productos donde 'traslado' contiene el txtID
      $sentencia_select = $conexion->prepare("SELECT * FROM producto WHERE JSON_CONTAINS(traslado, :json_id, '$')");
      $sentencia_select->bindValue(":json_id", json_encode($txtID));
      $sentencia_select->execute();
      $productos = $sentencia_select->fetchAll(PDO::FETCH_ASSOC);
  
      foreach ($productos as $resultado) {
          $traslado = json_decode($resultado['traslado'], true);
  
          // Verificar el número de elementos en el array 'traslado'
          if (is_array($traslado) && count($traslado) === 1) {
            // echo "El registro con ID " . $resultado['producto_id'] . " tiene solo un código de traslado, eliminando...<br>";
  
            // Actualizar el registro completo si solo hay un código de traslado
            $sql = "UPDATE producto SET producto_stock_total = producto_stock_total - ? WHERE producto_id = ?";
            $sentencia_producto = $conexion->prepare($sql);
            $params = array($cantidadHt, [$resultado['producto_id']]);
            $sentencia_producto->execute($params);

            // Eliminar el registro completo si solo hay un código de traslado
            $sentencia_delete = $conexion->prepare("DELETE FROM producto WHERE producto_id = ?");
            $sentencia_delete->execute([$resultado['producto_id']]);

          } else if (is_array($traslado) && count($traslado) > 1) {

            // Actualizar el registro con el nuevo array 'traslado'
            $sentencia_update = $conexion->prepare("UPDATE producto SET producto_stock_total = producto_stock_total - ? WHERE producto_id = ?");
            $sentencia_update->execute([$cantidadHt, $resultado['producto_id']]);

            // Eliminar el código específico del array 'traslado'
            $new_traslado = array_diff($traslado, [$txtID]);

            // Re-encode el array a JSON
            $new_traslado_json = json_encode(array_values($new_traslado)); // array_values reindexa el array

            // Actualizar el registro con el nuevo array 'traslado'
            $sentencia_update = $conexion->prepare("UPDATE producto SET traslado = ? WHERE producto_id = ?");
            $sentencia_update->execute([$new_traslado_json, $resultado['producto_id']]);
        }
      }
    }
    $sentencia=$conexion->prepare("DELETE FROM historial_traslados WHERE traslado=:txtID");
    $sentencia->bindParam(":txtID",$txtID);
    $sentencia->execute();
}

$responsable = $_SESSION['usuario_id'];
if ($responsable == 1) {

  $sentencia_historial_traslados = $conexion->prepare("SELECT DISTINCT ht.*, ht.traslado as trasladoCode, p.*, e.empresa_nombre 
    FROM historial_traslados ht 
    JOIN bodega p ON ht.producto_id = p.producto_id JOIN empresa e ON ht.link_destino = e.link 
    -- WHERE ht.link_remitente = :link
    GROUP BY ht.traslado");
  // $sentencia_historial_traslados->bindParam(":link", $link);

}else if($link == "sudo_bodega") {
  echo "bodega = >" .$link ;
  $sentencia_historial_traslados = $conexion->prepare("SELECT DISTINCT ht.*, ht.traslado as trasladoCode, p.*, e.empresa_nombre 
    FROM historial_traslados ht 
    JOIN producto p ON ht.producto_id = p.producto_id JOIN empresa e ON ht.link_destino = e.link 
    WHERE ht.link_remitente = :link
    GROUP BY ht.traslado");
  $sentencia_historial_traslados->bindParam(":link", $link);
  
}else {
  $sentencia_historial_traslados = $conexion->prepare("SELECT DISTINCT ht.*, ht.traslado as trasladoCode, p.*, e.empresa_nombre 
    FROM historial_traslados ht 
    JOIN producto p ON ht.producto_id = p.producto_id JOIN empresa e ON ht.link_destino = e.link 
    WHERE ht.link_remitente = :link
    GROUP BY ht.traslado");
  $sentencia_historial_traslados->bindParam(":link", $link);

}
$sentencia_historial_traslados->execute();
$detalle_traslado = $sentencia_historial_traslados->fetchAll(PDO::FETCH_ASSOC)

// -- WHERE ht.link_remitente =:link AND ht.traslado=:txtID");
?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
          <h2 class="card-title textTabla">HISTORIAL DE TRASLADOS</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="listaProductos" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Remision</th>
              <th>Referencia de Traslado</th>
              <th>Destino</th>
              <th>Fecha de Traslado</th>
              <th>Opciones</th>
            </tr>
            </thead>
            <tbody>
              <?php $count = 0; $precioComptaTotal =0;
              foreach ($detalle_traslado as $registro) {
                $_SESSION['link_remitente_array'][] = $registro['link_remitente'];
                ?>
                <tr>
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['remision_traslado']; ?></td>
                  <td><?php echo $registro['trasladoCode']; ?></td>
                  <td><?php echo $registro['empresa_nombre']; ?></td>
                  <td><?php echo $registro['fecha_traslado']; ?></td>
                  <td>
                    <a class="btn btn-primary btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $detalles_traslado_local ;?>=<?php echo $registro['trasladoCode']; ?>" role="button" title="Detalles">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a class="btn btn-danger btn-sm" href="<?php echo $url_base;?>secciones/<?php echo $index_traslados . '=' . $registro['trasladoCode']; ?>" role="button" title="Eliminar">
                        <i class="fas fa-trash-alt"></i> 
                    </a>
                  </td>
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>
        </div>
      </div>
      <?php include("../templates/footer.php") ?>