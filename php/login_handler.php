<?php
// 1. Incluimos la conexión
require 'db_connect.php';

// 2. Definimos que la respuesta será JSON (para que JS la entienda)
header('Content-Type: application/json');

// 3. Preparamos una respuesta por defecto
$response = [
    'success' => false,
    'message' => 'Método no permitido.'
];

// 4. Solo aceptamos peticiones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtenemos los datos del formulario (enviados por JS)
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validaciones básicas
    if (empty($username) || empty($password)) {
        $response['message'] = 'Usuario y contraseña son requeridos.';
        echo json_encode($response);
        exit;
    }

    try {
        // 5. Consulta PREPARADA (previene Inyección SQL)
        // Buscamos al usuario en la tabla 'Usuarios'
        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE User = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(); // fetch() obtiene la primera coincidencia

        // 6. Verificación
        if ($user && password_verify($password, $user['password'])) {
            
            // ¡ÉXITO! La contraseña coincide con el hash.
            
            // 7. Creamos la Sesión del Usuario
            // Guardamos datos en la variable global $_SESSION
            $_SESSION['user_id'] = $user['ID_user'];
            $_SESSION['username'] = $user['User'];
            $_SESSION['id_estudiante'] = $user['ID_Estudiante']; // Útil para el futuro

            // 8. Respondemos con éxito
            $response['success'] = true;
            $response['message'] = 'Login exitoso. Redirigiendo...';
            // Opcional: a dónde redirigir
            $response['redirect'] = 'dashboard.php'; 

        } else {
            // Error: Usuario no encontrado o contraseña incorrecta
            $response['message'] = 'Credenciales incorrectas.';
        }

    } catch (\PDOException $e) {
        // Error de base de datos
        $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
    }

} // Fin de (if POST)

// 9. Enviamos la respuesta JSON de vuelta a JavaScript
echo json_encode($response);