<?php
require 'db_connect.php'; // Inicia sesión y conecta a BD

// 1. Seguridad: verificar sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit;
}

// 2. Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_curso = $_POST['id_curso'] ?? 0;
    $id_estudiante = $_SESSION['id_estudiante'];
    
    if (empty($id_curso)) {
        header('Location: ../gestionar_materias.php?status=error');
        exit;
    }

    try {
        // 3. Verificamos que no esté ya inscrito (doble chequeo)
        $stmtCheck = $pdo->prepare("SELECT 1 FROM Inscripciones WHERE ID_estudiante = ? AND ID_curso = ?");
        $stmtCheck->execute([$id_estudiante, $id_curso]);
        if ($stmtCheck->fetch()) {
             throw new \Exception('Ya estás inscrito en este curso.');
        }

        // 4. Inscribimos al alumno
        $stmt = $pdo->prepare(
            "INSERT INTO Inscripciones (ID_estudiante, ID_curso) 
             VALUES (?, ?)"
        );
        $stmt->execute([$id_estudiante, $id_curso]);
        
        header('Location: ../gestionar_materias.php?status=alta_ok');

    } catch (\Exception $e) {
        error_log("Error al dar de alta: " . $e->getMessage());
        header('Location: ../gestionar_materias.php?status=error');
    }
    
} else {
    header('Location: ../gestionar_materias.php');
}
exit;