<?php include("../templates/header.php") ?>
<?php 
if(isset($_GET['banco'])){
    $banco = $_GET['banco'];
    switch($banco) {
        case 'Efectivo':
            //echo 'Soy Efectivo';
            $sqlEfectivo = $conexion->prepare("SELECT Efectivo FROM dtpmp ");
            $sqlEfectivo->execute();
            $DineroBanco = $sqlEfectivo->fetch(PDO::FETCH_ASSOC)['Efectivo'];
            break;
        case 'Davivienda':
           // echo 'Soy Davivienda';
            $sqlEfectivo = $conexion->prepare("SELECT Davivienda FROM dtpmp ");
            $sqlEfectivo->execute();
            $DineroBanco = $sqlEfectivo->fetch(PDO::FETCH_ASSOC)['Davivienda'];
            break;
        case 'Bancolombia':
           // echo 'Soy Bancolombia';
            $sqlEfectivo = $conexion->prepare("SELECT Bancolombia FROM dtpmp ");
            $sqlEfectivo->execute();
            $DineroBanco = $sqlEfectivo->fetch(PDO::FETCH_ASSOC)['Bancolombia'];
            break;
        case 'Nequi':
           // echo 'Soy Nequi';
            $sqlEfectivo = $conexion->prepare("SELECT Nequi FROM dtpmp ");
            $sqlEfectivo->execute();
            $DineroBanco = $sqlEfectivo->fetch(PDO::FETCH_ASSOC)['Nequi'];
            break;
        default:
            echo 'Banco no reconocido';
    }
}

if ($_POST) {
    $DineroBanco= isset($_POST['banco_efectivo_ingreso']) ? $_POST['banco_efectivo_ingreso'] : "";    
    // Eliminar el signo "$" y el separador de miles "," del valor del campo de entrada
    $DineroBanco = str_replace(array('$','.', ','), '', $DineroBanco);

    $banco = $_GET['banco'];
    switch($banco) {
        case 'Efectivo':
           // echo 'Soy update Efectivo';
            $banco_edit = $conexion->prepare("UPDATE dtpmp SET efectivo=efectivo+:DineroBanco");
            $banco_edit->bindParam(":DineroBanco", $DineroBanco); 
            break;
        case 'Davivienda':
           // echo 'Soy update Davivienda';
            $banco_edit = $conexion->prepare("UPDATE dtpmp SET davivienda=davivienda+:DineroBanco");
            $banco_edit->bindParam(":DineroBanco", $DineroBanco); 
            break;
        case 'Bancolombia':
           // echo 'Soy update Bancolombia';
            $banco_edit = $conexion->prepare("UPDATE dtpmp SET bancolombia=bancolombia+:DineroBanco");
            $banco_edit->bindParam(":DineroBanco", $DineroBanco); 
            break;
        case 'Nequi':
           // echo 'Soy update Nequi';
            $banco_edit = $conexion->prepare("UPDATE dtpmp SET nequi=nequi+:DineroBanco");
            $banco_edit->bindParam(":DineroBanco", $DineroBanco); 
            break;
        default:
            echo 'Banco no reconocido';
    } $resultado_edit = $banco_edit->execute();

    if ($resultado_edit) {
        echo '<script>
        Swal.fire({
            title: "¡Banco Actualizado Exitosamente!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        }).then((result)=>{
            if(result.isConfirmed){
                window.location.href = "configurar_bancos.php";
            }
        })
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Actualizar el Banco",
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
        <h3 class="card-title textTabla">EDITAR BANCO</h3>
    </div>
    <form action="" method="POST" id="formBancos">
        <div class="card-body">
            <div class="row" style="justify-content:center">                    
                <input type="hidden" class="textLabel" name="txtID" id="txtID" value="<?php echo $id;?>">                                                    
                    
                <div class="col-sm-2">
                    <br>
                    <div class="form-group">
                        <input type="text" class="form-control camposTabla" readonly name="banco_nombre" value="<?php echo $banco;?>">
                    </div>                                
                </div>
                <div class="col-sm-2">
                    <br>
                    <div class="form-group">
                        <input type="text" class="form-control camposTabla camposTabla_dinero" readonly name="banco_efectivo" id="bancoEfectivo_edit" value="<?php echo '$' . number_format($DineroBanco, 0, '.', ','); ?>">
                    </div>                                                               
                </div>                
            </div>
             <div class="row" style="justify-content:center">  
            <div class="col-sm-4">
                    <br>
                    <div class="form-group">
                        <input type="text" class="form-control camposTabla camposTabla_dinero" name="banco_efectivo_ingreso" id="bancoEfectivo_edit" placeholder="Ingrese Cantidad">
                    </div>                                                               
                </div>
            </div>
        </div>
        <div class="card-footer" style="text-align:center">
            <button type="submit" class="btn btn-primary btn-lg">Ingresar</button>
                
        </div>
    </form>
</div>
<?php include("../templates/footer.php") ?>
