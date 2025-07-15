<?php
session_start();

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}

include "../conexion.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // No permitir auto-eliminación
    if ($id != $_SESSION['user_id']) {
        $query = "DELETE FROM datos WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            header("Location: admin_panel.php?success=delete");
        } else {
            header("Location: admin_panel.php?error=delete");
        }
    } else {
        header("Location: admin_panel.php?error=self_delete");
    }
} else {
    header("Location: admin_panel.php");
}
exit();
?>