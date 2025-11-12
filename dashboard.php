<?php
// 1. Incluimos la conexión a la BD y se inicia la sesión
require 'php/db_connect.php';

// 2. Verificamos si el usuario está "logeado"
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit; 
}

// 3. Obtenemos los datos completos del estudiante
$estudiante = null;
try {
    // Pedimos ahora NUA, Carrera y el nuevo campo Promedio
    $stmtEstudiante = $pdo->prepare(
        "SELECT Nombre, A_Paterno, A_Materno, NUA, Carrera, Promedio 
         FROM Estudiante 
         WHERE ID_estudiante = ?"
    );
    $stmtEstudiante->execute([$_SESSION['id_estudiante']]);
    $estudiante = $stmtEstudiante->fetch();

} catch (\PDOException $e) {
    error_log("Error al buscar datos del estudiante: " . $e->getMessage());
    // (Manejar error, por ahora dejamos que $estudiante sea null)
}

// Si por alguna razón no encontramos al estudiante (borrado de BD, etc.)
if (!$estudiante) {
    // Destruimos la sesión y lo mandamos al login
    session_destroy();
    header('Location: index.html');
    exit;
}

// 4. Obtenemos las inscripciones (la tabla de materias)
$inscripciones = [];
try {
// Esta consulta ahora une 5 tablas de forma normalizada
    $stmtInscripciones = $pdo->prepare(
       "SELECT 
            u.Nombre AS MateriaNombre,
            p.Nombre AS ProfesorNombre,
            p.A_paterno AS ProfesorPaterno,
            p.A_materno AS ProfesorMaterno,
            c.Aula
        FROM Inscripciones AS i
        JOIN Cursos AS c ON i.ID_curso = c.ID_curso
        JOIN UDAS AS u ON c.ID_UDA = u.ID_UDAS
        JOIN Profesores AS p ON c.ID_profesor = p.ID_profesores
        WHERE i.ID_estudiante = ?
        ORDER BY u.Nombre"
    );
    $stmtInscripciones->execute([$_SESSION['id_estudiante']]);
    $inscripciones = $stmtInscripciones->fetchAll();

} catch (\PDOException $e) {
     error_log("Error al buscar inscripciones: " . $e->getMessage());
     // La página cargará pero la tabla de materias estará vacía
}

// 5. Preparamos los datos para mostrar (con seguridad)
$nombre_completo = htmlspecialchars($estudiante['Nombre'] . ' ' . $estudiante['A_Paterno'] . ' ' . $estudiante['A_Materno']);
$nua = htmlspecialchars($estudiante['NUA']);
$carrera = htmlspecialchars($estudiante['Carrera']);
// Formateamos el promedio a 2 decimales
$promedio = number_format($estudiante['Promedio'], 2);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard_styles.css">
</head>
<body>

    <div class="login-container dashboard-container">
        <a href="logout.php" class="logout-link">Cerrar Sesión</a>
        
        <div class="header-info">
            <h2>Mi Información</h2>
            <h1><?php echo $nombre_completo; ?></h1>
            <div class="info-grid">
                <div><span>NUA:</span> <?php echo $nua; ?></div>
                <div><span>Carrera:</span> <?php echo $carrera; ?></div>
                <div><span>Promedio:</span> <?php echo $promedio; ?></div>
            </div>
        </div>

        <h2>Mis Materias Inscritas</h2>

        <table class="materias-tabla">
            <thead>
                <tr>
                    <th>Materia (UDA)</th>
                    <th>Profesor</th>
                    <th>Aula</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inscripciones)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No tienes materias inscritas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inscripciones as $materia): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($materia['MateriaNombre']); ?></td>
                            <td>
                                <?php 
                                // Creamos el nombre completo del profesor
                                echo htmlspecialchars(
                                    $materia['ProfesorNombre'] . ' ' .
                                    $materia['ProfesorPaterno'] . ' ' .
                                    $materia['ProfesorMaterno']
                                ); 
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($materia['Aula']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="gestion-materias">
            <h3>Gestionar Materias</h3>
            <p>Inscríbete en nuevas materias o da de baja las actuales.</p>
            <a href="gestionar_materias.php" class="btn-submit" style="width: auto; padding: 0.75rem 1.5rem;">Gestión de materias</a>
        </div>

    </div>
</body>
</html>