// Listar empleado nomina
listarEmpleados();
function listarEmpleados() {
    fetch("listarNominado.php", { 
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
            console.log("entrando");
            const dataEmpleados = data.data;
            mostrarEmpleados(dataEmpleados);
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function mostrarEmpleados(info) {
    let infoContainer = document.getElementById('listaNominandoEmpleado');
    let valesNominaInput = document.getElementById('vales_nomina');
    let quincenaEmpleadoInput = document.getElementById('nominaCantidad');
    let descontarRadioNo = document.querySelector('input[name="descontar_vale"][value="2"]');
    let pagoFinalInput = document.getElementById('pagoFinal');
    infoContainer.innerHTML = '';

    // Objeto para mantener un registro de usuarios
    let usuarios = {};

    // Recorrer la información y mantener solo una entrada por usuario
    if (info) {
        info.forEach(empleado => {
            if (!usuarios[empleado.usuario_id]) {
                usuarios[empleado.usuario_id] = empleado;
            } else if (empleado.nomina_estado === 0) {
                usuarios[empleado.usuario_id] = empleado;
            }
        });
    }

    // Agregar la opción "Escoger empleado" al principio del select
    var escogerOption = document.createElement('option');
    escogerOption.value = '';
    escogerOption.textContent = 'Escoger empleado';
    infoContainer.appendChild(escogerOption);

    // Mostrar las opciones en el select
// Mostrar las opciones en el select
Object.values(usuarios).forEach(empleado => {
    var infoOption = document.createElement('option');
    infoOption.value = empleado.usuario_id + " - " + empleado.link + " - " 
    + (empleado.nomina_estado === 1 ? "0" : empleado.nomina_prestamo || "Todo saldado") 
    + " - " + (empleado.quincena_empleado || "Este dato no fue registrado")
    + " - " + (empleado.nomina_estado || "No hay vales pendientes");
    infoOption.textContent = empleado.usuario_nombre + " " + empleado.usuario_apellido + " " + empleado.usuario_cedula;
    infoContainer.appendChild(infoOption);
});


    // Llamar a listnominados y actualizar el valor del input cuando cambie la selección en el select
    infoContainer.onchange = function() {
        var selectedOption = infoContainer.options[infoContainer.selectedIndex];
        var selectedValue = selectedOption.value;
        var nominaPrestamo = selectedValue.split(" - ")[2] || 0; // Obtener el valor de nominaPrestamo
        var quincenaEmpleado = selectedValue.split(" - ")[3] || 0; // Obtener el valor de quincena
        
        valesNominaInput.value = nominaPrestamo;
        quincenaEmpleadoInput.value = quincenaEmpleado;
        pagoFinalInput.value = quincenaEmpleadoInput.value.replace(/[$,]/g, "") - valesNominaInput.value.replace(/[$,]/g, "");
    };

    console.log("Lista de empleados mostrada");
}

// Llamar a la función para mostrar empleados al cargar la página
mostrarEmpleados();


