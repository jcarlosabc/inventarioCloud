<?php

include("../db.php");
	# Incluyendo librerias necesarias #
    require ('code128_nomina.php');
    
    $pdf = new PDF_Code128('P','mm',array(80,140));
    $pdf->SetMargins(4,10,4);
    $pdf->AddPage();

    // if(isset($_GET['url'])){
    //     $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    //     echo "holaaaa";
    // }

    // Datos del empleado 
    $empresa_nombre = isset($_POST['empresa_nombre']) ? $_POST['empresa_nombre'] : "No hay nada";
    $empresa_telefono = isset($_POST['empresa_telefono']) ? $_POST['empresa_telefono'] : "No hay nada";
    $empresa_direccion = isset($_POST['empresa_direccion']) ? $_POST['empresa_direccion'] : "No hay nada";
    $empresa_nit = isset($_POST['empresa_nit']) ? $_POST['empresa_nit'] : "No hay nada";
    $usuario_cedula = isset($_POST['usuario_cedula']) ? $_POST['usuario_cedula'] : "No hay nada";
    $usuario_nombre = isset($_POST['usuario_nombre']) ? $_POST['usuario_nombre'] : "No hay nada";
    $usuario_apellido = isset($_POST['usuario_apellido']) ? $_POST['usuario_apellido'] : "No hay nada";
    $nomina_cantidad = isset($_POST['nomina_cantidad']) ? $_POST['nomina_cantidad'] : "No hay nada";
    $nomina_cantidad = '$ ' . number_format($nomina_cantidad, 0, '.', ','); 
    $nomina_fecha = isset($_POST['nomina_fecha']) ? $_POST['nomina_fecha'] : "No hay nada";
    $nomina_hora = isset($_POST['nomina_hora']) ? $_POST['nomina_hora'] : "No hay nada";
    $nomina_prestamo = isset($_POST['nomina_prestamo']) ? $_POST['nomina_prestamo'] : "No hay nada";
    $nomina_prestamo = '$ ' . number_format($nomina_prestamo, 0, '.', ','); 
    $quincena_empleado = isset($_POST['quincena_empleado']) ? $_POST['quincena_empleado'] : "No hay nada";
    $quincena_empleado = '$ ' . number_format($quincena_empleado, 0, '.', ','); 
    $nomina_estado = isset($_POST['nomina_estado']) ? $_POST['nomina_estado'] : "No hay nada";
    $usuario_id = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : "No hay nada";
    $sentencia = $conexion->prepare("SELECT * 
    FROM nomina 
    WHERE nomina_usuario_id = :usuario_ids 
    AND nomina_estado = 1 
    AND nomina_prestamo = (SELECT MAX(nomina_prestamo) FROM nomina WHERE nomina_usuario_id = :usuario_id)
    ORDER BY nomina_usuario_id DESC");
$sentencia->bindParam(":usuario_id", $usuario_id);
$sentencia->bindParam(":usuario_ids", $usuario_id);
$sentencia->execute();
$nomina_vale = $sentencia->fetch(PDO::FETCH_LAZY);
if ($nomina_vale) {
$infoId = $nomina_vale['nomina_id'];
$nomina_psrestamo = $nomina_vale['nomina_prestamo'];  
}

 
    # Encabezado #
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

    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Fecha de Pago: " . $nomina_fecha . " Hora: " . $nomina_hora ),0,'C',false);
    $pdf->Ln(3);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Datos del Empleado "),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cc.: ". $usuario_cedula ),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Nombre: ". $usuario_nombre . " " . $usuario_apellido),0,'C',false);
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Pago Normal: " . $quincena_empleado),0,'C',false);
    // if ($nomina_estado == 0) {
    //     $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","vale: -" . $nomina_prestamo),0,'C',false);
    // }else if($nomina_estado == 1){
    //     $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","vale: -" . $nomina_prestamo),0,'C',false);
    // }
    if ($nomina_vale) {
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","vale : -" . $nomina_psrestamo),0,'C',false);
    }else {
        $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","vale : -" . $nomina_prestamo),0,'C',false);
    }
    $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Pago: " . $nomina_cantidad),0,'C',false);
    $pdf->SetFont('Arial','B',10);
    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1",strtoupper("Ticket Nro: " . $usuario_cedula)),0,'C',false);
    $pdf->SetFont('Arial','',9);

    // $pdf->Ln(1);
    // $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","------------------------------------------------------"),0,0,'C');
    // $pdf->Ln(5);

    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Cliente: ". $usuario_cedula),0,'C',false);
    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Nit/C.C: ". $usuario_cedula),0,'C',false);
    // // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","Teléfono: ". $cliente_telefono),0,'C',false);

    // $pdf->Ln(1);
    // $pdf->Cell(0,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
    // $pdf->Ln(3);

    # Tabla de productos #
    // $pdf->Cell(10,5,iconv("UTF-8", "ISO-8859-1","Cant."),0,0,'C');
    // $pdf->Cell(19,5,iconv("UTF-8", "ISO-8859-1","Precio"),0,0,'C');
    // $pdf->Cell(15,5,iconv("UTF-8", "ISO-8859-1","Prod.."),0,0,'C');
    // $pdf->Cell(28,5,iconv("UTF-8", "ISO-8859-1","Total"),0,0,'C');

    // $pdf->Ln(3);
    // $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');
    // $pdf->Ln(3);

    // if ($detalles_venta_json) {
    // // Decodificar el JSON para obtener un array asociativo
    // $detalles_venta = json_decode($detalles_venta_json, true);

    // // Verificar si la decodificación fue exitosa
    // if ($detalles_venta) {
    //     foreach ($detalles_venta as $detalle) {
    //         $detalle_pv = '$ ' . number_format($detalle['precio_venta'], 0, '.', ','); 
    //         $detalle_total = '$ ' . number_format($detalle['total'], 0, '.', ','); 
    //         /*----------  Detalles de la tabla  ----------*/
    //         $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle['descripcion']." / ".$detalle['producto_marca']." / ".$detalle['producto_modelo']),0,'C',false);
    //         $pdf->Cell(10,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle['cantidad']),0,0,'C');
    //         $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle_pv),0,0,'C');
    //         $pdf->Cell(19,4,iconv("UTF-8", "ISO-8859-1"," "),0,0,'C');
    //         $pdf->Cell(28,4,iconv("UTF-8", "ISO-8859-1"," " . $detalle_total),0,0,'C');
    //         $pdf->Ln(4);
    //         $pdf->MultiCell(0,4,iconv("UTF-8", "ISO-8859-1","Garantía " . $detalle['fecha_garantia']),0,'C',false);
    //         // $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","----------------------"),0,0,'C');

    //         $pdf->Ln(7);
    //         /*----------  Fin Detalles de la tabla  ----------*/
    //     }
    // } 
    // } 


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

    // $pdf->Cell(72,5,iconv("UTF-8", "ISO-8859-1","-------------------------------------------------------------------"),0,0,'C');

    // $pdf->Ln(5);

    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","TOTAL A PAGAR"),0,0,'C');
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $usuario_cedula),0,0,'C');

    // $pdf->Ln(5);
    
    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","TOTAL PAGADO"),0,0,'C');
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $usuario_cedula),0,0,'C');

    // $pdf->Ln(5);

    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // if ($venta_metodo_pago == "Credito") {
    //     $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","CREDITO PENDIENTE"),0,0,'C');
    // } else { 
    //     $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","CAMBIO"),0,0,'C');
    // } 
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $usuario_cedula),0,0,'C');

    // if ($venta_metodo_pago == "Credito") {
    // $pdf->Ln(5);

    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","PLAZO PARA PAGAR"),0,0,'C');
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1"," " . $usuario_cedula ." ". $usuario_cedula),0,0,'C');
    // } 
    // $pdf->Ln(5);

    // $pdf->Cell(18,5,iconv("UTF-8", "ISO-8859-1",""),0,0,'C');
    // $pdf->Cell(22,5,iconv("UTF-8", "ISO-8859-1","USTED AHORRA"),0,0,'C');
    // $pdf->Cell(32,5,iconv("UTF-8", "ISO-8859-1","$0.00 USD"),0,0,'C');

    $pdf->Ln(10);

    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","*** Precios de productos incluyen impuestos. Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);
    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","***Para poder realizar un reclamo debe de presentar este ticket ***"),0,'C',false);

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Tu labor es importante"),'',0,'C');
    $pdf->Ln(3);
    $pdf->Cell(0,7,iconv("UTF-8", "ISO-8859-1","Gracias por trabajar con nosotros"),'',0,'C');

    $pdf->Ln(9);

    # Codigo de barras #
    // $pdf->Code128(5,$pdf->GetY(), " " . $usuario_cedula . " ",70,20);
    // $pdf->SetXY(0,$pdf->GetY()+21);
    // $pdf->SetFont('Arial','',14);
    // $pdf->MultiCell(0,5,iconv("UTF-8", "ISO-8859-1","" . $usuario_cedula),0,'C',false);
    
    # Nombre del archivo PDF #
    $pdf->Output("I", "Ticket_". $usuario_cedula . ".pdf" ,true);

