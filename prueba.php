<!-- <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Ticket</title>
</head>
<body>
    <h1>Generador de Ticket</h1>
    <form id="ticketForm" action="ticket.php" method="POST" target="_blank">
        <input type="hidden" name="generar_ticket">
        <label for="hola">Ingrese un mensaje:</label>
        <input type="text" name="hola" id="hola">
        <button type="button" onclick="openTicketWindow()">Generar Ticket</button>
    </form>

    <script>
        function openTicketWindow() {
            // Obtén el formulario y el valor del campo 'hola'
            var form = document.getElementById('ticketForm');
            var holaValue = form.elements['hola'].value;

            // Construye la URL con el valor de 'hola'
            var url = 'ticket.php?generar_ticket&hola=' + encodeURIComponent(holaValue);

            // Abre una nueva ventana con el tamaño específico y no redimensionable
            window.open(url, 'Imprimir Ticket', 'width=400,height=720,top=0,left=100,menubar=no,toolbar=yes');
        }
    </script>
</body>
</html> -->
