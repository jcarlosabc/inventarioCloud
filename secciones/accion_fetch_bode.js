// Registrando Cliente FAST DE BODEGA
function sendData() {
    const cliente_empresa = document.getElementById('cliente_empresa').value; 
    const cliente_nit = document.getElementById('cliente_nit').value; 
    const cliente_nombre = document.getElementById('cliente_nombre').value; 
    const cliente_apellido = document.getElementById('cliente_apellido').value; 
    const cliente_telefono = document.getElementById('cliente_telefono').value; 
    const cliente_ciudad = document.getElementById('cliente_ciudad').value; 
    const cliente_direccion = document.getElementById('cliente_direccion').value; 
    const cliente_email = document.getElementById('cliente_email').value; 
    const link = document.getElementById('link').value; 
    fetch('regi_fast_cliente.php', {
        method: 'POST',
        body: new URLSearchParams({
          cliente_empresa: cliente_empresa,
          cliente_nit: cliente_nit,
          cliente_nombre: cliente_nombre,
          cliente_apellido: cliente_apellido,
          cliente_telefono: cliente_telefono,
          cliente_ciudad: cliente_ciudad,
          cliente_direccion: cliente_direccion,
          cliente_email: cliente_email,
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
        document.getElementById('cerrarModalCliente').click(); 
        listarCliente();
    })
    .catch(error => {
        console.error('Error:', error);
    });
} 

// Listar Clientes
function listarCliente(){
    fetch("listar.php", { 
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
            mostrarCliente(dataCliente);
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Mostrar Clientes
function mostrarCliente(info) {
    var infoContainer = document.getElementById('fastCliente');
    infoContainer.innerHTML = '';
    info.forEach(info => {
        var infoOption = document.createElement('option');
        infoOption.value = info.cliente_id;
        infoOption.textContent = info.cliente_nombre + " " + info.cliente_nit;
        infoContainer.appendChild(infoOption);
    });
}

// Llamar a la función para listar productos al cargar la página
listarCliente();

function remover_producto_carrito(id) {
    fetch('removerProducto.php', {
        method: 'POST',
        body: new URLSearchParams({id: id})
    })
    .then(response => response.text())
    .then(data => {
        console.log("Borrado");
        // Seleccionar y eliminar la fila del carrito del HTML
        var filaEliminar = document.getElementById('producto_' + id);
        if (filaEliminar) {
            filaEliminar.parentNode.removeChild(filaEliminar);
            // Llamar a la función para actualizar los totales globales y otros campos
            if (typeof actualizarCampoTotalGlobal === 'function') {
                actualizarCampoTotalGlobal();
            }
        } else {
            console.log("Fila del carrito no encontrada en el HTML");
        }
    })
    .catch(error => {
        console.log("No borrado");
        console.error('Error:', error);
    });
}

function update_producto_bodega(id) {
    let cantidadProducto = document.getElementById('cantidadProductoBodega_' + id).value;
    let cantidadPrecioMenor = document.getElementById('cantidadPrecioMenorBodega_' + id).value;
    let cantidadPrecioMayor = document.getElementById('cantidadPrecioMayorBodega_' + id).value;
    fetch('updateProducto.php', {
        method: 'POST',
        body: new URLSearchParams({id: id, cantidadProducto:cantidadProducto, cantidadPrecioMenor:cantidadPrecioMenor,
            cantidadPrecioMayor:cantidadPrecioMayor })
    })
    .then(response => response.text())
    .then(data => {
    //    console.log("update realizado...");
    })
    .catch(error => {
        // console.log("Algo salio mal con el update...");
        console.error('Error:', error);
    });
}