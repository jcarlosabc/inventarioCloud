

<?php include("../templates/header.php") ?>
<?php
    $index_nomina  = "nomina.php";

if ($_POST) {
    $link = isset($_POST['nomina_empresa']) ? $_POST['nomina_empresa'] : "";
    $usuario_id = isset($_POST['nomina_empleados']) ? $_POST['nomina_empleados'] : "";
    $nomina_caja = isset($_POST['nomina_caja']) ? $_POST['nomina_caja'] : "";    
    $nomina_cantidad = isset($_POST['nomina_cantidad']) ? $_POST['nomina_cantidad'] : "";
    $nomina_cantidad = str_replace(array('$','.', ','), '', $nomina_cantidad);

    //Nomina
    $metodo_pago = isset($_POST['metodo_pago_nomina']) ? $_POST['metodo_pago_nomina'] : $_POST['metodo_pago_nomina'];
    print_r(" //Metoro: ".$metodo_pago . " //// ");

    //Restando la Nomina en la Caja
    if ($metodo_pago == 0) {
        print_r(" //Cantidad: ". $nomina_cantidad . " //// ");
        print_r(" //Caja: ". $nomina_caja . " //// ");
        print_r(" //Link: ". $link . " //// ");
        $sql = "UPDATE caja SET caja_efectivo = caja_efectivo - ? WHERE caja_id = ? AND link = ?;";
            $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $nomina_cantidad, 
                        $nomina_caja,
                        $link  
                    );
                    $sentencia->execute($params);
    }    
    if ($metodo_pago == 1) {
        $transferenciaMetodo = isset($_POST['transferenciaMetodoNomina']) ? $_POST['transferenciaMetodoNomina'] : $_POST['transferenciaMetodoNomina'];
        print_r(" //Cantidad: ". $nomina_cantidad . " //// ");
        print_r(" //Caja: ". $nomina_caja . " //// ");
        print_r(" //Link: ". $link . " //// ");
        print_r(" //transferenciaMetodo: ". $transferenciaMetodo . " //// ");
        if ($transferenciaMetodo == 00 ) {
            //davivienda
            $sql = "UPDATE caja SET davivienda = davivienda - ? WHERE caja_id = ? AND link = ?;";
            $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $nomina_cantidad, 
                        $nomina_caja,
                        $link  
                    );
                    $sentencia->execute($params);
        }
        if ($transferenciaMetodo == 01) {
            //bancolombia
            $sql = "UPDATE caja SET bancolombia = bancolombia - ? WHERE caja_id = ? AND link = ?;";
            $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $nomina_cantidad, 
                        $nomina_caja,
                        $link  
                    );
                    $sentencia->execute($params);
        }
        if ($transferenciaMetodo == 02) {
            //Nequi
            $sql = "UPDATE caja SET nequi = nequi - ? WHERE caja_id = ? AND link = ?;";
            $sentencia = $conexion->prepare($sql);
                    $params = array(
                        $nomina_cantidad, 
                        $nomina_caja,
                        $link  
                    );
                    $sentencia->execute($params);
        }
    }else {
        $transferenciaMetodo = "";
    }



    $sentencia=$conexion->prepare("SELECT * FROM usuario WHERE usuario_id =:usuario_id AND link = :link");
    $sentencia->bindParam(":usuario_id", $usuario_id);
    $sentencia->bindParam(":link", $link);
    $sentencia->execute();
    $lista_ultimo_update=$sentencia->fetch(PDO::FETCH_LAZY);
    if (!$lista_ultimo_update) {
        echo '<script>
        Swal.fire({
            title: "Oops este Empleado no pertenece al Negocio que escogiste",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }else {
        $sql = "INSERT INTO nomina (nomina_usuario_id, nomina_fecha, nomina_hora, nomina_cantidad, nomina_estado, link) 
        VALUES (?,?,?,?,?,?)";
        $sentencia_nomina = $conexion->prepare($sql);
        $params = array(
            $usuario_id,
            $fechaActual,
            $horaActual,
            $nomina_cantidad,
            1,
            $link
        );
        $resultado = $sentencia_nomina->execute($params);
    
      //  $sentencia=$conexion->prepare("SELECT * FROM dinero_por_quincena WHERE link = :link ORDER BY id DESC");
      //  $sentencia->bindParam(":link", $link);
       // $resultado = $sentencia->execute();
       // $lista_ultimo_update=$sentencia->fetch(PDO::FETCH_LAZY);
       // $id = $lista_ultimo_update['id'];
       // $dinero = $lista_ultimo_update['dinero'];
        //$nomina_cantidad = $dinero - $nomina_cantidad;
        
       // $sql = "UPDATE dinero_por_quincena SET dinero = ? WHERE id = ?";
       //     $sentencia = $conexion->prepare($sql);
       //     $params = array(
       //         $nomina_cantidad, 
       //        $id  
       //     );
       //$resultado = $sentencia->execute($params);
    
        if ($resultado) {
            echo '<script>
            Swal.fire({
                title: "¡Proceso de Pago Realizado Exitosamente!",
                icon: "success",
                confirmButtonText: "¡Entendido!"
            }).then((result)=>{
                if(result.isConfirmed){
                    window.location.href= "'.$url_base.'secciones/'.$index_nomina.'"
                }
            })
            </script>';
        } else {
            echo '<script>
            Swal.fire({
                title: "Error al procesar el Pago",
                icon: "error",
                confirmButtonText: "¡Entendido!"
            });
            </script>';
        }
    }
}

$sentencia = $conexion->prepare("SELECT * FROM empresa ");
$sentencia->execute();
$lista_empresas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT u.*, e.empresa_nombre FROM usuario u INNER JOIN empresa e ON u.link = e.link ");
$sentencia->execute();
$lista_usuarios_empresa = $sentencia->fetchAll(PDO::FETCH_ASSOC);

$sentencia = $conexion->prepare("SELECT c.*, e.* FROM caja AS c INNER JOIN empresa AS e ON c.link = e.link");

$sentencia->execute();
$lista_cajas = $sentencia->fetchAll(PDO::FETCH_ASSOC);


?>

<br>
<div class="">
    <div class="card card-primary" style="margin-top:7%">
        <form action="" method="post" id="formNomina" onsubmit="return validarFormulario(4)">
            <div class="card-body">
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <label class="textLabel">Negocio</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <select class="form-control select2 camposTabla" style="width: 100%;" name="nomina_empresa">
                            <option value="">Escoger Negocio</option>
                            <?php foreach ($lista_empresas as $registro) { ?>
                                <option value="<?php echo $registro['link']; ?>"><?php echo $registro['empresa_nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <label class="textLabel">Escoger Empleado</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <select class="form-control select2 camposTabla" style="width: 100%;" name="nomina_empleados">
                            <option value="">Escoger Empleados</option>
                            <?php foreach ($lista_usuarios_empresa as $registro) { ?>
                                <option value="<?php echo $registro['usuario_id']; ?>"><?php echo  $registro['usuario_nombre'] . " " . $registro['usuario_apellido'] . " (". $registro['empresa_nombre'] .") - ".$registro['usuario_cedula']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <label class="textLabel">Escoger Caja</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <select class="form-control select2 camposTabla" style="width: 100%;" name="nomina_caja">
                            <option value="">Escoger Empleados</option>
                            <?php foreach ($lista_cajas as $registro) { ?>
                                <option value="<?php echo $registro['caja_id']; ?>"><?php echo  $registro['caja_nombre'] . " " . " (". " Valor en Caja: ". '$' . number_format($registro['caja_efectivo'], 0, '.', ',') .") - ".$registro['empresa_nombre']; ?></option>
                            <?php } ?>          
                        </select>
                    </div>
                </div>
                <br>
                <!--Comienzo -->
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <label class="textLabel">Escoger Metodo de Pago</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <select class="form-control select2 camposTabla" id="metodoPago_nomina" name="metodo_pago_nomina" onchange="mostrarMetodosNomina(1)">                                    
                                <option value="0" style="color:#22c600">Efectivo</option> 
                                <option value="1" style="color:#009fc1">Transferencia</option> 
                            </select>
                    </div>
                </div>
            
                <style>
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
                <br>
                <div class="row" style="justify-content:center" id="metodo_transferencia_nomina">
                    <div class="col-sm-3">
                        <label class="textLabel">Escoger Banco</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                        <select class="form-control select2 camposTabla" id="transferenciaMetodoNomina" name="transferenciaMetodoNomina">                                    
                            <option value="00" style="color:#22c600">davivienda</option> 
                            <option value="01" style="color:#009fc1">bancolombia</option> 
                            <option value="02" style="color:#d50000">nequi</option>
                        </select>
                    </div>
                </div>

            </div>
                <!--Comienzo -->
                <div class="row" style="justify-content:center">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="textLabel">Monto</label> &nbsp;<i class="nav-icon fas fa-edit"></i>
                            <input type="text" class="form-control camposTabla_dinero" placeholder="$ 000.000" name="nomina_cantidad" id="nominaCantidad" required>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="link" value="<?php echo $link ?>">
                <div class="card-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary btn-lg" name="guardar">Guardar</button>
                    <a role="button" href="<?php echo $url_base;?>secciones/<?php echo $lista_proveedor_link;?>" class="btn btn-danger btn-lg">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    span.select2-selection.select2-selection--single{
        height: 38px;
    }
</style>
<?php include("../templates/footer.php") ?>