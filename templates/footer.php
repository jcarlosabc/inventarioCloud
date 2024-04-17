  
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
 <!-- /.content-wrapper -->
 <footer class="main-footer">
    <strong>Copyright &copy; <a href="https://www.instagram.com/innova.cloud1?igsh=M2R2eWcwdjkzYmhn" target="_blank">Innova Cloud</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0.0
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Sparkline -->
<script src="../plugins/sparklines/sparkline.js"></script>
<!-- jQuery Knob Chart -->
<script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../plugins/moment/moment.min.js"></script>
<script src="../plugins/daterangepicker/daterangepicker.js"></script>
<script src="../plugins/moment/locales.js"></script>
<script src="../plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../dist/js/pages/dashboard.js"></script>
<!-- barcode -->
<script type="text/javascript" src="../dist/js/barcode.js"></script>
<!-- DataTables  & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../plugins/jszip/jszip.min.js"></script>
<script src="../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="../plugins/select2/js/select2.full.min.js"></script>
<!-- dropzonejs -->
<script src="../plugins/dropzone/min/dropzone.min.js"></script>

<script>
  // Escaneo de código de barras
  $.widget.bridge('uibutton', $.ui.button);
  var sound = new Audio("../dist/sound/barcode.wav");
  $(document).ready(function() {
    barcode.config.start = 0.1;
    barcode.config.end = 0.9;
    barcode.config.video = '#barcodevideo';
    barcode.config.canvas = '#barcodecanvas';
    barcode.config.canvasg = '#barcodecanvasg';
    barcode.setHandler(function(barcode) {
      $('#result').html(barcode);
    });
    barcode.init();

    $('#result').bind('DOMSubtreeModified', function(e) {
      sound.play();	
    });
});
// Función para realizar el copiado 
  function copiarContenido() {
    var contenido = document.getElementById('result').innerText;
    var elementoTemporal = document.createElement('textarea');
    elementoTemporal.value = contenido;
    // Añade el elemento temporal al documento
    document.body.appendChild(elementoTemporal);
    elementoTemporal.select();
    document.execCommand('copy');
    // Elimina el elemento temporal
    document.body.removeChild(elementoTemporal);
    alert('Contenido copiado!');
  }
  // Mascara para el input de edit fecha de producto
  $(function () {
      //Datemask dd/mm/yyyy
      $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
      //Datemask2 mm/dd/yyyy
      $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
      //Money Euro
      $('[data-mask]').inputmask()

    })


  //calendario
  $(function () {
    $('#fechaGarantia, #fechaGarantia_edit').datetimepicker({
      locale: 'es',
      format: 'DD/MM/YYYY',
      daysOfWeekDisabled: [6],
      //defaultDate: "11/1/2013",
      disabledDates: [
        // moment("12/25/2013"),
        "11-11-2021",
        "11-10-2021",
        "11-05-2021"
      ], });
});

  $(function () {
  //Initialize Select2 Elements
    $('.select2').select2()

  //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  })

  // Generador de codigo de facturas 
  function generarRandom(num) {
    const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    const charactersLength = characters.length;
    let result = "";
      for (let i = 0; i < num; i++) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }

  return result;
}
  $("#generador_codigo_factura").val(generarRandom(14))  
    
  // link unico de empresa
  function linkunico(num) {
    const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    const charactersLength = characters.length;
    let result = "";
      for (let i = 0; i < num; i++) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }

  return result;
}
  $("#linkEmpresa").val("negocio_" + generarRandom(6))  
    
  $(document).ready(function () {
    // Función para calcular el total
    function actualizarTotal(fila, tipoPrecio) {
        var cantidad = fila.find('.cantidad-input').val();
        var precio_fila;

        // Obtener el precio según el tipo seleccionado
        if (tipoPrecio === 'porMenor') {
            precio_fila = fila.find('td:eq(6)').text(); // al por menor
        } else {
            precio_fila = fila.find('td:eq(7)').text(); // al por mayor
        }

        let precio_formateado = precio_fila.replace(/[$,]/g, "");
        var total = cantidad * precio_formateado;
        var total_formateado = total.toLocaleString('en-US', {style: 'currency', currency: 'USD', minimumFractionDigits: 0});
        
        fila.find('.total-column').text(total_formateado);
        fila.find('.total-input').val(total_formateado);
        actualizarCampoTotalGlobal();
    }

    // Escucha los cambios en los radio buttons
    $('input[name="tipo-precio"]').on('change', function() {
        var tipoPrecio = $(this).val();
        $('.cantidad-input').each(function () {
            var fila = $(this).closest('tr');
            actualizarTotal(fila, tipoPrecio);
        });
    });

    // Llama a la función al cargar la página para inicializar los totales
    $('.cantidad-input').each(function () {
        var fila = $(this).closest('tr');
        var tipoPrecio = $('input[name="tipo-precio"]:checked').val(); // Obtener el tipo de precio seleccionado
        actualizarTotal(fila, tipoPrecio); // Pasar el tipo de precio al llamar a actualizarTotal
    });

    // Función para actualizar el campo total global
    function actualizarCampoTotalGlobal() {
        var totalGlobal = 0;

        // Suma todos los totales de las filas
        $('.total-column').each(function () {
            // Obtén el texto sin el formato de dinero y luego conviértelo a punto flotante
            var totalSinFormato = $(this).text().replace(/[$,]/g, "");
            totalGlobal += parseFloat(totalSinFormato) || 0;
        });

        // Asigna el total global al campo de texto
        let total_factura = totalGlobal.toLocaleString('en-US', {style: 'currency', currency: 'USD', minimumFractionDigits: 0});
        $('.campo-total-global').val(total_factura);

        // Actualiza el campo de cambio
        actualizarCampoCambio();
    }

    // Escucha los cambios en los campos de cantidad
    $('.cantidad-input').on('input', function () {
        var fila = $(this).closest('tr');
        var tipoPrecio = $('input[name="tipo-precio"]:checked').val(); // Obtener el tipo de precio seleccionado
        actualizarTotal(fila, tipoPrecio); // Pasar el tipo de precio al llamar a actualizarTotal
    });

    // Escucha los cambios en el campo "Recibido"
    $('.recibido').on('input', function () {
        actualizarCampoCambio();
    });

    // Llama a la función al cargar la página para inicializar el campo de cambio
    actualizarCampoCambio();
});

// Mascara para el campo recibido
document.addEventListener('DOMContentLoaded', function () {
    var campoRecibido = document.getElementById("recibido");
    campoRecibido.addEventListener("input", function(event) {
        var valor = event.target.value;
        valor = valor.replace(/[^\d]/g, '');
        valor = "$" + valor;
        valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        event.target.value = valor;
        actualizarCampoCambio();
    });
});

// Función para actualizar el campo de cambio
function actualizarCampoCambio() {
    let total_factura = $(".campo-total-global").val();
    total_factura = total_factura.replace(/[$,]/g, "");
    let recibido = $("#recibido").val();
    recibido = recibido.replace(/[$,.]/g, "");

    // Calcula el cambio
    let cambio = recibido - total_factura;
    // Actualiza el campo "Se devuelve"
    let cambioFormateado = cambio.toLocaleString('en-US', {style: 'currency', currency: 'USD', minimumFractionDigits: 0});
    // Actualiza el campo "Se devuelve"
    $('.se_devuelve').val(cambioFormateado);
}
 
    //  CONFIGURANDO TABLAS
    $(document).ready(function () {
    var table = $("#vBuscar, #vBuscar_bodega, #producto_bodega, #historialVentas, #listaClientes, #listaProductos, #lista_cajas, #lista_usuario, #lista_categoria").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "language": {
            "decimal":        ",",
            "thousands":      ".",
            "emptyTable":     "No hay datos disponibles en la tabla",
            "info":           "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty":      "Mostrando 0 a 0 de 0 Páginas",
            "infoFiltered":   "(filtrado de _MAX_ entradas totales)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Mostrar _MENU_ entradas",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontraron registros coincidentes",
            "paginate": {
                "first":      "Primero",
                "last":       "Último",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "aria": {
                "sortAscending":  ": Activar para ordenar la columna en orden ascendente",
                "sortDescending": ": Activar para ordenar la columna en orden descendente"
            }
        }
    });

    table.buttons().container().appendTo('#vBuscar_wrapper .col-md-6:eq(0)');
});

    // Quitar las flechas de los campos number
    document.addEventListener('DOMContentLoaded', function() {
      var numberInput = document.getElementById('producto_stock_total');
      var numberInput_2 = document.getElementById('producto_modelo');
      var numberInput_add = document.getElementById('producto_stock_total_add');

      numberInput.addEventListener('focus', function() {
          this.setAttribute('type', 'text');
      });
      numberInput.addEventListener('blur', function() {
          this.setAttribute('type', 'number');
      });

      numberInput_2.addEventListener('focus', function() {
          this.setAttribute('type', 'text');
      });
      numberInput_2.addEventListener('blur', function() {
          this.setAttribute('type', 'number');
      });

      numberInput_add.addEventListener('focus', function() {
          this.setAttribute('type', 'text');
      });
      numberInput_add.addEventListener('blur', function() {
          this.setAttribute('type', 'number');
      });
    });

    // Ocultar y mostrar campo de cuotas cuando pagan a credito
    document.addEventListener("DOMContentLoaded", function () {
      mostrarOcultarPartes();
    });

function mostrarOcultarPartes() {
    var metodoPago = document.getElementById("metodoPago");
    var partesCampo = document.getElementById("partes");
    var transferenciaCampo = document.getElementById("metodo_transferencia");

    if (metodoPago.value == "2") { // "2" es el valor de "A Crédito"
        partesCampo.style.display = "block";
        partesCampo.style.border = "1px solid #9f9f9f";
        partesCampo.style.padding = "16px";
        partesCampo.style.borderRadius = "13px";


        // Cambiar el estilo para mostrar los campos uno al lado del otro
        var inputCampo = document.querySelector('#partes input');
        var selectCampo = document.querySelector('#partes select');

        inputCampo.style.display = 'inline-block';
        selectCampo.style.display = 'inline-block';
    } else {
        partesCampo.style.display = "none";
    }
    if (metodoPago.value == "1") { // "1" es el valor de "Transferencia"
        transferenciaCampo.style.display = "inline";
        transferenciaCampo.style.padding = "16px";
        transferenciaCampo.style.borderRadius = "13px";

        // Cambiar el estilo para mostrar los campos uno al lado del otro
        var inputCampo = document.querySelector('#metodo_transferencia input');
        var selectCampo = document.querySelector('#metodo_transferencia select');

        inputCampo.style.display = 'inline-block';
        selectCampo.style.display = 'inline-block';
    } else {
        transferenciaCampo.style.display = "none";
    }

    
}
/*mostrar metodo nomina*/
function mostrarMetodosNomina() {
    var metodoPago = document.getElementById("metodoPago_nomina");
    var transferenciaCampo = document.getElementById("metodo_transferencia_nomina");

    if (metodoPago.value == "1") { // "1" es el valor de "Transferencia"
      transferenciaCampo.style.display = "flex";
      transferenciaCampo.style.borderRadius = "13px";

        // Cambiar el estilo para mostrar los campos uno al lado del otro
        var inputCampo = document.querySelector('#partes input');
        var selectCampo = document.querySelector('#partes select');

        inputCampo.style.display = 'inline-flex';
        selectCampo.style.display = 'inline-flex';
    } else {
      transferenciaCampo.style.display = "none";
    }
}




    // Validando que la clave sean iguales para la vista de crear productos
    document.addEventListener("DOMContentLoaded", function() {
        pass1 = document.getElementById("usuario_clave_1");
        pass2 = document.getElementById("usuario_clave_2");
        var mensaje = document.getElementById("mensaje");

        pass1.addEventListener("input", function() {
            if (pass1.value === pass2.value) {
                // mensaje.textContent = "Las contraseñas coinciden.";
            } else {
              //   mensaje.textContent = "Las contraseñas no coinciden.";
            }
        });
        pass2.addEventListener("input", function() {
            if (pass1.value === pass2.value) {
                mensaje.textContent = "";
                document.getElementById("guardar").disabled = false;

            } else {
                mensaje.textContent = "Las contraseñas no coinciden.";
                document.getElementById("guardar").disabled = true;
            }
        });
    });

    // Función formato dinero 
    // campos agregados: cajas, caja_edit, producto_precio_compra, producto_precio_venta, producto_precio_compra_edit,
    // gasto_precio, montoDevolucion, nominaCantidad, quincenaEmpleado
  $(document).ready(function() {
      function formatDineroSinDecimales(valor) {
          return "$" + parseFloat(valor).toFixed(0).replace(/\d(?=(\d{3})+$)/g, "$&,");
      }
      $("#cajaEfectivo, #cajaEfectivo_edit, #producto_precio_compra, #producto_precio_venta, " + 
        "#producto_precio_compra_edit, #producto_precio_venta_edit, #precio_compra_stock, #precio_venta_stock, #gastoPrecio, #montoDevolucion, #nominaCantidad, " +
        "#historialAbono, #quincenaEmpleado").on("input", function() {
          var valor = $(this).val().replace(/[^0-9]/g, '');
          $(this).val(formatDineroSinDecimales(valor));
      });

      // Evento al enviar el formulario
      $("form").submit(function() {
          var valor = $("#cajaEfectivo, #cajaEfectivo_edit, #producto_precio_compra, #producto_precio_venta," +
          "#producto_precio_compra_edit, #producto_precio_venta_edit, #precio_compra_stock, #precio_venta_stock, #gastoPrecio," + 
          "#historialAbono, #montoDevolucion, #nominaCantidad, #quincenaEmpleado").val().replace(/[^0-9]/g, ''); 
          $("#cajaEfectivo").val(valor);
      });
  });

  // Validar los select obligatorios
  function validarFormulario(id) {
    if (id == 1) {
      var categoriaSeleccionada = document.forms["formProducto"]["categoria_id"].value;
     // var proveedorSeleccionado = document.forms["formProducto"]["proveedor_id"].value;
      if (categoriaSeleccionada == "") {
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Por favor, seleccione una categoría.',
          });
          return false;
      }
      // if (proveedorSeleccionado == "") {
      //     Swal.fire({
      //         icon: 'error',
      //         title: 'Oops...',
      //         text: 'Por favor, seleccione un proveedor.',
      //     });
      //     return false;
      // }
    }else if(id == 2){
      var rolSeleccionado = document.forms["formEmpleado"]["usuario_empresa"].value;
      var cajaSeleccionado = document.forms["formEmpleado"]["usuario_caja"].value;

      if (rolSeleccionado == "") {
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Por favor, seleccione un negocio donde va a laboral el empleado.',
          });
          return false;
      }
      if (cajaSeleccionado == "") {
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Por favor, seleccione una Caja para el empleado.',
          });
          return false;
      }
    }else if (id == 3) {
      var usuario_empresaSeleccionada = document.forms["formGastos"]["usuario_empresa_gastos"].value;
      if (usuario_empresaSeleccionada == "") {
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Por favor, seleccione una Empresa.',
          });
          return false;
      }
    }else if (id == 4) {
      var negocioSeleccionada = document.forms["formNomina"]["nomina_empresa"].value;
      var nomina_empleadosSeleccionada = document.forms["formNomina"]["nomina_empleados"].value;
      if (negocioSeleccionada == "") {
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Por favor, seleccione una Empresa.',
          });
          return false;
      }
      if (nomina_empleadosSeleccionada == "") {
          Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Por favor, seleccione un Empleado.',
          });
          return false;
      }
    }
    return true;
}
    // Validar que las claves sean iguales
    document.addEventListener("DOMContentLoaded", function() {
      pass1 = document.getElementById("usuario_clave_1");
      pass2 = document.getElementById("usuario_clave_2");
      var mensaje = document.getElementById("mensaje");

      // Campos de crear empleados
        pass2.addEventListener("input", function() {
          if (pass1.value === pass2.value) {
            mensaje.textContent = "";
            document.getElementById("guardar").disabled = false;
            
          } else {
            mensaje.textContent = "Las contraseñas no coinciden.";
            document.getElementById("guardar").disabled = true;
          }
        });
      });

      document.addEventListener("DOMContentLoaded", function() {
        // Campos de editar empleados
        pass1Edit = document.getElementById("usuario_clave_1E");
        pass2Edit = document.getElementById("usuario_clave_2E");
        var mensajeEdit = document.getElementById("mensajeEdit");
      
          pass2Edit.addEventListener("input", function() {
              if (pass1Edit.value === pass2Edit.value) {
                  mensajeEdit.textContent = "";
                  document.getElementById("guardarEdit").disabled = false;

              } else {
                  mensajeEdit.textContent = "Las contraseñas no coinciden.";
                  document.getElementById("guardarEdit").disabled = true;
              }
          });
    });
  // Funcion para validar peso de imagen en configurar perfil
document.addEventListener('DOMContentLoaded', function() {
    var fileInput = document.getElementById('logo_confi');
    var maxSize = 600 * 1024; // Tamaño máximo en bytes (600 KB)

    fileInput.addEventListener('change', function() {
        var file = fileInput.files[0];
        var fileType = file.type;
        var fileSize = file.size;

        // Validar el tipo de archivo
        if (fileType !== 'image/png') {
            alert('Solo se permiten archivos PNG.');
            fileInput.value = ''; // Limpiar el valor del campo de archivo
            return;
        }

        // Validar el tamaño del archivo
        if (fileSize > maxSize) {
            alert('El archivo es demasiado grande. Debe ser menor a 600 KB.');
            fileInput.value = ''; // Limpiar el valor del campo de archivo
            return;
        }
    });
});

// Cerrar session sola 
  function cerrarSesion() {
        document.getElementById('cerrarSesion').click();
    }

  // Alternar entre los metodos de devolucion 
  document.addEventListener("DOMContentLoaded", function() {
      var cambiarMetodoBtn = document.getElementById('cambiar_metodo_btn');
      var camposAOcultar = document.querySelector('.campos_a_ocultar');
      var camposAdicionales = document.querySelector('.campos_adicionales');

      cambiarMetodoBtn.addEventListener('click', function(e) {
          e.preventDefault();
          if (camposAOcultar.style.display === 'none') {
              camposAOcultar.style.display = 'block';
              camposAdicionales.style.display = 'none';
          } else {
              camposAOcultar.style.display = 'none';
              camposAdicionales.style.display = 'block';
          }
      });
  });

    // Ocultar y mostrar campo de TRANSFERENCIAS cuando pagan en:: ABONO
    document.addEventListener("DOMContentLoaded", function () {
      mostrarOcultarPartesAbono();
    });

  function mostrarOcultarPartesAbono() {
      var metodoPagoAbono = document.getElementById("metodoPagoAbono");
      var tipoTransferenciaCampo = document.getElementById("tipoTransferencia");

      if (metodoPagoAbono.value == "1") { // "2" es el valor de "A Crédito"
          tipoTransferenciaCampo.style.display = "block";
          tipoTransferenciaCampo.style.borderRadius = "13px";

          // Cambiar el estilo para mostrar los campos uno al lado del otro
          var inputCampo = document.querySelector('#tipoTransferencia input');
          var selectCampo = document.querySelector('#tipoTransferencia select');

          inputCampo.style.display = 'inline-block';
          selectCampo.style.display = 'inline-block';
      } else {
          tipoTransferenciaCampo.style.display = "none";
      }
  }

  // validando logo para bodega
    document.getElementById('crearBodega').addEventListener('submit', function(event) {
        const fileInput = document.getElementById('logoBodega');
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size; // Tamaño en bytes
            const maxSize = 10 * 1024 * 1024; // 10 MB
            if (fileSize > maxSize) {
                alert('La imagen excede el tamaño máximo permitido de 10 MB.');
                event.preventDefault(); // Evita que el formulario se envíe
            }
        }
    });

</script>
</body>
</html>