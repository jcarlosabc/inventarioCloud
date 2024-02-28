<?php 
session_start();
if ($_POST) {
    include("db.php");

    $usuario_nombre = isset($_POST['usuario_nombre']) ? $_POST['usuario_nombre'] : "";
    $usuario_clave = hash('sha256',(isset($_POST['usuario_clave']) ? $_POST['usuario_clave'] : ""));

    $sentencia=$conexion ->prepare("SELECT *,count(*) as n_usuario 
    FROM usuario 
    WHERE usuario_nombre=:usuario_nombre AND usuario_clave=:usuario_clave");
      
    $sentencia->bindParam("usuario_nombre",$usuario_nombre);
    $sentencia->bindParam("usuario_clave",$usuario_clave);

    $sentencia->execute();
    $lista_usuario = $sentencia->fetch(PDO::FETCH_ASSOC);
    
    if ($lista_usuario["n_usuario"]>0) {
        $_SESSION['usuario_nombre']=$lista_usuario["usuario_nombre"];
        $_SESSION['usuario_id'] = $lista_usuario["usuario_id"];
        $_SESSION['logueado']=true;

        //die(print_r($_SESSION));

        header("Location:index.php");
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
  <title>Innova CLoud | Log in </title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="dist/css/estilos.css">
</head>
<body class="hold-transition login-page fondoLogin">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline">
    <img src="dist/img/InnovaCLoudLogo.png">
    <div class="card-header text-center">
      <a href="login.php" class="h2"><b>Inventario </b>Cloud</a>
    </div>
    <div class="card-body">
    <?php if (isset($mensaje)) { ?>
            <div
                class="alert alert-danger"
                role="alert"
            >
                <strong><?php echo $mensaje; ?></strong> 
            </div>
            
        
        <?php } ?>
      <p class="login-box-msg">Inicia sesión</p> 

      <form action="" method="post">
        <div class="input-group mb-3">
        <input type="text"
             class="form-control"
             name="usuario_nombre"
             id="usuario_nombre"
             aria-describedby="helpId"
            placeholder="Escriba su usuario"/>

          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password"
             class="form-control"
             name="usuario_clave"
             id="usuario_clave"
             aria-describedby="helpId"
             placeholder="Escriba su contraseña"/>

          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row text-center ">
          <!-- /.col -->
          <div class="col-12"class="" >
            <button type="submit" class="btn btn-primary btn-block" >Entrar</button>
          </div>
          <!-- /.col -->
        </div>
      </form>  
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
