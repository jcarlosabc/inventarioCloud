<?php include("../templates/header.php") ?>
<?php 

if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT nomina.*, u.usuario_nombre, u.usuario_apellido 
    FROM nomina 
    LEFT JOIN usuario u ON u.usuario_id = nomina.nomina_usuario_id WHERE usuario_id = :usuario_id");
    $sentencia->bindParam(":usuario_id",$txtID);
    $sentencia->execute();
    $lista_nomina=$sentencia->fetchAll(PDO::FETCH_ASSOC);
  }

  if ($_POST) {
    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d/m/Y");

    $nomina_usuario_id = isset($_POST['nomina_usuario_id']) ? $_POST['nomina_usuario_id'] : "";
    $nomina_cantidad = isset($_POST['nueva_nomina']) ? $_POST['nueva_nomina'] : "";

    $sql = "INSERT INTO nomina (nomina_usuario_id, nomina_fecha, nomina_cantidad, nomina_estado) VALUES (?, ?, ?, '1')";
                
        $sentencia = $conexion->prepare($sql);
        $params = array(
            $nomina_usuario_id,
            $fechaActual,
            $nomina_cantidad
        );
        $resultado=$sentencia->execute($params);

        if ($resultado) {
            echo '<script>
            // Código JavaScript para mostrar SweetAlert
            Swal.fire({
                title: "Pago Procesado correctamente!!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result) => {
                if(result.isConfirmed){
                    window.location.href = "'.$url_base.'secciones/nomina_pago.php?txtID='.$txtID.'";
                }
            })
            </script>';
        }

  }

?>
      <br>
      <div class="card card-primary">
        <div class="card-header">
        <h2 class="card-title textTabla">LISTA DE PAGOS &nbsp;</h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="lista_categoria" class="table table-bordered table-striped" style="text-align:center">
            <thead>
            <tr>
              <th>#</th>
              <th>Código de Usuario</th>
              <th>Nombre</th>
              <th>fecha del pago</th> 
              <th>Cantidad</th> 
            </tr>
            </thead>
            <tbody>
              <?php $count = 0;
              foreach ($lista_nomina as $registro) {?>
                <tr class="">
                  <td scope="row"><?php $count++; echo $count; ?></td>
                  <td><?php echo $registro['nomina_usuario_id']; ?></td>
                  <td><?php echo $registro['usuario_nombre']; ?> <?php echo $registro['usuario_apellido']; ?></td>                               
                  <td><?php echo $registro['nomina_fecha']; ?></td>                  
                  <td><?php echo $registro['nomina_cantidad']; ?></td> 
                </tr>  
              <?php } ?>
            </tbody>                  
          </table>

          <form action="" method="POST" id="formNomia">
            <div class="card-body ">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3" style="justify-content:center">
                        <div class="form-group">
                            <input type="text" class="form-control camposTabla" name="nueva_nomina" required >
                            <input type="hidden" name="nomina_usuario_id" value="<?php echo $txtID?>">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer" style="text-align:center">
                <button type="submit" class="btn btn-primary btn-lg">Añadir Pago</button>
                <a role="button" href="nomina.php" class="btn btn-danger btn-lg">Cancelar</a>
            </div>
        </form>

        </div>
      </div>
<?php include("../templates/footer.php") ?>