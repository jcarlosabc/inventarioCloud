<?php include("../../templates/header_content.php") ?>
<br>

          <!-- left column -->
          <div class="">
            <!-- general form elements -->
            <div class="card card-primary" style="margin-top:7%">
              <div class="card-header">
                <h3 class="card-title textTabla" >REGISTRE EL NUEVO PRODUCTO</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form>
                <div class="card-body ">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Nombre del Producto</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <input type="text" class="form-control camposTabla" required >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="exampleInputEmail1" class="textLabel">Codigo de Barra</label> &nbsp;<i class="nav-icon fas fa-edit"></i> 
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-default">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <input type="text" class="form-control camposTabla" required>
                                <div class="modal fade" id="modal-default">
                                    <div class="modal-dialog">
                                    <div class="modal-content bg-default" style="width: 115%;">
                                        <div class="modal-header" style="text-align:center">
                                            <h4 class="modal-title">Escanear Codigo</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="barcode">
                                                <video id="barcodevideo" autoplay></video>
                                                <canvas id="barcodecanvasg" ></canvas>
                                            </div>
                                            <canvas id="barcodecanvas" ></canvas>
                                            <div id="result"></div> 
                                            <a class="btn btn-app" id="miBoton" onclick="copiarContenido()">
                                                <i class="far fa-clipboard"></i> Copiar
                                            </a>
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
                        
                    </div>
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