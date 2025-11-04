// Esperamos a que todo el HTML esté cargado
document.addEventListener('DOMContentLoaded', function() {
    
    const loginForm = document.getElementById('loginForm');
    const errorMessageDiv = document.getElementById('errorMessage');

    // 1. Escuchamos el evento 'submit' del formulario
    loginForm.addEventListener('submit', function(event) {
        
        // 2. Prevenimos que el formulario se envíe de la forma tradicional
        event.preventDefault();

        // 3. Ocultamos errores previos
        errorMessageDiv.style.display = 'none';
        errorMessageDiv.textContent = '';

        // 4. Creamos un objeto FormData con los datos del formulario
        const formData = new FormData(loginForm);

        // 5. Usamos la API Fetch para enviar los datos al backend (PHP)
        fetch('php/login_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Esperamos una respuesta JSON
        .then(data => {
            // 6. Procesamos la respuesta del servidor
            if (data.success) {
                // Éxito: El servidor dice que el login es correcto
                // Redirigimos al dashboard
                window.location.href = data.redirect;
            } else {
                // Error: El servidor dice que algo salió mal
                errorMessageDiv.textContent = data.message;
                errorMessageDiv.style.display = 'block';
            }
        })
        .catch(error => {
            // Error de red o si el JSON está mal formado
            console.error('Error en la petición Fetch:', error);
            errorMessageDiv.textContent = 'Error de conexión con el servidor.';
            errorMessageDiv.style.display = 'block';
        });
    });
});