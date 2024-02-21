<?php 
include("../../db.php");

if(isset($_GET['txtID'])){

    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";

    $sentencia=$conexion->prepare("SELECT * FROM producto WHERE producto_id=:producto_id");
    $sentencia->bindParam(":producto_id",$txtID);
    $sentencia->execute();
    $registro=$sentencia->fetch(PDO::FETCH_LAZY);
    $producto_codigo=$registro["producto_codigo"];
    $producto_nombre=$registro["producto_nombre"];
    $producto_precio_compra=$registro["producto_precio_compra"];  
    $producto_precio_venta=$registro["producto_precio_venta"];  
    $producto_stock_total=$registro["producto_stock_total"];  
    $producto_marca=$registro["producto_marca"];  
    $producto_modelo=$registro["producto_modelo"];  
    // agregar categoria $producto_nombre=$registro["producto_nombre"];     
}

if($_POST){
    print_r($_POST);

    //recolectamos los datos del metodo POST    
    $txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
    $producto_codigo=(isset($_POST["producto_codigo"])?$_POST["producto_codigo"]:"");
    $producto_nombre=(isset($_POST["producto_nombre"])?$_POST["producto_nombre"]:"");
    $producto_precio_compra=(isset($_POST["producto_precio_compra"])?$_POST["producto_precio_compra"]:"");
    $producto_precio_venta=(isset($_POST["producto_precio_venta"])?$_POST["producto_precio_venta"]:"");
    $producto_stock_total=(isset($_POST["producto_stock_total"])?$_POST["producto_stock_total"]:"");
    $producto_marca=(isset($_POST["producto_marca"])?$_POST["producto_marca"]:"");    
    $producto_modelo=(isset($_POST["producto_modelo"])?$_POST["producto_modelo"]:"");

    
    // preparar la inserccion de los datos

    $sentencia=$conexion->prepare("UPDATE producto 
    SET producto_codigo=:producto_codigo,
    producto_nombre=:producto_nombre,
    producto_precio_compra=:producto_precio_compra,
    producto_precio_venta=:producto_precio_venta,
    producto_stock_total=:producto_stock_total,
    producto_marca=:producto_marca,
    producto_modelo=:producto_modelo
    WHERE producto_id=:producto_id");       

     
    
    //asignando los valores que vienen del metodo POST (los q ue vienen del formulario)
    $sentencia->bindParam(":producto_codigo",$producto_codigo);
    $sentencia->bindParam(":producto_nombre",$producto_nombre);
    $sentencia->bindParam(":producto_precio_compra",$producto_precio_compra);
    $sentencia->bindParam(":producto_precio_venta",$producto_precio_venta);
    $sentencia->bindParam(":producto_stock_total",$producto_stock_total);
    $sentencia->bindParam(":producto_marca",$producto_marca);
    $sentencia->bindParam(":producto_modelo",$producto_modelo);
    $sentencia->bindParam(":producto_id",$txtID);   

    $resultado = $sentencia->execute();
    if ($resultado) {
        echo '<script>
        Swal.fire({
            title: "¡Editado Correctamente!!",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }else {
        echo '<script>
        Swal.fire({
            title: "Error al Editar",
            icon: "error",
            confirmButtonText: "¡Entendido!"
        });
        </script>';
    }
}
?>

<?php include("../../templates/header_content.php") ?>

<br>

          <!-- left column -->
          <div class="">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">REGISTRE EL NUEVO PRODUCTO</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form action="" method="post" enctype="multipart/form-data">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Nombre del Producto</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" 
                                class="form-control" 
                                 id="exampleInputEmail1" 
                                name="producto_nombre"
                                id="producto_nombre"
                                value="<?php echo $producto_nombre;?>"
                                >
                                
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Codigo de Barra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-success">
                                    Escanear Codigo
                                </button>
                                <input type="text" 
                                class="form-control" 
                                 id="result"
                                name="producto_codigo"
                                id="producto_codigo"
                                value="<?php echo $producto_codigo;?>"
                                >
                                <div class="modal fade" id="modal-success">
                                    <div class="modal-dialog">
                                    <div class="modal-content bg-success" style="width: 115%;">
                                        <div class="modal-header" style="text-align:center">
                                            <h4 class="modal-title">Escanear Codigo</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                        <!-- <div id="barcode"> -->
                                            <video id="barcodevideo" autoplay></video>
                                            <canvas id="barcodecanvasg" ></canvas>
                                        <!-- </div> -->
                                        <canvas id="barcodecanvas"></canvas>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-outline-light" data-dismiss="modal">Cerrar</button>
                                            <button type="button" class="btn btn-outline-light">Siguiente</button>
                                        </div>
                                    </div>
                                <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Categoria</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <div class="form-group">
                                <select class="form-control select2" style="width: 100%;">
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
                                <label for="exampleInputEmail1">Precio de Compra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" 
                                    class="form-control" 
                                    placeholder="000.000" 
                                     id="exampleInputEmail1" 
                                    name="producto_precio_compra"
                                    id="producto_precio_compra"
                                    value="<?php echo $producto_precio_compra;?>">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Precio de Venta</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" 
                                class="form-control"
                                placeholder="000.000"
                                 id="exampleInputEmail1"
                                name="producto_precio_venta"
                                id="producto_precio_venta"
                                value="<?php echo $producto_precio_venta;?>">
                                  

                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Stock o Existencias</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" 
                                class="form-control" 
                                 id="exampleInputEmail1"
                                name="producto_stock_total"
                                id="producto_stock_total"
                                value="<?php echo $producto_stock_total;?>"
                                >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Marca</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" 
                                class="form-control" 
                                id="exampleInputEmail1"
                                name="producto_marca"
                                id="producto_marca"
                                value="<?php echo $producto_marca;?>"
                                >
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Modelo</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="num" 
                                class="form-control" 
                                id="exampleInputEmail1"
                                name="producto_modelo"
                                id="producto_modelo"
                                value="<?php echo $producto_modelo;?>">
                            </div>
                        </div>
                        
                    </div>
                  <!-- <div class="form-group col-4">
                    <label for="exampleInputPassword1">Codigo de Barra</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                  </div> -->
                  <!-- <div class="form-group">
                    <label for="exampleInputFile">File input</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text">Upload</span>
                      </div>
                    </div>
                  </div> -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="text-align:center">
                
                <button
                 type="submit"
                 class="btn btn-success">
                 Actualizar
                </button>

                 <a            
                 class="btn btn-danger"
                href="index.php"
                role="button"
                >Cancelar</a>

                </div>
              </form>
            </div>
            <!-- /.card -->
          </div>



<?php include("../../templates/footer_content.php") ?>