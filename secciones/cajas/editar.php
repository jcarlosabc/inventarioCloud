<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT * FROM caja WHERE caja_id=:caja_id");
    $sentencia->bindParam(":caja_id",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    
    $caja_id=$registro["caja_id"];
    $caja_numero=$registro["caja_numero"];
    $caja_nombre=$registro["caja_nombre"];
    $caja_efectivo=$registro["caja_efectivo"];
    
}


if ($_POST) {
    
    $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
    $caja_numero= isset($_POST['caja_numero']) ? $_POST['caja_numero'] : "";
    $caja_nombre= isset($_POST['caja_nombre']) ? $_POST['caja_nombre'] : "";
    $caja_efectivo= isset($_POST['caja_efectivo']) ? $_POST['caja_efectivo'] : "";

    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $caja_efectivo = str_replace(array('$', ','), '', $caja_efectivo);
     
    $sentencia_edit = $conexion->prepare("UPDATE caja SET 
    caja_numero=:caja_numero,
    caja_nombre=:caja_nombre,
    caja_efectivo=:caja_efectivo    
    WHERE caja_id =:caja_id");   

    $sentencia_edit->bindParam(":caja_id", $txtID);
    $sentencia_edit->bindParam(":caja_numero", $caja_numero);
    $sentencia_edit->bindParam(":caja_nombre", $caja_nombre);
    $sentencia_edit->bindParam(":caja_efectivo", $caja_efectivo);

$resultado_edit = $sentencia_edit->execute();
    if ($resultado_edit) {
        echo '<script>
        Swal.fire({
            title: "¡Caja Actualizada Correctamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "http://localhost/inventariocloud/secciones/cajas/";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Actualizar la Caja",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }

}

?>
<br>
<script>
    //Colocar el signo $ en el efectivo de la Caja
    document.addEventListener('DOMContentLoaded', function () {
        // Obtener el input de efectivo en caja
        var inputEfectivo = document.getElementById("caja_efectivo");

        // Escuchar el evento 'input' para actualizar el valor formateado
        inputEfectivo.addEventListener("input", function(event) {
            // Obtener el valor actual del input
            var valor = event.target.value;

            // Remover cualquier caracter que no sea número
            valor = valor.replace(/[^\d]/g, '');

            // Añadir el signo de peso al inicio
            valor = "$" + valor;

            // Formatear el número con separador de miles
            valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

            // Asignar el valor formateado de vuelta al input
            event.target.value = valor;
        });

        // Prevenir el envío del formulario si el valor de efectivo en caja no es válido
        document.getElementById("formCaja").addEventListener("submit", function(event) {
            // Obtener el valor actual del input
            var valor = inputEfectivo.value;

            // Remover cualquier caracter que no sea número
            valor = valor.replace(/[^\d]/g, '');

            // Si el valor es vacío o no es un número válido, prevenir el envío del formulario
            if (valor === '' || isNaN(parseInt(valor))) {
                event.preventDefault();
                alert("Ingrese un monto válido en efectivo en caja.");
            }
        });
    });
</script>

<!-- left column -->
<div class="">
  <!-- general form elements -->
  <div class="card card-warning" style="margin-top:7%">
      <div class="card-header">
          <h3 class="card-title textTabla">EDITAR CAJA</h3>
      </div>
    <!-- /.card-header -->
    <!-- form start --> 
      <form action="" method="POST" id="formCaja">
          <div class="card-body ">
              <div class="row">
                  <div class="col-sm-4">
                      <div class="form-group">
                            <input type="hidden" class="textLabel" name="txtID" id="txtID" value="<?php echo $caja_id;?>" >
                          <label for="caja_numero" class="textLabel">Numero de la Caja</label> 
                          <input type="text" class="form-control camposTabla" name="caja_numero"  id="caja_numero" value="<?php echo $caja_numero;?>">
                      </div>                               
                   </div>
              <div class="col-sm-4">
                  <div class="form-group">
                       <label for="caja_nombre" class="textLabel">Nombre de la Caja</label> 
                      <input type="text" class="form-control camposTabla" name="caja_nombre"  id="caja_nombre" value="<?php echo $caja_nombre;?>">
                  </div>                                
              </div>
              <div class="col-sm-4">
                  <div class="form-group">
                       <label for="caja_efectivo" class="textLabel">Efectivo de la Caja</label> 
                      <input type="text" class="form-control camposTabla" name="caja_efectivo"  id="caja_efectivo" value="<?php echo '$' . number_format($caja_efectivo, 2, '.', ','); ?>">
                  </div>                                                               
              </div>

              </div>
          </div>
          <!-- /.card-body -->
          <div class="card-footer" style="text-align:center">
              <button type="submit"  class="btn btn-primary">Guardar</button>
              <a role="button"  href="index.php" class="btn btn-danger">Cancelar</a>
          </div>
      </form>
  </div>
  <!-- /.card -->
</div>









<?php include("../../templates/footer_content.php") ?>