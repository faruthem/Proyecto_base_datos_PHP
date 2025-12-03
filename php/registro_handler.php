<?php
// 1. Incluimos la conexión
require 'db_connect.php';

// 2. Definimos que la respuesta será JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Método no permitido.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Recolectamos TODOS los datos
    $nombre = $_POST['nombre'] ?? '';
    $a_paterno = $_POST['a_paterno'] ?? '';
    $a_materno = $_POST['a_materno'] ?? ''; // Puede ser opcional
    $carrera = $_POST['carrera'] ?? '';
    $nua = $_POST['nua'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // 4. Validación de campos vacíos
    if (empty($nombre) || empty($a_paterno) || empty($carrera) || empty($nua) || empty($username) || empty($password)) {
        $response['message'] = 'Todos los campos (excepto A. Materno) son requeridos.';
        echo json_encode($response);
        exit;
    }

    // 5. Validación de reglas de negocio
    if ($password !== $confirm_password) {
        $response['message'] = 'Las contraseñas no coinciden.';
        echo json_encode($response);
        exit;
    }

    // ==== NUEVA VERIFICACIÓN:  Contraseña Segura ====
    // Exigimos 8+ caracteres, al menos una letra y al menos un número.
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) {
        $response['message'] = 'La contraseña debe tener al menos 8 caracteres, incluyendo al menos una letra y un número.';
        echo json_encode($response);
        exit;
    }
    // FIN DE LA NUEVA VERIFICACIÓN

    if (strlen($nua) != 6 || !ctype_digit($nua)) {
         $response['message'] = 'El NUA debe ser un número de 6 dígitos.';
         echo json_encode($response);
         exit;
    }
    
    // 6. Hashear la contraseña (¡NUNCA GUARDAR TEXTO PLANO!)
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // 7. Iniciamos la TRANSACCIÓN
    // Esto es fundamental. O todo o nada.
    try {
        $pdo->beginTransaction();

        // 8. Chequeo de duplicados (NUA y User)
        // Usamos SELECT ... FOR UPDATE para bloquear las filas y evitar que otro se registre
        // con el mismo NUA o User al mismo tiempo (condición de carrera).
        
        $stmt = $pdo->prepare("SELECT 1 FROM Estudiante WHERE NUA = ? FOR UPDATE");
        $stmt->execute([$nua]);
        if ($stmt->fetch()) {
            throw new \Exception('El NUA ingresado ya está registrado.');
        }

        $stmt = $pdo->prepare("SELECT 1 FROM Usuarios WHERE User = ? FOR UPDATE");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            throw new \Exception('El nombre de usuario ya está en uso.');
        }

        // 9. PASO A: Insertar en la tabla 'Estudiante'
        $stmtEstudiante = $pdo->prepare(
            "INSERT INTO Estudiante (Nombre, A_Paterno, A_Materno, Carrera, NUA) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmtEstudiante->execute([$nombre, $a_paterno, $a_materno, $carrera, $nua]);

        // 10. Obtener el ID del estudiante recién creado
        $id_estudiante_nuevo = $pdo->lastInsertId();

        // 11. PASO B: Insertar en la tabla 'Usuarios'
        $stmtUsuario = $pdo->prepare(
            "INSERT INTO Usuarios (User, password, ID_Estudiante) 
             VALUES (?, ?, ?)"
        );
        $stmtUsuario->execute([$username, $password_hash, $id_estudiante_nuevo]);

        // 12. Si ambos INSERT fueron exitosos, confirmamos la transacción
        $pdo->commit();
        
        $response['success'] = true;
        $response['message'] = '¡Registro exitoso! Ahora puedes iniciar sesión.';

    } catch (\Exception $e) {
        // 13. Si algo falló, deshacemos la transacción
        $pdo->rollBack();
        
        $response['message'] = 'Error en el registro: ' . $e->getMessage();
    }
}

// 14. Enviamos la respuesta JSON
echo json_encode($response);