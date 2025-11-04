<?php
session_start(); // 1. Reanudamos la sesión existente

// 2. Vaciamos todas las variables de sesión
$_SESSION = [];

// 3. Destruimos la sesión
session_destroy();

// 4. Redirigimos al usuario al login
header('Location: index.html');
exit;