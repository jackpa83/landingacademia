<?php
session_start();
require_once "../conexion.php"; // Asegúrate de ajustar la ruta si está en otra carpeta

if (isset($_SESSION['user_id'])) {
    $id_usuario = $_SESSION['user_id'];
    
    // Registrar actividad de cierre de sesión
    $accion = "Cierre de sesión";
    $descripcion = "El usuario cerró su sesión en el sistema.";
    $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (?, ?, ?)");
    $log->bind_param("iss", $id_usuario, $accion, $descripcion);
    $log->execute();
}

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al inicio de sesión
header("Location: ../InicioP.html");
exit();
?>
