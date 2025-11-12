<?php
require 'php/db_connect.php';

//1. Seguridad: Verifica sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit; 
}

$id_estudiante = $_SESSION['id_estudiante'];
$estudiante_carrera = '';

//2. Obtener la carrera del estudiante (para filtrar la oferta)
try {
    $stmtCarrera = $pdo->prepare("SELECT Carrera FROM Estudiante WHERE ID_estudiante = ?");
    $stmtCarrera->execute([$id_estudiante]);
    $estudiante = $stmtCarrera->fetch();
    $estudiante_carrera = $estudiante['Carrera'];
} catch (\PDOException $e) {
    die("Error al obtener datos del estudiante: " . $e->getMessage());
}


// 3. (LISTA 1) Obtener las MATERIAS INSCRITAS
$materias_inscritas = [];
try {
    $stmtInscritas = $pdo->prepare(
       "SELECT 
            i.ID_inscripciones,
            u.Nombre AS MateriaNombre,
            p.Nombre AS ProfesorNombre,
            p.A_paterno AS ProfesorPaterno,
            c.Aula
        FROM Inscripciones AS i
        JOIN Cursos AS c ON i.ID_curso = c.ID_curso
        JOIN UDAS AS u ON c.ID_UDA = u.ID_UDAS
        JOIN Profesores AS p ON c.ID_profesor = p.ID_profesores
        WHERE i.ID_estudiante = ?"
    );
    $stmtInscritas->execute([$id_estudiante]);
    $materias_inscritas = $stmtInscritas->fetchAll();

} catch (\PDOException $e) {
     error_log("Error al buscar inscripciones: " . $e->getMessage());
}

// 4. (LISTA 2) Obtener las MATERIAS DISPONIBLES
// (Cursos de su carrera en los que NO está inscrito)
$materias_disponibles = [];
try {
    /*
     * Esta consulta busca en Cursos (la oferta)
     * que coincida con la carrera del estudiante
     * Y que el ID_curso NO ESTÉ en la tabla de Inscripciones
     * para ESE estudiante.
     */
    $sql_disponibles = "
        SELECT
            c.ID_curso,
            u.Nombre AS MateriaNombre,
            p.Nombre AS ProfesorNombre,
            p.A_paterno AS ProfesorPaterno,
            c.Aula,
            c.Horario
        FROM Cursos AS c
        JOIN UDAS AS u ON c.ID_UDA = u.ID_UDAS
        JOIN Profesores AS p ON c.ID_profesor = p.ID_profesores
        WHERE u.Carrera = ?
        AND c.ID_curso NOT IN (
            SELECT ID_curso FROM Inscripciones WHERE ID_estudiante = ?
        )";
        
    $stmtDisponibles = $pdo->prepare($sql_disponibles);
    $stmtDisponibles->execute([$estudiante_carrera, $id_estudiante]);
    $materias_disponibles = $stmtDisponibles->fetchAll();

} catch (\PDOException $e) {
     error_log("Error al buscar disponibles: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Materias</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard_styles.css"> 
    <link rel="stylesheet" href="css/gestion_styles.css">
</head>
<body>
    <div class="login-container dashboard-container">
        
        <a href="dashboard.php" style="float: right;">Volver a perfil</a>
        <h1>Gestión de Materias</h1>

        <?php
            // Esto mostrará un mensaje si la baja/alta fue exitosa
            // después de redirigirnos desde el handler.
            if (isset($_GET['status'])):
                $message = '';
                $class = '';
                if ($_GET['status'] === 'baja_ok') {
                    $message = 'Materia dada de baja exitosamente.';
                    $class = 'feedback-success';
                } elseif ($_GET['status'] === 'alta_ok') {
                    $message = 'Inscripción exitosa.';
                    $class = 'feedback-success';
                } elseif ($_GET['status'] === 'error') {
                    $message = 'Error: No se pudo completar la operación.';
                    $class = 'feedback-error';
                }
        ?>
            <div class="feedback-message <?php echo $class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>


        <div class="gestion-container">
            <h2>Mis Materias Inscritas</h2>
            <table class="materias-tabla">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Profesor</th>
                        <th>Aula</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($materias_inscritas)): ?>
                        <tr><td colspan="4" style="text-align: center;">No estás inscrito en materias.</td></tr>
                    <?php else: ?>
                        <?php foreach ($materias_inscritas as $materia): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($materia['MateriaNombre']); ?></td>
                                <td><?php echo htmlspecialchars($materia['ProfesorNombre'] . ' ' . $materia['ProfesorPaterno']); ?></td>
                                <td><?php echo htmlspecialchars($materia['Aula']); ?></td>
                                <td>
                                    <form action="php/gestion_baja.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="id_inscripcion" value="<?php echo $materia['ID_inscripciones']; ?>">
                                        <button type="submit" class="btn-gestion btn-baja">Dar de Baja</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="gestion-container">
            <h2>Materias Disponibles (Carrera: <?php echo htmlspecialchars($estudiante_carrera); ?>)</h2>
            <table class="materias-tabla">
                <thead>
                    <tr>
                        <th>Materia</th>
                        <th>Profesor</th>
                        <th>Aula</th>
                        <th>Horario</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($materias_disponibles)): ?>
                        <tr><td colspan="5" style="text-align: center;">No hay materias disponibles para tu carrera o ya estás inscrito en todas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($materias_disponibles as $materia): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($materia['MateriaNombre']); ?></td>
                                <td><?php echo htmlspecialchars($materia['ProfesorNombre'] . ' ' . $materia['ProfesorPaterno']); ?></td>
                                <td><?php echo htmlspecialchars($materia['Aula']); ?></td>
                                <td><?php echo htmlspecialchars($materia['Horario']); ?></td>
                                <td>
                                    <form action="php/gestion_alta.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="id_curso" value="<?php echo $materia['ID_curso']; ?>">
                                        <button type="submit" class="btn-gestion btn-alta">Inscribir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>