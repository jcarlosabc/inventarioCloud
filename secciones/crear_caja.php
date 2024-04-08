<?php include("../templates/header.php") ?>
<?php 

if ($_SESSION['valSudoAdmin']) {
    $index_cajas_link  = "index_cajas.php";
  
 }else{
    $index_cajas_link  = "index_cajas.php?link=".$link;
 }

 if(isset($_GET['link'])){
    $link=(isset($_GET['link']))?$_GET['link']:"";
 }
if ($_POST) {
    $caja_numero = isset($_POST['caja_numero']) ? $_POST['caja_numero'] : "";
    $caja_nombre = isset($_POST['caja_nombre']) ? $_POST['caja_nombre'] : "";
    $caja_efectivo = isset($_POST['caja_efectivo']) ? $_POST['caja_efectivo'] : "";  
    $responsable = $_SESSION['usuario_id'];
    $link =  isset($_POST['link']) ? $_POST['link'] : "";
    if ($responsable == 1) {
        $link = "sudo_admin";
    }
    // Eliminar el signo "$" y el separador de miles "." del valor del campo de entrada
    $caja_efectivo = str_replace(array('$','.',','), '', $caja_efectivo); 
    


        $sentencia = $conexion->prepare("SELECT * FROM dinero_por_quincena WHERE link = :link AND metodo_pago = 0");
        $sentencia->bindParam(":link", $link);
        $sentencia->execute();
        $lista_efectivo = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        
       $metodo_pago = 0;
       $transferencia_metodo = '';
       date_default_timezone_set('America/Bogota'); 
    //    $fechaDia = date("d");
    //    $fechaMes = date("m");
    //    $fechaYear = date("Y");
       $fechaDia = 1;
       $fechaMes = 04;
       $fechaYear = date("Y");
        if (empty($lista_efectivo)) {
            
            echo "=============";
            echo "<br>";
            echo "...Añadieron Efectivo a la Nomina...";
            echo "<br>";
            echo "=============";

        $sentencia = $conexion->prepare("INSERT INTO dinero_por_quincena(dinero, metodo_pago,link, dia, mes, anio, transferencia_metodo) 
            VALUES (:dinero, :metodo_pago,:link, :dia, :mes,:anio, :transferencia_metodo)");
        $sentencia->bindParam(":dinero", $caja_efectivo);
        $sentencia->bindParam(":metodo_pago", $metodo_pago);
        $sentencia->bindParam(":link", $link);
        $sentencia->bindParam(":dia", $fechaDia);
        $sentencia->bindParam(":mes", $fechaMes);
        $sentencia->bindParam(":anio", $fechaYear);
        $sentencia->bindParam(":transferencia_metodo", $transferencia_metodo);
        $sentencia->execute();
        } else {
            foreach ($lista_efectivo as $fila) {
                echo "Aqui suma la tabla";
                if ($fechaDia <= 15 && $fila['mes'] == $fechaMes) {
                    if ($fechaDia <= 15 && $fila['mes'] == $fechaMes && $fila['anio'] == $fechaYear && $fila['metodo_pago']==$metodo_pago && $fila['transferencia_metodo'] == '' && $fila['link']== $link) {
                        echo "=============";                    
                        echo "<br>";                    
                        echo "... | Sumar dinero de caja nueva a dinero quincena |...";
                        echo "<br>";                    
                        echo "=============";   

                        $sql = "UPDATE dinero_por_quincena SET dinero = ? WHERE id = ?";
                        $sentencia = $conexion->prepare($sql);
                        $params = array(
                            $fila['dinero'] += $caja_efectivo,
                            $fila['id']
                        );
                        $resul_nuevaCaja = $sentencia->execute($params);

                        if ($resul_nuevaCaja) {
                           echo "... | Sumando dinero a la primera quincena por la caja nueva |...";
                        }else {
                           echo "... | No sumo nada |...";
                        }


                    }
                }else if ($fechaDia > 15 && $fila['dia'] > 15 && $fila['mes'] == $fechaMes && $fila['anio'] == $fechaYear && $fila['metodo_pago'] == 0 && $fila['link']== $link ) {
                    echo "=============";                    
                    echo "<br>";                    
                    echo "...Sumar dinero de caja nueva a dinero segunda quincena...";
                    echo "<br>";                    
                    echo "=============";                    
    
                    $sql = "UPDATE dinero_por_quincena SET dinero = ? WHERE id = ?";
                    $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $fila['dinero'] += $caja_efectivo,
                        $fila['id']
                    );
                    $sentencia->execute($params);
                }
            }
        }

    $sentencia = $conexion->prepare("INSERT INTO caja(caja_numero, caja_nombre, caja_efectivo, link, responsable) 
        VALUES (:caja_numero, :caja_nombre,:caja_efectivo, :link, :responsable)");
    
    $sentencia->bindParam(":caja_numero", $caja_numero);
    $sentencia->bindParam(":caja_nombre", $caja_nombre);
    $sentencia->bindParam(":caja_efectivo", $caja_efectivo);
    $sentencia->bindParam(":link", $link);
    $sentencia->bindParam(":responsable",$responsable);
    $resultado = $sentencia->execute();
    
    if ($resultado) {
        echo '<script>
        // Código JavaScript para mostrar SweetAlert
        Swal.fire({
            title: "¡Caja Creada Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$index_cajas_link.'";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Crear nueva Caja",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>
    <br>
    <div class="card card-primary" style="margin-top:7%">
        <div class="card-header">
            <h3 class="card-title textTabla">REGISTRE UNA NUEVA CAJA &nbsp;&nbsp;<a class="btn btn-warning"  style="color:black" href="<?php echo $url_base;?>secciones/<?php echo $index_cajas_link;?>" role="button">Lista de Caja</a></h3>
        </div>
        <!-- /.card-header -->
        <!-- form start --> 
        <form action="" method="POST">
            <input type="hidden" name="link" value="<?php echo $link ?>">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Código</label> 
                            <input type="text" class="form-control camposTabla" name="caja_numero" required>
                        </div>                               
                        </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Nombre</label> 
                            <input type="text" class="form-control camposTabla" name="caja_nombre" required>
                        </div>                                
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="cajaEfectivo" class="textLabel">Efectivo</label> 
                            <input type="text" class="form-control camposTabla_dinero" id="cajaEfectivo" name="caja_efectivo" required>
                        </div>                                
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer" style="text-align:center">
                <button type="submit"  class="btn btn-primary btn-lg">Guardar</button>
                <a role="button" href="<?php echo $url_base;?>secciones/<?php echo $crear_caja_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
<?php include("../templates/footer.php") ?>