<?php
session_start();

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}

include "../conexion.php";

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    
    // Usar consultas preparadas para seguridad
    if ($action === 'block') {
        $stmt = mysqli_prepare($conn, "UPDATE datos SET bloqueado = TRUE, fecha_bloqueo = NOW() WHERE id = ?");
        $success_msg = "Usuario bloqueado exitosamente";
        $error_msg = "Error al bloquear usuario";
    } elseif ($action === 'unblock') {
        $stmt = mysqli_prepare($conn, "UPDATE datos SET bloqueado = FALSE, intentos_fallidos = 0, fecha_bloqueo = NULL WHERE id = ?");
        $success_msg = "Usuario desbloqueado exitosamente";
        $error_msg = "Error al desbloquear usuario";
    }
    
    if (isset($stmt)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = $success_msg;
        } else {
            $_SESSION['error'] = $error_msg;
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Redirección con mensajes en sesión
header("Location: admin_panel.php");
exit();
?>