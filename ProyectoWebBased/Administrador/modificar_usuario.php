<?php
session_start();

// Verificar si es admin
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}

include "../conexion.php";

// Obtener usuario a modificar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM datos WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: admin_panel.php?error=user_not_found");
    exit();
}

$user = mysqli_fetch_assoc($result);

// Procesar modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $rol = mysqli_real_escape_string($conn, $_POST['rol']);
    
    $query = "UPDATE datos SET Nombre='$name', Email='$email', rol='$rol' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header("Location: admin_panel.php?success=update");
    } else {
        header("Location: admin_panel.php?error=update");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="adminpanel.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-nav">
            <h1>Modificar Usuario</h1>
            <div class="admin-actions">
                <a href="admin_panel.php" class="btn btn-info">Volver</a>
            </div>
        </div>
        
        <form method="post" action="modificar_usuario.php?id=<?php echo $id; ?>">
            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo htmlspecialchars($user['Nombre']); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($user['Email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="usuario" <?php echo $user['rol'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                    <option value="admin" <?php echo $user['rol'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>
            
            <div class="form-actions">
                <a href="consultar_usuario.php?id=<?php echo $id; ?>" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>