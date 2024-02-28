
<?php 
session_start();
$url_base = "http://localhost/inventariocloud/";

if (!isset($_SESSION['usuario_usuario'])) {
    header("Location:".$url_base."login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventario | Cloud</title>
  <link rel="icon" type="image/x-icon" href="../../dist/img/logos/logo_nube.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../../plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.min.css">
  <!-- Estilos Personalizados -->
  <link rel="stylesheet" href="../../dist/css/estilos_content.css">
  <!-- Agrega estos enlaces en la sección head de tu HTML -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

  <!-- DataTables -->
  <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

</head>
<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">
    <!-- Preloader -->
    <!-- <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="../../dist/img/logo_nube.png" alt="AdminLTELogo" height="60" width="80">
    </div> -->

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item">
        <a href="http://localhost/inventariocloud/" class="nav-link" style="background: #dc5bf3; color: white; border-radius: 17px ">Inicio</a>
        
      </li>
      <li>
        <a href="<?php echo $url_base;?>cerrar.php" style="background: #17A2B8; border-radius: 17px; font-size: 15px;
          color: white;margin-left: 10px;" class="nav-link">Cerrar Sesion</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="../../dist/img/logos/logo_nube.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Inventario Cloud</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block h5"><?php echo $_SESSION['usuario_usuario']?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <li class="nav-item">
              <a href="../../index.php" class="nav-link active">
                <i class="far fa-circle nav-icon"></i>
                <p>Panel de control</p>
              </a>
            </li>
          </li>
          
        <!-- SECCIÓN DE VENTAS -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cart-plus fa-lg mr-2"></i>
              <p>
                VENTAS
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/ventas/crear.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Nueva venta</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/ventas/" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Historial de Ventas</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- SECCIÓN DE CLIENTES -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-address-book fa-lg mr-2"></i>
              <p>
                CLIENTES
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/clientes/crear.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Cliente</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/clientes/index.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Lista de Clientes</p>
                </a>
              </li>                           
            </ul>
          </li>

        <!-- SECCIÓN DE PRODUCTO -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-shopping-basket fa-lg mr-2"></i>
              <p>
                PRODUCTOS
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/productos/crear_categoria.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Categoria</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/productos/crear.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Productos</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/productos/" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Lista de Productos</p>
                </a>
              </li>              
            </ul>
          </li>
          
          <?php if ($_SESSION['rolEmpleado']) { ?>
        <!-- SECCIÓN DE CAJAS -->
          <li class="nav-item">
            <a href="#" class="nav-link">
            <i class="nav-icon fas fa-cash-register fa-lg mr-2"></i>
              <p>
                CAJAS
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">              
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/cajas/crear.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Caja</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/cajas/" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Lista de Caja</p>
                </a>
              </li>
            </ul>
          </li>
          
          <!-- SECCIÓN DE USUARIO -->
          <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-users fa-lg mr-2"></i>
                <p>
                  USUARIO
                  <i class="fas fa-angle-left right"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview">              
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/empleados/crear.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear usuario</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/empleados/" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Usuario</p>
                  </a>
                </li>
              </ul>
            </li>
          <?php } ?>


          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-edit"></i>
              <p>
                Forms
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pages/forms/general.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>General Elements</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/forms/advanced.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Advanced Elements</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/forms/editors.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Editors</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../../pages/forms/validation.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Validation</p>
                </a>
              </li>
            </ul>
          </li> 
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
        