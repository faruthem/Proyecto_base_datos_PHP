<?php
require 'db_connect.php'; // Inicia sesión y conecta a BD

// 1. Seguridad: verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit;
}

// 2. Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_inscripcion = $_POST['id_inscripcion'] ?? 0;
    $id_estudiante = $_SESSION['id_estudiante'];
    
    if (empty($id_inscripcion)) {
        header('Location: ../gestionar_materias.php?status=error');
        exit;
    }

    try {
        // 3. ¡Seguridad Clave!
        // Un alumno no puede borrar la inscripción de otro.
        $stmt = $pdo->prepare(
            "DELETE FROM Inscripciones 
             WHERE ID_inscripciones = ? AND ID_estudiante = ?"
        );
        
        $stmt->execute([$id_inscripcion, $id_estudiante]);
        
        // 4. Verificamos si realmente se borró algo
        if ($stmt->rowCount() > 0) {
            header('Location: ../gestionar_materias.php?status=baja_ok');
        } else {
            // No se borró nada (quizás intentó borrar algo que no era suyo)
            throw new \Exception('No se encontró la inscripción o no tiene permisos.');
        }

    } catch (\Exception $e) {
        error_log("Error al dar de baja: " . $e->getMessage());
        header('Location: ../gestionar_materias.php?status=error');
    }
    
} else {
    header('Location: ../gestionar_materias.php');
}
exit;