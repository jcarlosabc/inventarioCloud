<?php 

    session_start();
    include("../db.php");
    $url_base = "http://localhost/inventariocloud/";
    
    $sentencia=$conexion->prepare("SELECT empresa_logo, empresa_nombre FROM empresa LIMIT 1 ");
    $sentencia->execute();
    $lista_empresa=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

    $logo_empresa = isset($lista_empresa[0]['empresa_logo'])? $lista_empresa[0]['empresa_logo']:'';
    $nombre_empresa = isset($lista_empresa[0]['empresa_nombre'])? $lista_empresa[0]['empresa_nombre'] : '';

    if (!isset($_SESSION['usuario_usuario'])) {
      header("Location:".$url_base."login.php");
    }

    date_default_timezone_set('America/Bogota'); 
    $fechaActual = date("d-m-Y");
    $horaActual = date("h:i a");

    $valSudoAdmin = $_SESSION['valSudoAdmin'];
    if (isset($_SESSION['link'])) {
      $link = $_SESSION['link'];
    }else {
      $link ="";
    }

    if ($valSudoAdmin) {
      $inicio_link = "index.php";
    //SECCIÓN DE VENTAS
      $ventas_link = "crear_venta.php";
      $ventas_detalles_link = "detalles.php";
      $ventas_link_historia_venta = "index_ventas.php";
    //SECCIÓN DE PRODUCTOS
      $crear_categoria_link = 'crear_categoria.php';
      $lista_categoria_link = 'lista_categoria.php';
      $crear_producto_link = 'crear_producto.php';
      $lista_producto_link = 'index_productos.php';
      $editar_producto_link = 'editar_productos.php';
    //SECCIÓN DE CLIENTES
      $crear_cliente_link = 'crear_cliente.php';
      $lista_cliente_link = 'index_clientes.php';
      $editar_cliente_link = 'editar_clientes.php';
    //SECCIÓN DE PROVEEDORES
      $crear_proveedore_link = 'crear_proveedor.php';
      $lista_proveedore_link = 'index_proveedores.php';
    //SECCIÓN DE CAJAS
      $crear_caja_link = 'crear_caja.php';
      $index_cajas_link = 'index_cajas.php';
    //SECCIÓN DE USUARIO
      $crear_empleado_link = 'crear_empleado.php'; 
      $index_empleados_link = 'index_empleados.php'; 
    //GASTOS 
      $crear_gasto_link = 'crear_gastos.php';
      $index_gastos_link = 'index_gastos.php';
    //DEVOLUCIONES
      $index_devoluciones_link = 'index_devoluciones.php';
    //BODEGA
    $producto_bodega_link = 'producto_bodega.php';
    //SECCIÓN CREDITOS
      $index_pendientes_link = 'index_pendientes.php';


    } else {
      $inicio_link = "index_estadisticas.php?link=".$link;
    //SECCIÓN DE VENTAS
      $ventas_link = "crear_venta.php?link=".$link;
      $ventas_detalles_link = "detalles.php?link=".$link;
      $ventas_link_historia_venta = "index_ventas.php?link=".$link;
    //SECCIÓN DE PRODUCTOS
      $crear_categoria_link = 'crear_categoria.php?link='.$link;
      $lista_categoria_link = 'lista_categoria.php?link='.$link;
      $crear_producto_link = 'crear_producto.php?link='.$link;
      $lista_producto_link ='index_productos.php?link='.$link;
      $editar_producto_link ='editar_productos.php?link='.$link;
    //SECCIÓN DE CLIENTES
      $crear_cliente_link = 'crear_cliente.php?link='.$link;
      $lista_cliente_link = 'index_clientes.php?link='.$link;
      $editar_cliente_link = 'editar_clientes.php?link='.$link;
    //SECCIÓN DE PROVEEDORES
      $crear_proveedore_link = 'crear_proveedor.php?link='.$link;
      $lista_proveedore_link = 'index_proveedores.php?link='.$link;
    //SECCIÓN DE CAJAS
      $crear_caja_link = 'crear_caja.php?link='.$link;               
      $index_cajas_link = 'index_cajas.php?link='.$link; 
      $asignar_caja_link = 'asignar_caja.php?link='.$link; 
    //SECCIÓN DE USUARIO
      $crear_empleado_link = 'crear_empleado.php?link='.$link; 
      $index_empleados_link = 'index_empleados.php?link='.$link;
    //GASTOS 
      $crear_gasto_link = 'crear_gastos.php?link='.$link;
      $index_gastos_link = 'index_gastos.php?link='.$link;
    //DEVOLUCIONES
      $index_devoluciones_link = 'index_devoluciones.php?link='.$link;
    //SECCIÓN CREDITOS
      $index_pendientes_link = 'index_pendientes.php?link='.$link;
  }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventario Cloud</title>
  <link rel="icon" type="image/x-icon" href="../dist/img/logos/logo_nube.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
  <!-- ajax -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- Estilos Personalizados -->
  <link rel="stylesheet" href="../dist/css/custom_content.css">
  <!-- sweetalert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php if ($_SESSION['logueado']) { ?>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="<?php echo $url_base;?>secciones/<?php echo $inicio_link;?>" class="nav-link">Inicio</a>
        </li>
        <li>
          <?php if ($valSudoAdmin) { ?>
            <a id="cerrarSesion" href="<?php echo $url_base;?>cerrar.php" class="nav-link" style="background: #17A2B8; border-radius: 17px;font-size: 15px;
              color: white;">Cerrar Sesion</a>
          <?php } else { ?>
            <a id="cerrarSesion" href="<?php echo $url_base;?>cerrar.php?link=<?php echo $link; ?>" class="nav-link" style="background: #17A2B8; border-radius: 17px;font-size: 15px;
              color: white;">Cerrar Sesion</a>
          <?php } ?>

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
      <!-- <a href="#" class="brand-link">
        <img src="<?php echo "data:image/png;base64,".$logo_empresa;?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"><?php echo $nombre_empresa;?></span>
      </a> -->
    
      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="../dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block h5" ><?php echo $_SESSION['usuario_usuario']?></a>
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
              <?php if($valSudoAdmin){ ?>
              <li class="nav-item menu-open">
                <li class="nav-item">
                  <a href="index.php" class="nav-link active">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Panel de Control</p>
                  </a>
                </li>
              </li>
             <?php } ?>
            <li class="nav-item menu-open">
              <li class="nav-item" >
              <a href=<?php echo $_SESSION['valSudoAdmin'] ? "index_estadisticas.php" : "index_estadisticas.php?link=".$link ?>  class="nav-link active" style="background:#0f9b6e;">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Panel de Estadística</p>
                </a>
              </li>
            </li>
  
            <!-- SECCIÓN DE VENTAS -->
            <!-- Permisos => sudo admin(historial ventas) | admin local | empleado  -->
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-cart-plus fa-lg mr-2"></i>
                <p>
                  VENTAS
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
              <?php if (!$_SESSION['rolSudoAdmin']) { ?>
                <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/<?php echo $ventas_link; ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Nueva venta</p>
                  </a>
                </li>
                <?php } ?>
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $ventas_link_historia_venta;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Historial de Ventas</p>
                  </a>
                </li>
              </ul>
            </li>
  
          <!-- SECCIÓN DE PRODUCTOS --> 
          <!-- Permisos => sudo admin | admin local  -->
          <?php if (!$_SESSION['rolUserEmpleado']) { ?>
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
                  <a href="<?php echo $url_base;?>secciones/<?php echo $crear_categoria_link?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear Categoria</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $lista_categoria_link?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Categorias</p>
                  </a>
                </li> 
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $crear_producto_link?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear Producto</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $lista_producto_link?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Productos</p>
                  </a>
                </li>              
              </ul>
            </li>
          <?php } ?> 

          <!-- SECCIÓN DE CLIENTES -->
          <!-- Permisos => sudo admin | admin local | empleado -->
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
                  <a href="<?php echo $url_base;?>secciones/<?php echo $crear_cliente_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear Cliente</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $lista_cliente_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Clientes</p>
                  </a>
                </li>                          
              </ul>
            </li>
            
          <!-- PROVEEDORES -->
          <!-- Permisos => sudo admin | admin local | -->
          <?php if (!$_SESSION['rolUserEmpleado']) { ?>
            <li class="nav-item">
              <a href="#" class="nav-link">
              <i class="nav-icon fas fa-truck fa-lg mr-2"></i>
                <p>
                  PROVEEDORES  
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">              
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $crear_proveedore_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear Proveedor</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $lista_proveedore_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista Proveedores</p>
                  </a>
                </li>
              </ul>
            </li>

          <!-- SECCIÓN DE CAJAS -->
          <!-- Permisos => sudo admin | admin local | -->
            <li class="nav-item">
              <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cash-register fa-lg mr-2"></i>
                <p>
                  CAJAS
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview"> 
            <?php if ($_SESSION['roladminlocal']) { ?>
              <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $asignar_caja_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Asignar Caja</p>
                  </a>
                </li>  
              <?php } ?>
              <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $crear_caja_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear Caja</p>
                  </a>
                </li>            
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $index_cajas_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Cajas</p>
                  </a>
                </li>              
              </ul>
            </li>

          <!-- SECCIÓN DE USUARIO -->
          <!-- Permisos => sudo admin | admin local | -->
             <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa-users fa-lg mr-2"></i>
                <p>
                  EMPLEADOS
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">              
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $crear_empleado_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear Empleado</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $index_empleados_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Empleados</p>
                  </a>
                </li>
              </ul>
            </li>
          <?php } ?> 

          <!-- SECCIÓN DE PENDIENTES -->
          <!-- Permisos => sudo admin | admin local | empleado (cuentas clientes) -->
            <li class="nav-item">
              <a href="#" class="nav-link">
              <i class="nav-icon fas fa-money-bill fa-lg mr-2"></i>
                <p>
                MORAS PENDIENTES
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $index_pendientes_link; ?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cuentas Clientes</p>
                  </a>
                </li>
              </ul>
              <?php if ($_SESSION['rolSudoAdmin'] || $_SESSION['roladminlocal']) { ?>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="?php echo $url_base;?>secciones/index_pendientes.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cuentas Proveedores</p>
                  </a>
                </li>
              </ul>
              <?php } ?> 
            </li>

             <!-- CONFIGURACIÓNES -->
            <!-- <li class="nav-item">
            <a href="#" class="nav-link">
            <i class="nav-icon fas  fa-cog fa-lg mr-2"></i>
              <p>
                Configuración
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">              
              <li class="nav-item">
                <a href="php echo $url_base;?>secciones/crear_config.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Configurar Empresa</p>
                </a>
              </li>
            </ul>
          </li> -->
          
          <!-- BODEGA -->
          <!-- Permisos => sudo admin | -->
            <?php if ($_SESSION['rolSudoAdmin']){ ?>
              <li class="nav-item menu-open">
                <li class="nav-item">
                  <a href="bodega.php" class="nav-link ">
                    <i class="fa fa-archive nav-icon"></i>
                    <p>BODEGA</p>
                  </a>
                </li>
              </li>
        
          <!-- NÓMINA -->
          <!-- Permisos => sudo admin | -->
            <li class="nav-item">
              <a href="#" class="nav-link">
              <i class="fa fa-list-alt nav-icon"></i>
                  <p>NÓMINA</p>
                  <i class="fas fa-angle-left right"></i>
              </a>
              <ul class="nav nav-treeview">              
                <li class="nav-item">
                  <a href="crear_nomina.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Pagar Nómina</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">              
                <li class="nav-item">
                  <a href="nomina.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Historial Nómina</p>
                  </a>
                </li>
              </ul>
            </li>
          <!-- CONFIGURACIÓNES -->
          <!-- Permisos => sudo admin | -->
            <li class="nav-item">
              <a href="#" class="nav-link">
              <i class="nav-icon fas  fa-cog fa-lg mr-2"></i>
                <p>
                  Crear Empresa
                </p>
              </a>
              <ul class="nav nav-treeview">              
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/crear_empresa.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Crear</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">              
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/index_empresas.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Locales</p>
                  </a>
                </li>
              </ul>
            </li>
            <?php } ?>

          <!-- DEVOLUCIONES -->
          <!-- Permisos => sudo admin | admin local | empleado  -->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-retweet fa-lg mr-2"></i>
                <p>DEVOLUCIONES
                  <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">              
              <li class="nav-item">
                <a href="<?php echo $url_base;?>secciones/<?php echo $index_devoluciones_link;?>" class="nav-link">
                  <i class="far fa-circle nav-icon"></i><p>Lista Devoluciones</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- GASTOS -->
          <!-- Permisos => sudo admin | admin local | empleado  -->
          <li class="nav-item">
              <a href="#" class="nav-link">
              <i class="nav-icon fa fa-puzzle-piece fa-lg mr-2"></i>
                <p>
                  GASTOS
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">  
              <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $crear_gasto_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Registrar Gasto</p>
                  </a>
                </li>            
                <li class="nav-item">
                  <a href="<?php echo $url_base;?>secciones/<?php echo $index_gastos_link;?>" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Lista de Gastos</p>
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
          
    <?php } ?>
    
    <div class="content-wrapper" >
      <!-- Main content -->
      <section class="content">
          <div class="container-fluid">
    
        