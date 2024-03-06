      </div><!-- /.container-fluid -->
    </section>
</div>
 <!-- /.content-wrapper -->
 <footer class="main-footer">
    <strong>Copyright &copy; <a href="https://adminlte.io">Innova Cloud</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0.0
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<script src="../../validaciones/validacion.js"></script>
<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../../plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  // Escaneo de código de barras
  $.widget.bridge('uibutton', $.ui.button);
  var sound = new Audio("../../dist/sound/barcode.wav");
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
</script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../../plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../../plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../../plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../../plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../../plugins/moment/moment.min.js"></script>
<script src="../../plugins/moment/locales.js"></script>
<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../../plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../../dist/js/pages/dashboard.js"></script>
<!-- barcode -->
<script type="text/javascript" src="../../dist/js/barcode.js"></script>
<!-- DataTables  & Plugins -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../../plugins/jszip/jszip.min.js"></script>
<script src="../../plugins/pdfmake/pdfmake.min.js"></script>
<script src="../../plugins/pdfmake/vfs_fonts.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Select2 -->
<script src="../../plugins/select2/js/select2.full.min.js"></script>
<!-- dropzonejs -->
<script src="../../plugins/dropzone/min/dropzone.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>


<script>

  //calendario
  $(function () {
    $('#fechaGarantia').datetimepicker({
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
</script>

<script>
    
    $(document).ready(function () {
        // Función para calcular el total
        function actualizarTotal(fila) {
            var cantidad = fila.find('.cantidad-input').val();
            var precio_fila = fila.find('td:eq(6)').text();

            let precio_formateado = precio_fila.replace(/[$,]/g, "");
            var total = cantidad * precio_formateado;
            var total_formateado = total.toLocaleString('en-US', {style: 'currency', currency: 'USD', minimumFractionDigits: 0});
            
            fila.find('.total-column').text(total_formateado);
            fila.find('.total-input').val(total_formateado);
            actualizarCampoTotalGlobal();
        }

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
            actualizarTotal(fila);
        });

        // Llama a la función al cargar la página para inicializar los totales
        $('.cantidad-input').each(function () {
            var fila = $(this).closest('tr');
            actualizarTotal(fila);
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
    var table = $("#vBuscar, #historialVentas, #listaClientes, #listaProductos, #lista_cajas, #lista_usuario").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });
    table.buttons().container().appendTo('#vBuscar_wrapper .col-md-6:eq(0)');
    });

    // MASCARAS DE DINERO
    document.addEventListener('DOMContentLoaded', function () {
      var inputPrecioCompra = document.getElementById("producto_precio_compra");
      var inputPrecioVenta = document.getElementById("producto_precio_venta");
      var campoRecibido = document.getElementById("recibido");

      // Valor formateado para el precio de compra
      inputPrecioCompra.addEventListener("input", function(event) {
          var valor = event.target.value;
          valor = valor.replace(/[^\d]/g, '');
          valor = "$" + valor;
          valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
          event.target.value = valor;
      });

      // Valor formateado para el precio de venta
      inputPrecioVenta.addEventListener("input", function(event) {
          var valor = event.target.value;
          valor = valor.replace(/[^\d]/g, '');
          valor = "$" + valor;
          valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
          event.target.value = valor;
      });

      // Prevenir el envío del formulario si el valor de alguno de los campos no es válido
      document.getElementById("formCaja").addEventListener("submit", function(event) {
          var valorCompra = inputPrecioCompra.value;
          var valorVenta = inputPrecioVenta.value;
          // Remover cualquier caracter que no sea número
          valorCompra = valorCompra.replace(/[^\d]/g, '');
          valorVenta = valorVenta.replace(/[^\d]/g, '');

          // Si alguno de los valores es vacío o no es un número válido, prevenir el envío del formulario
          if (valorCompra === '' || isNaN(parseInt(valorCompra)) || valorVenta === '' || isNaN(parseInt(valorVenta))) {
              event.preventDefault();
              alert("Ingrese un monto válido en precio de compra y precio de venta.");
          }
      });
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

        if (metodoPago.value == "2") { // "2" es el valor de "A Crédito"
            partesCampo.style.display = "block";
        } else {
            partesCampo.style.display = "none";
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
</script>
</body>
</html>