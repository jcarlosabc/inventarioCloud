<?php include("../../templates/header_content.php") ?>
<?php 
include("../../db.php");

//Eliminar Elementos
if(isset($_GET['txtID'])){

  $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

  $sentencia=$conexion->prepare("DELETE FROM producto WHERE producto_id=:producto_id");
  $sentencia->bindParam(":producto_id",$txtID);
  $sentencia->execute();
  header("Location:index.php");
}


$sentencia=$conexion->prepare("SELECT * FROM `producto`");
$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
        <br>
            <!-- left column -->
            <div class="">
                <!-- general form elements -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title textTabla" >REALIZAR VENTA</h3>
                    </div>
                <!-- form start --> 
                <form>
                    <div class="card-body ">
                        <div class="card card-info">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="vBuscar" class="table table-bordered table-striped" style="text-align:center">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Nombre</th>
                                            <th>Existencias</th>
                                            <th>Precio</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Escoger</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lista_producto as $registro) {?>
                                            <tr class="">
                                                <td scope="row"><?php echo $registro['producto_codigo']; ?></td>
                                                <td><?php echo $registro['producto_nombre']; ?></td>
                                                <td><?php echo $registro['producto_stock_total']; ?></td>
                                                <td><?php echo $registro['producto_precio_venta']; ?></td>
                                                <td><?php echo $registro['producto_marca']; ?></td>
                                                <td><?php echo $registro['producto_modelo']; ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                    <a class="btn btn-info" href="editar.php?txtID=<?php echo $registro['producto_id']; ?>" role="button">Escoger <i class="fas fa-chevron-right"></i></a>
                                                    </div>
                                                </td>
                                            </tr>  
                                        <?php } ?>
                                    </tbody>                  
                                </table>
                            </div>
                             <!-- /.card-body -->
                        </div>
                        <div class="row">

                            <div class="card card-success">
                              <div class="card-header">
                                <h3 class="card-title">PRODUCTOS A COMPRAR</h3>
                              </div>
                              <!-- /.card-header -->
                              <div class="card-body">
                                <table class="table table-bordered table-striped" style="text-align:center">
                                  <thead>
                                  <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>x</th>
                                    <th>Precio</th>
                                    <th>=</th>
                                    <th>SubTotal</th>
                                    <th>Remover</th>
                                    
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <tr>
                                    <td>77888</td>
                                    <td>Mouse</td>
                                    <td style="font-size: 24px;">2</td>
                                    <td style="font-size: 24px;">x</td>
                                    <td style="font-size: 24px;">30.000</td>
                                    <td style="font-size: 24px;">=</td>
                                    <td style="font-size: 24px;color:#24d124;font-weight: 800;">60.000</td>
                                    <td>
                                        <a class="btn btn-danger" href="index.php?txtID=<?php echo $registro['producto_id']; ?>" role="button">Eliminar</a>
                                    </td>
                                  </tr>
                                  </tbody>
                                </table>
                              </div>
                              <!-- /.card-body -->
                            </div>
                        </div>

                    <!-- <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Nombre del Producto</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" required >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Categoria</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <div class="form-group">
                                <select class="form-control select2 camposTabla" style="width: 100%;">
                                    <option selected="selected">Alabama</option>
                                    <option>Alaska</option>
                                    <option>California</option>
                                    <option>Delaware</option>
                                    <option>Tennessee</option>
                                    <option>Texas</option>
                                    <option>Washington</option>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control camposTabla_dinero" placeholder="000.000" required >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Precio de Venta</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control camposTabla_dinero" placeholder="000.000" required >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Stock o Existencias</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control camposTabla_stock" required >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Marca</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control camposTabla" >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" class="form-control camposTabla" >
                            </div>
                        </div>
                        
                    </div> -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                  <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>


<?php include("../../templates/footer_content.php") ?>