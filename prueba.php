<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo SweetAlert en PHP</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
</head>
<body>
    <?php
        // Tu lógica PHP aquí

        // Supongamos que quieres mostrar un SweetAlert después de algún evento
        echo '<script>
                // Código JavaScript para mostrar SweetAlert
                Swal.fire({
                    title: "¡Hola!",
                    text: "Este es un mensaje SweetAlert desde PHP.",
                    icon: "success",
                    confirmButtonText: "¡Entendido!"
                });
              </script>';
    ?>
</body>
</html>
