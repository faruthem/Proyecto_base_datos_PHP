// Esperamos a que todo el HTML esté cargado
document.addEventListener('DOMContentLoaded', function() {
    
    const registroForm = document.getElementById('registroForm');
    const messageDiv = document.getElementById('message');

    registroForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevenimos el envío tradicional

        // Limpiamos mensajes previos
        messageDiv.style.display = 'none';
        messageDiv.textContent = '';
        messageDiv.classList.remove('success-message'); // (Añadiremos este estilo)

        // Validacion rápida en frontend
        const password = document.getElementById('password').value;
        const confirm_password = document.getElementById('confirm_password').value;

        if (password !== confirm_password) {
            messageDiv.textContent = 'Las contraseñas no coinciden.';
            messageDiv.style.display = 'block';
            return; // Detenemos la ejecución
        }

        // Preparamos los datos del formulario
        const formData = new FormData(registroForm);

        // Enviamos por Fetch
        fetch('php/registro_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // ÉXITO
                messageDiv.textContent = data.message;
                messageDiv.classList.add('success-message'); // Estilo verde
                messageDiv.style.display = 'block';
                
                // Opcional: limpiar el formulario
                registroForm.reset();
                
                // Opcional: redirigir al login después de unos segundos
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 3000); // 3 segundos

            } else {
                // ERROR (manejado por PHP)
                messageDiv.textContent = data.message;
                messageDiv.classList.remove('success-message');
                messageDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error en la petición Fetch:', error);
            messageDiv.textContent = 'Error de conexión con el servidor.';
            messageDiv.style.display = 'block';
        });
    });
});