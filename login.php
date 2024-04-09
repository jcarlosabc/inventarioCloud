<?php 
session_start();
include("db.php");

$valSudoAdmin = false;
if(isset($_GET['link'])){
  $link=(isset($_GET['link']))?$_GET['link']:"";


  $sentencia=$conexion->prepare("SELECT * FROM usuario WHERE link=:link");
  $sentencia->bindParam(":link",$link);
  $sentencia->execute();
  $registro=$sentencia->fetch(PDO::FETCH_LAZY);
  
  if (!$link == "sudo_bodega") {
    $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa WHERE link=:link");
    $sentencia_empresa->bindParam(":link",$link);
    $sentencia_empresa->execute();
    $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
    $empresa_login = $registro_empresa['empresa_nombre'];
  }else {
    $sentencia_empresa=$conexion->prepare("SELECT * FROM empresa_bodega WHERE link=:link");
    $sentencia_empresa->bindParam(":link",$link);
    $sentencia_empresa->execute();
    $registro_empresa=$sentencia_empresa->fetch(PDO::FETCH_LAZY);
    $empresa_login = $registro_empresa['bodega_nombre'];
  }


  $link = $registro["link"];
  $valSudoAdmin;
}else {
  $valSudoAdmin = true ;
  $empresa_login = "ADMIN";
}

if ($_POST) {

    $usuario_usuario = isset($_POST['usuario_usuario']) ? $_POST['usuario_usuario'] : "";
    $usuario_clave = hash('sha256',(isset($_POST['usuario_clave']) ? $_POST['usuario_clave'] : ""));
    $link = isset($_POST['link']) ? $_POST['link'] : "";

    if ($valSudoAdmin) {
      $linkAdmin = "sudo_admin";
      $sentencia=$conexion ->prepare("SELECT *,count(*) as n_usuario 
      FROM usuario 
      WHERE usuario_usuario=:usuario_usuario AND usuario_clave=:usuario_clave AND link =:link");
        
      $sentencia->bindParam("usuario_usuario",$usuario_usuario);
      $sentencia->bindParam("usuario_clave",$usuario_clave);
      $sentencia->bindParam("link",$linkAdmin);
    } else {
      $sentencia=$conexion ->prepare("SELECT *,count(*) as n_usuario 
      FROM usuario 
      WHERE usuario_usuario=:usuario_usuario AND usuario_clave=:usuario_clave AND link =:link");
        
      $sentencia->bindParam("usuario_usuario",$usuario_usuario);
      $sentencia->bindParam("usuario_clave",$usuario_clave);
      $sentencia->bindParam("link",$link);
    }

    $sentencia->execute();
    $lista_usuario = $sentencia->fetch(PDO::FETCH_ASSOC);
    
    if ($lista_usuario["n_usuario"]>0) {
        $_SESSION['usuario_usuario']=$lista_usuario["usuario_usuario"];
        $_SESSION['usuario_id']=$lista_usuario["usuario_id"];
        $_SESSION['rol']=$lista_usuario["rol"];

      if ($_SESSION['rol'] == 0) {
        $_SESSION['rolSudoAdmin']=true;
        $_SESSION['roladminlocal']= false;
        $_SESSION['rolUserEmpleado']= false;
      }else if($_SESSION['rol'] == 1) {
        $_SESSION['rolSudoAdmin']=false;
        $_SESSION['roladminlocal']= true;
        $_SESSION['rolUserEmpleado']= false;
      }else {
        $_SESSION['rolSudoAdmin']=false;
        $_SESSION['roladminlocal']= false;
        $_SESSION['rolUserEmpleado']= true;
      }
        $_SESSION['caja_id']=$lista_usuario["caja_id"];
        $_SESSION['logueado']=true;

        if ($valSudoAdmin) {
          $_SESSION['valSudoAdmin']= true;
        } else { 
          $_SESSION['valSudoAdmin']= false;
          $_SESSION['link'] = $link;
        }
        $valSudoAdmin ? header("Location:".$url_base."secciones/index.php")  : header("Location:".$url_base."secciones/index_estadisticas.php?link=".$link);
      }else {
        $mensaje="Error: el usuario o contraseña son incorrectos";  
      }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventario Cloud</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="dist/css/login_estilo.css">
  <link rel="icon" type="image/x-icon" href="dist/img/logos/logo_nube.png">
</head>
<body class="hold-transition login-page fondoLogin">
  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a class="h2"><b>Sistema </b><?php echo $empresa_login; ?></a>
      </div>
      <div class="card-body">
        <?php if (isset($mensaje)) { ?>
          <div class="alert alert-danger" role="alert">
            <strong><?php echo $mensaje; ?></strong> 
          </div>
        <?php } ?>
        <p class="login-box-msg">Inicia sesión</p> 
        <form action="" method="post">
          <div class="input-group mb-3">
          <input type="hidden" class="form-control" name="link" value="<?php echo $link ?>" >
            <input type="text" class="form-control" name="usuario_usuario" id="usuario_usuario" aria-describedby="helpId" placeholder="Escriba su usuario"/>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="usuario_clave" id="usuario_clave" aria-describedby="helpId" placeholder="Escriba su contraseña"/>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row text-center ">
            <div class="col-12"class="" >
              <button type="submit" class="btn btn-primary btn-block">Entrar  <i class="fa fa-paper-plane" aria-hidden="true"></i></button> 
            </div>
          </div>
        </form>  
      </div>
    </div>
  </div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
