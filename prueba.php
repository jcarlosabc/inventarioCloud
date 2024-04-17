<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Imprimir contenido</title>
<link rel="stylesheet" type="text/css" media="print" href="print.css">
</head>
<body>
    <style>
        @media print {
    body {
        width: 80mm; /* Ancho de la página */
        margin: 0 auto; /* Centrar el contenido */
    }
}

    </style>
<button id="imprimirBtn">Imprimir</button>

<div id="contenidoParaImprimir">
    <!-- Aquí coloca el contenido que deseas imprimir -->
    <h1>Contenido para imprimir</h1>
    <p>Este es el contenido que será impreso cuando hagas clic en el botón.</p>
</div>

<script>
document.getElementById("imprimirBtn").addEventListener("click", function() {
    window.print(); // Este método activa el diálogo de impresión del navegador
});
</script>
</body>
</html>
