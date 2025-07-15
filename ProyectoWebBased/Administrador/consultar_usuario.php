<?php
session_start();

// Verificar si es admin
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}

include "../conexion.php";

// Obtener usuario a consultar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM datos WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: admin_panel.php?error=user_not_found");
    exit();
}

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Usuario</title>
    <link rel="stylesheet" href="adminpanel.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-nav">
            <h1>Detalles del Usuario</h1>
            <div class="admin-actions">
                <a href="admin_panel.php" class="btn btn-info">Volver</a>
            </div>
        </div>
        
        <div class="user-details">
            <div class="detail-row">
                <span class="detail-label">ID:</span>
                <span class="detail-value"><?php echo $user['id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nombre:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['Nombre']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['Email']); ?></span>
            </div>
             <div class="detail-row">
                <span class="detail-label">Contraseña:</span>
                <span class="detail-value"><?php echo htmlspecialchars($user['Contraseña']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Rol:</span>
                <span class="detail-value"><?php echo $user['rol']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Fecha de registro:</span>
                <span class="detail-value"><?php echo $user['fecha']; ?></span>
            </div>
        </div>
        


        <!-- Agregamos el estado en los detalles del usuario -->
<div class="detail-row">
    <span class="detail-label">Estado:</span>
    <span class="detail-value">
        <?php echo $user['bloqueado'] ? '<span class="badge badge-danger">Bloqueado</span>' : '<span class="badge badge-success">Activo</span>'; ?>
        <?php if ($user['bloqueado']): ?>
        <br><small>Bloqueado el: <?php echo $user['fecha_bloqueo']; ?></small>
        <?php endif; ?>
    </span>
</div>
<!-- Agregamos acciones de bloqueo/desbloqueo -->
<div class="form-actions">
    <?php if ($user['bloqueado']): ?>
    <a href="gestion_usuario.php?action=unblock&id=<?php echo $user['id']; ?>" class="btn btn-success">Desbloquear</a>
    <?php else: ?>
    <a href="gestion_usuario.php?action=block&id=<?php echo $user['id']; ?>" class="btn btn-warning">Bloquear</a>
    <?php endif; ?>
    <a href="modificar_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">Modificar</a>
    <?php if ($user['id'] != $_SESSION['user_id']): ?>
    <a href="eliminar_usuario.php?id=<?php echo $user['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')" class="btn btn-danger">Eliminar</a>
    <?php endif; ?>
</div>

    </div>
</body>
</html>