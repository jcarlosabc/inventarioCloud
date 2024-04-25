// Registrando Cliente FAST DE LOCALES
function sendDataVenta() {
    const cliente_empresa_venta = document.getElementById('cliente_empresa_venta').value; 
    const cliente_nit_venta = document.getElementById('cliente_nit_venta').value; 
    const cliente_nombre_venta = document.getElementById('cliente_nombre_venta').value; 
    const cliente_apellido_venta = document.getElementById('cliente_apellido_venta').value; 
    const cliente_telefono_venta = document.getElementById('cliente_telefono_venta').value; 
    const cliente_ciudad_venta = document.getElementById('cliente_ciudad_venta').value; 
    const cliente_direccion_venta = document.getElementById('cliente_direccion_venta').value; 
    const cliente_email_venta = document.getElementById('cliente_email_venta').value; 
    const link = document.getElementById('link').value; 
    fetch('regi_fast_cliente_venta.php', {
        method: 'POST',
        body: new URLSearchParams({
          cliente_empresa: cliente_empresa_venta,
          cliente_nit: cliente_nit_venta,
          cliente_nombre: cliente_nombre_venta,
          cliente_apellido: cliente_apellido_venta,
          cliente_telefono: cliente_telefono_venta,
          cliente_ciudad: cliente_ciudad_venta,
          cliente_direccion: cliente_direccion_venta,
          cliente_email: cliente_email_venta,
          link: link
        })
    })
    .then(response => response.text())
    .then(data => {
        Swal.fire({
            title: "Cliente Creado",
            icon: "success",
            confirmButtonText: "¡Entendido!"
        })
        document.getElementById('cerrarModalClienteVenta').click(); 
        listarClienteVenta();
    })
    .catch(error => {
        console.error('Error:', error);
    });
} 

// Listar Clientes
function listarClienteVenta(){
    fetch("listar_venta.php", { 
        method: "POST"
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('La solicitud falló con estado ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const dataCliente = data.data;
            mostrarClienteVenta(dataCliente);
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Mostrar Clientes
function mostrarClienteVenta(info) {
    var infoContainer = document.getElementById('fastClienteVenta');
    infoContainer.innerHTML = '';
    info.forEach(info => {
        var infoOption = document.createElement('option');
        infoOption.value = info.cliente_id;
        infoOption.textContent = info.cliente_nombre + " " + info.cliente_nit;
        infoContainer.appendChild(infoOption);
    });
}

// Llamar a la función para listar productos al cargar la página
listarClienteVenta();