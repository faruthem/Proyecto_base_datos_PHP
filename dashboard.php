<?php
// 1. Incluimos la conexión a la BD.
// db_connect.php también inicia la sesión (session_start()), 
// por lo que no necesitamos llamarlo de nuevo.
require 'php/db_connect.php';

// 2. Verificamos si el usuario está "logeado"
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit; 
}

// 3. (NUEVO) Obtenemos los datos completos del estudiante
// Ya que estamos logeados, sabemos quién es por $_SESSION['id_estudiante']
$nua_estudiante = 'N/A'; // Valor por defecto
$nombre_completo = htmlspecialchars($_SESSION['username']); // Usamos el username como fallback

try {
    $stmt = $pdo->prepare("SELECT Nombre, A_Paterno, NUA FROM Estudiante WHERE ID_estudiante = ?");
    $stmt->execute([$_SESSION['id_estudiante']]);
    $estudiante = $stmt->fetch();

    if ($estudiante) {
        // Formateamos un nombre más completo
        $nombre_completo = htmlspecialchars($estudiante['Nombre'] . ' ' . $estudiante['A_Paterno']);
        $nua_estudiante = htmlspecialchars($estudiante['NUA']);
    }

} catch (\PDOException $e) {
    // Manejar error si es necesario, pero no detenemos la página
    error_log("Error al buscar datos del dashboard: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos rápidos para el dashboard */
        .dashboard-container { text-align: center; }
        .logout-link { color: #007bff; text-decoration: none; font-weight: bold; }
        .info-label { font-weight: bold; color: #555; }
    </style>
</head>
<body>
    <div class="login-container dashboard-container">
        <h1>¡Bienvenido, <?php echo $nombre_completo; ?>!</h1>
        <p>Has iniciado sesión correctamente.</p>
        
        <p><span class="info-label">NUA:</span> <?php echo $nua_estudiante; ?></p>
        
        <hr>
        <a href="logout.php" class="logout-link">Cerrar Sesión</a>
    </div>
</body>
</html>