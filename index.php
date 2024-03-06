<?php 
session_start();
$url_base = "http://localhost/inventariocloud/";

include("db.php");


if (!isset($_SESSION['usuario_usuario'])) {
  header("Location:".$url_base."login.php");
}


$sentencia=$conexion->prepare("SELECT COUNT(*) as total_usuarios FROM usuario");
$sentencia->execute();
$contardor_usuario=$sentencia->fetchAll(PDO::FETCH_ASSOC);


$sentencia=$conexion->prepare("SELECT COUNT(*) as total_producto FROM producto");
$sentencia->execute();
$contardor_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC); 

$sentencia=$conexion->prepare("SELECT COUNT(*) as total_ventas FROM venta");
$sentencia->execute();
$contardor_ventas =$sentencia->fetchAll(PDO::FETCH_ASSOC); 


// consulta para el dinero de la caja que inicio secion 
//$_SESSION['usuario_id']
$usuario_sesion = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id']  : 0;

$sentencia=$conexion->prepare("SELECT * FROM usuario
INNER JOIN caja ON usuario.caja_id = caja.caja_id 
WHERE usuario.usuario_id = :usuario_id;");
$sentencia->bindParam(":usuario_id", $usuario_sesion);
$sentencia->execute();
$total_dinero_caja =$sentencia->fetchAll(PDO::FETCH_ASSOC);


if ($_POST) {

  $rad_factura = isset($_POST['rad_factura']) ? $_POST['rad_factura'] : "";

  $sentencia=$conexion->prepare("SELECT * from venta where venta_codigo = :venta_codigo");
  $sentencia->bindParam(":venta_codigo", $rad_factura);
  $sentencia->execute();
  $rad_factura_list =$sentencia->fetchAll(PDO::FETCH_ASSOC);

  $dato = $rad_factura_list[0]['venta_id'];

  if (isset($dato)) {
    header("Location: http://localhost/inventariocloud/secciones/ventas/detalles.php?txtID=".$dato);
  }else{
    header("Location: http://localhost/inventariocloud/");
  }

}



?>
<?php include("templates/header.php") ?>

    <!-- Small boxes (Stat box) -->
    <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo (isset( $contardor_producto[0]['total_producto']))?$contardor_producto[0]['total_producto']: " ";?></h3>

                <p>Productos</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>  $ <?php echo  (isset($total_dinero_caja[0]['caja_efectivo']))?$total_dinero_caja[0]['caja_efectivo']: "Nah" ;?>
                  <!--<sup style="font-size: 20px">%</sup>--></h3>
                <p>Tota de dinero en la caja </p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="<?php echo $url_base;?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo  (isset($contardor_usuario[0]['total_usuarios']))?$contardor_usuario[0]['total_usuarios']: "" ;?></h3>
                <p>Total de usuario</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo (isset( $contardor_ventas[0]['total_ventas']))?$contardor_ventas[0]['total_ventas']: " ";?></h3>

                <p>Ventas del dia</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>


          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>Facturas</h3>

                <form action="" method="post" class="text-center">
                    <input type="text" class="form-control " name="rad_factura" id="rad_factura" >
                  </div>
                  <!-- <button type="submit" class="btn btn-primary btn-lg" id="guardar" name="guardar">Guardar</button>-->
                    <button  class="btn btn-primary text-center" id="guardar" name="guardar" type="submit">Ir a la factura</button>  
                    
                </div>
              </form>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
        <!--   Left col -->
          <section class="col-lg-7 connectedSortable">      

            <!-- TO DO List -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ion ion-clipboard mr-1"></i>
                  To Do List
                </h3>

                <div class="card-tools">
                  <ul class="pagination pagination-sm">
                    <li class="page-item"><a href="#" class="page-link">&laquo;</a></li>
                    <li class="page-item"><a href="#" class="page-link">1</a></li>
                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                    <li class="page-item"><a href="#" class="page-link">3</a></li>
                    <li class="page-item"><a href="#" class="page-link">&raquo;</a></li>
                  </ul>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list">
                  <li>
                    <!-- drag handle -->
                    <span class="handle">
                      <i class="fas fa-ellipsis-v"></i>
                      <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <!-- checkbox -->
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" value="" name="todo1" id="todoCheck1">
                      <label for="todoCheck1"></label>
                    </div>
                    <!-- todo text -->
                    <span class="text">Design a nice theme</span>
                    <!-- Emphasis label -->
                    <small class="badge badge-danger"><i class="far fa-clock"></i> 2 mins</small>
                    <!-- General tools such as edit or delete-->
                    <div class="tools">
                      <i class="fas fa-edit"></i>
                      <i class="fas fa-trash-o"></i>
                    </div>
                  </li>
                  <li>
                    <span class="handle">
                      <i class="fas fa-ellipsis-v"></i>
                      <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" value="" name="todo2" id="todoCheck2" checked>
                      <label for="todoCheck2"></label>
                    </div>
                    <span class="text">Make the theme responsive</span>
                    <small class="badge badge-info"><i class="far fa-clock"></i> 4 hours</small>
                    <div class="tools">
                      <i class="fas fa-edit"></i>
                      <i class="fas fa-trash-o"></i>
                    </div>
                  </li>
                  <li>
                    <span class="handle">
                      <i class="fas fa-ellipsis-v"></i>
                      <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" value="" name="todo3" id="todoCheck3">
                      <label for="todoCheck3"></label>
                    </div>
                    <span class="text">Let theme shine like a star</span>
                    <small class="badge badge-warning"><i class="far fa-clock"></i> 1 day</small>
                    <div class="tools">
                      <i class="fas fa-edit"></i>
                      <i class="fas fa-trash-o"></i>
                    </div>
                  </li>
                  <li>
                    <span class="handle">
                      <i class="fas fa-ellipsis-v"></i>
                      <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" value="" name="todo4" id="todoCheck4">
                      <label for="todoCheck4"></label>
                    </div>
                    <span class="text">Let theme shine like a star</span>
                    <small class="badge badge-success"><i class="far fa-clock"></i> 3 days</small>
                    <div class="tools">
                      <i class="fas fa-edit"></i>
                      <i class="fas fa-trash-o"></i>
                    </div>
                  </li>
                  <li>
                    <span class="handle">
                      <i class="fas fa-ellipsis-v"></i>
                      <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" value="" name="todo5" id="todoCheck5">
                      <label for="todoCheck5"></label>
                    </div>
                    <span class="text">Check your messages and notifications</span>
                    <small class="badge badge-primary"><i class="far fa-clock"></i> 1 week</small>
                    <div class="tools">
                      <i class="fas fa-edit"></i>
                      <i class="fas fa-trash-o"></i>
                    </div>
                  </li>
                  <li>
                    <span class="handle">
                      <i class="fas fa-ellipsis-v"></i>
                      <i class="fas fa-ellipsis-v"></i>
                    </span>
                    <div  class="icheck-primary d-inline ml-2">
                      <input type="checkbox" value="" name="todo6" id="todoCheck6">
                      <label for="todoCheck6"></label>
                    </div>
                    <span class="text">Let theme shine like a star</span>
                    <small class="badge badge-secondary"><i class="far fa-clock"></i> 1 month</small>
                    <div class="tools">
                      <i class="fas fa-edit"></i>
                      <i class="fas fa-trash-o"></i>
                    </div>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <button type="button" class="btn btn-primary float-right"><i class="fas fa-plus"></i> Add item</button>
              </div>
            </div>
            <!-- /.card -->

             <!-- Calendar -->
             <div class="card bg-gradient-success">
              <div class="card-header border-0">

                <h3 class="card-title">
                  <i class="far fa-calendar-alt"></i>
                  Calendar
                </h3>
                <!-- tools card -->
                <div class="card-tools">
                  <!-- button with a dropdown -->
                  <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                      <i class="fas fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" role="menu">
                      <a href="#" class="dropdown-item">Add new event</a>
                      <a href="#" class="dropdown-item">Clear events</a>
                      <div class="dropdown-divider"></div>
                      <a href="#" class="dropdown-item">View calendar</a>
                    </div>
                  </div>
                  <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <!-- /. tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body pt-0">
                <!--The calendar -->
                <div id="calendar" style="width: 100%"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
          <!-- /.Left col -->


        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable">           
           <!--graficos mapa -->
          <div id="sparkline-1" style="display:none"></div>               
          <div id="sparkline-2" style="display:none"></div>                
          <div id="sparkline-3" style="display:none"></div>
                
             

              
           


        </section>
        <!-- right col -->
  </div>
  <!-- /.row (main row) -->
      
<?php include("templates/footer.php") ?>