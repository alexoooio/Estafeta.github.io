function updateDateTime() {
    const now = new Date();
    const date = now.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    const time = now.toLocaleTimeString('es-ES');
    document.getElementById('date').innerHTML = `Fecha: ${date}`;
    document.getElementById('time').innerHTML = `Hora: ${time}`;
}

// Actualizar la fecha y hora cada segundo
setInterval(updateDateTime, 1000);

// Inicializar la fecha y hora al cargar la página
updateDateTime();
