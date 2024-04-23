<?php


	# Incluyendo librerias necesarias #
    require ('code128.php');
    
    $pdf = new PDF_Code128('P','mm',array(80,258));
    $pdf->SetMargins(4,10,4);
    $pdf->AddPage();

    // Datos de la empresa 
    $empresa_nombre = isset($_POST['empresa_nombre']) ? $_POST['empresa_nombre'] : "No hay nada";
    $empresa_telefono = isset($_POST['empresa_telefono']) ? $_POST['empresa_telefono'] : "No hay nada";
    $empresa_direccion = isset($_POST['empresa_direccion']) ? $_POST['empresa_direccion'] : "No hay nada";
    $empresa_nit = isset($_POST['empresa_nit']) ? $_POST['empresa_nit'] : "No hay nada";
    // Datos de la venta
    $venta_id = isset($_POST['venta_id']) ? $_POST['venta_id'] : "No hay nada";
    $venta_codigo = isset($_POST['venta_codigo']) ? $_POST['venta_codigo'] : "No hay nada";
    $venta_fecha = isset($_POST['venta_fecha']) ? $_POST['venta_fecha'] : "No hay nada";
    $venta_hora = isset($_POST['venta_hora']) ? $_POST['venta_hora'] : "No hay nada";
    $caja_id = isset($_POST['caja_id']) ? $_POST['caja_id'] : "No hay nada";
    $venta_metodo_pago = isset($_POST['venta_metodo_pago']) ? $_POST['venta_metodo_pago'] : "No hay nada";
    // Datos del empleado
    $usuario_nombre = isset($_POST['usuario_nombre']) ? $_POST['usuario_nombre'] : "No hay nada";
    // Datos del cliente
    $nombre_cliente = isset($_POST['nombre_cliente']) ? $_POST['nombre_cliente'] : "No hay nada";
    $cliente_nit = isset($_POST['cliente_nit']) ? $_POST['cliente_nit'] : "No hay nada";
    $cliente_telefono = isset($_POST['cliente_telefono']) ? $_POST['cliente_telefono'] : "No hay nada";
    // Datos de dinero
    $venta_total = isset($_POST['venta_total']) ? $_POST['venta_total'] : "No hay nada";
    $venta_total = '$ ' . number_format($venta_total, 0, '.', ','); 

    $venta_pagado = isset($_POST['venta_pagado']) ? $_POST['venta_pagado'] : "No hay nada";
    $venta_pagado = '$ ' . number_format($venta_pagado, 0, '.', ','); 

    $venta_cambio = isset($_POST['venta_cambio']) ? $_POST['venta_cambio'] : "No hay nada";
    $venta_cambio = '$ ' . number_format($venta_cambio, 0, '.', ','); 

    $plazo = isset($_POST['plazo']) ? $_POST['plazo'] : "No hay nada";
    $tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : "No hay nada";

    // Datos detalles ventas
    $detalles_venta_json = isset($_POST['detalles_venta']) ? $_POST['detalles_venta'] : null;
 
    # Encabezado y datos de la empresa #
    $pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(0,0,0);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper($empresa_nombre)),0,'C',false);
    $pdf->SetFont('Arial','',9);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","NIT: " . $empresa_nit),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Dirección: " .$empresa_direccion ),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: " . $empresa_telefono ),0,'C',false);
    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Email: correo@ejemplo.com"),0,'C',false);
   
    $pdf->Ln(1);
    $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha: " . $venta_fecha . " Hora: " . $venta_hora ),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cajero: VARIEDADES21CTG"),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Caja Nro: ". $caja_id),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Método de Pago: " . $venta_metodo_pago),0,'C',false);
    $pdf->SetFont('Arial','B',10);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("Ticket Nro: " . $venta_id)),0,'C',false);
    $pdf->SetFont('Arial','',9);

    $pdf->Ln(1);
    $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cliente: ". $nombre_cliente),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Nit/C.C: ". $cliente_nit),0,'C',false);
    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ". $cliente_telefono),0,'C',false);

    $pdf->Ln(1);
    $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);

    # Tabla de productos #
    $pdf->Cell(10,5,iconv("UTF-8", "ISO-8859-1","Cant."),0,0,'C');
    $pdf->Cell(19,5,iconv("UTF-8", "ISO-8859-1","Precio"),0,0,'C');
    $pdf->Cell(15,5,iconv("UTF-8", "ISO-8859-1","Prod.."),0,0,'C');
    $pdf->Cell(28,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');

    $pdf->Ln(3);
    $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);

    if ($detalles_venta_json) {
    // Decodificar el JSON para obtener un array asociativo
    $detalles_venta = json_decode($detalles_venta_json, true);

    // Verificar si la decodificación fue exitosa
    if ($detalles_venta) {
        foreach ($detalles_venta as $detalle) {
            $detalle_pv = '$ ' . number_format($detalle['precio_venta'], 0, '.', ','); 
            $detalle_total = '$ ' . number_format($detalle['total'], 0, '.', ','); 
            /*----------  Detalles de la tabla  ----------*/
            $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle['descripcion']." / ".$detalle['producto_marca']." / ".$detalle['producto_modelo']),0,'C',false);
            $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle['cantidad']),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle_pv),0,0,'C');
            $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1"," "),0,0,'C');
            $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle_total),0,0,'C');
            $pdf->Ln(4);
            // $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","Garantía " . $detalle['fecha_garantia']),0,'C',false);
            // $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","----------------------"),0,0,'C');

            $pdf->Ln(7);
            /*----------  Fin Detalles de la tabla  ----------*/
        }
    } 
    } 


    // $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

    //     $pdf->Ln(5);

    # Impuestos & totales #
    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","SUBTOTAL"),0,0,'C');
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","+ $70.00 USD"),0,0,'C');

    // $pdf->Ln(5);

    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","IVA (13%)"),0,0,'C');
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","+ $0.00 USD"),0,0,'C');

    // $pdf->Ln(5);

    $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

    $pdf->Ln(5);

    $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","TOTAL A PAGAR"),0,0,'C');
    $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $venta_total),0,0,'C');

    $pdf->Ln(5);
    
    $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","TOTAL PAGADO"),0,0,'C');
    $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $venta_pagado),0,0,'C');

    $pdf->Ln(5);

    $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    if ($venta_metodo_pago == "Credito") {
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","CREDITO PENDIENTE"),0,0,'C');
    } else { 
        $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","CAMBIO"),0,0,'C');
    } 
    $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $venta_cambio),0,0,'C');

    if ($venta_metodo_pago == "Credito") {
    $pdf->Ln(5);

    $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","PLAZO PARA PAGAR"),0,0,'C');
    $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $plazo ." ". $tiempo),0,0,'C');
    } 
    // $pdf->Ln(5);

    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","USTED AHORRA"),0,0,'C');
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$0.00 USD"),0,0,'C');

    $pdf->Ln(10);

    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","***Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por su compra"),'',0,'C');

    $pdf->Ln(9);

    # Codigo de barras #
    $pdf->Code128(5,$pdf->GetY(), " " . $venta_codigo . " ",70,20);
    $pdf->SetXY(0,$pdf->GetY()+21);
    $pdf->SetFont('Arial','',14);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","" . $venta_codigo),0,'C',false);
    
    # Nombre del archivo PDF #
    $pdf->Output("I", "Ticket_". $venta_codigo . ".pdf" ,true);

