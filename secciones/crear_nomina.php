<?php include("../templates/header.php") ?>
<?php
    $index_nomina  = "nomina.php";

    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    // Mostrar Empleados
    // $sentencia=$conexion->prepare("SELECT u.*, MAX(n.nomina_prestamo) AS nomina_prestamo,
    //                             MAX(n.nomina_estado) AS nomina_estado 
    //                             FROM usuario u 
    //                             LEFT JOIN nomina n ON n.nomina_usuario_id = u.usuario_id 
    //                             WHERE u.usuario_id > 1 
    //                             GROUP BY u.usuario_id;");
    // $sentencia->execute();
    // $lista_empleados=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

if ($_POST) {
    $valor_seleccionado = $_POST['nomina_empleado'];
    $partes = explode('-', $valor_seleccionado);
    $usuario_id = $partes[0];
    $link = $partes[1];
    $nomina_caja = isset($_POST['nomina_caja']) ? $_POST['nomina_caja'] : "";    
    $nomina_cantidad = isset($_POST['nomina_cantidad']) ? $_POST['nomina_cantidad'] : "";
    $nomina_cantidad = str_replace(array('$','.', ','), '', $nomina_cantidad);
    $nomina_adelanto = isset($_POST['nomina_adelanto']) ? $_POST['nomina_adelanto'] : "";
    $metodo_pago = isset($_POST['metodo_pago_nomina']) ? $_POST['metodo_pago_nomina'] : $_POST['metodo_pago_nomina'];
    $vale_pendiente = isset($_POST['vale_pendiente']) ? $_POST['vale_pendiente'] : $_POST['vale_pendiente'];
    echo " vale_pendiente = >" .$vale_pendiente;
    $descontar_vale = isset($_POST['descontar_vale']) ? $_POST['descontar_vale'] : $_POST['descontar_vale'];

    // Condicionales
    if ($nomina_adelanto === "00") {
        $nomina_estado = 1 ;
        $nomina_prestamo = 0;
    }else {
        $nomina_estado = 0 ;
        $nomina_prestamo = $nomina_cantidad;
        $nomina_cantidad = 0;
    }
    if ($descontar_vale == 1 && $vale_pendiente != "") {
        $nomina_estado_vale = 1;
    }
    
    $sentencia=$conexion->prepare("SELECT * FROM nomina WHERE nomina_usuario_id = :usuario_id AND nomina_estado = 0 ORDER BY nomina_usuario_id DESC");
    $sentencia->bindParam(":usuario_id", $usuario_id);
    $sentencia->execute();
    $nomina_vale = $sentencia->fetch(PDO::FETCH_LAZY);
    if ($nomina_vale) {
        $infoId = $nomina_vale['nomina_id'];
        
        $sql = "UPDATE nomina SET nomina_estado = ? WHERE nomina_id = ?";
        $sentencia_envio = $conexion->prepare($sql);
        $params = array($nomina_estado_vale, $infoId);
        $resultado = $sentencia_envio->execute($params);
    }



    // Actualizando - Restando la Nomina en dtpmp
    // Efectivo
    $nomina_adelanto === "00" ? $nomina_cantidad_dinero = $nomina_cantidad : $nomina_cantidad_dinero = $nomina_prestamo;
    if ($metodo_pago == 0) {
        $sql = "UPDATE dtpmp SET efectivo = efectivo - ?";
        $sentencia = $conexion->prepare($sql);
        $params = array($nomina_cantidad_dinero);
        $sentencia->execute($params);
    }else if ($metodo_pago == 1) {
    // Transferencia
        $transferenciaMetodo = isset($_POST['transferenciaMetodoNomina']) ? $_POST['transferenciaMetodoNomina'] : $_POST['transferenciaMetodoNomina'];
        if ($transferenciaMetodo == 00 ) {
            //davivienda
            $sql = "UPDATE dtpmp SET davivienda = davivienda - ?";
            $sentencia = $conexion->prepare($sql);
            $params = array($nomina_cantidad_dinero);
            $sentencia->execute($params);
        }
        if ($transferenciaMetodo == 01) {
            //bancolombia
            $sql = "UPDATE dtpmp SET bancolombia = bancolombia - ?";
            $sentencia = $conexion->prepare($sql);
            $params = array($nomina_cantidad_dinero);
            $sentencia->execute($params);
        }
        if ($transferenciaMetodo == 02) {
            //Nequi
            $sql = "UPDATE dtpmp SET nequi = nequi - ?";
            $sentencia = $conexion->prepare($sql);
            $params = array($nomina_cantidad_dinero);
            $sentencia->execute($params);
        }
    }else {
        $transferenciaMetodo = "";
    }

    // Registrando nomina
    $sql = "INSERT INTO nomina (nomina_usuario_id, nomina_fecha, nomina_hora, nomina_cantidad, nomina_prestamo, nomina_estado, link) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $sentencia = $conexion->prepare($sql);
    $params = array($usuario_id, $fechaActual, $horaActual, $nomina_cantidad, $nomina_prestamo, $nomina_estado, $link );
    $resultado = $sentencia->execute($params);

    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "¡Pago Efectuado Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = "'.$url_base.'secciones/'.$index_nomina.'";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Pagar",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>
<br>
    <div class="card card-success" style="margin-top:7%">
        <div class="card-header text-center">
            <h2 class="card-title textTabla">NÓMINA</h2>
        </div>
        <form action=" " method="post">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-2">
                        <label class="textLabel">Empleado</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <!-- <select class="form-control select2 camposTabla" style="width: 100%;" name="nomina_empleado">
                            <option value="">Seleccione Empleado</option>
                            php foreach ($lista_empleados as $registro) { ?>
                                <option value="php echo $registro['usuario_id'] . '-' . $registro['link']; ?>">php echo $registro['usuario_nombre'] . " " .$registro['usuario_apellido'] . " " . $registro['usuario_cedula'] ?></option>
                            php } ?>          
                        </select> -->
                        <select class="form-control select2" onchange="listnominados()" name="nomina_empleado" id="listaNominandoEmpleado" style="height: 20px">
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label class="textLabel">Metodo de Pago</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <select class="form-control select2 camposTabla" id="metodoPago_nomina" name="metodo_pago_nomina" onchange="mostrarMetodosNomina(1)">
                            <option value="0" style="color:#22c600">Efectivo</option> 
                            <option value="1" style="color:#009fc1">Transferencia</option> 
                        </select>
                    </div>
                    <div id="metodo_transferencia_nomina">
                        <div>
                            <label class="textLabel">Escoger Banco</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <select class="form-control select2 camposTabla" id="transferenciaMetodoNomina" name="transferenciaMetodoNomina">                                    
                                <option value="00" style="color:#22c600">davivienda</option> 
                                <option value="01" style="color:#009fc1">bancolombia</option> 
                                <option value="02" style="color:#d50000">nequi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label class="textLabel">Vale</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <select class="form-control select2 camposTabla" id="nomina_adelanto" name="nomina_adelanto">                                    
                            <option value="00" style="color:#22c600">No</option> 
                            <option value="01" style="color:#009fc1">Si</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row" style="justify-content:center">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Vales Pendientes</label>
                            <input type="text" name="vale_pendiente" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 0, 'digitsOptional': false, 'prefix': '$ ', 'placeholder': '0'" class="form-control camposTabla_dinero text-warning" id="vales_nomina" readonly>
                        </div>
                    </div>
                    <style>
                        .custom-radio {
                            transform: scale(1.5);
                            margin: 3px;
                        }
                    </style>
                    <div class="col-1">
                        <div class="form-group">
                            <label>¿Descontar?</label>
                            <table style="width: 74%">
                                <tr>     
                                    <td><input checked type="radio" class="custom-radio" name="descontar_vale" value="1"> Si</td>
                                    <td><input type="radio" class="custom-radio" name="descontar_vale" value="2"> No</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Pago Establecido</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 0, 'digitsOptional': false, 'prefix': '$ ', 'placeholder': '0'" class="form-control camposTabla_dinero" placeholder="$ 000.000" id="nominaCantidad" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="textLabel">Pago final</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 0, 'digitsOptional': false, 'prefix': '$ ', 'placeholder': '0'" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="nomina_cantidad" id="pagoFinal" required>
                        </div>
                    </div>
                </div>
            </div>
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg">Pagar</button>
                </div>
            </div>
        </form>
    </div>

<style>
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
    #metodo_transferencia_nomina{
        display: none;
    }
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
    /* Ocultar las flechas de incremento/decremento en campos numéricos */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield; /* Firefox */
    }
</style>
<script src="https://code.jquery.com/jquery-3.7.1.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
<script src="accion_fetchNominado.js"></script>
<script>
$(document).ready(function(){
    $('#vales_nomina, #nominaCantidad, #pagoFinal').inputmask();
});
</script>
<?php include("../templates/footer.php") ?>