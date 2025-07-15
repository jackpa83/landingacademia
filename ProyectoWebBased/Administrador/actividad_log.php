<?php
session_start();
require_once "../conexion.php";

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}

// Filtros
$nombreFiltro = $_GET['usuario'] ?? '';
$accionFiltro = $_GET['accion'] ?? '';
$fechaInicio = $_GET['fecha_inicio'] ?? '';
$fechaFin = $_GET['fecha_fin'] ?? '';

// Construir consulta dinámica
$condiciones = [];
$parametros = [];
$tipos = '';

if ($nombreFiltro !== '') {
    $condiciones[] = "d.Nombre LIKE ?";
    $parametros[] = "%$nombreFiltro%";
    $tipos .= 's';
}

if ($accionFiltro !== '') {
    $condiciones[] = "a.tipo_accion = ?";
    $parametros[] = $accionFiltro;
    $tipos .= 's';
}

if ($fechaInicio !== '') {
    $condiciones[] = "a.fecha_hora >= ?";
    $parametros[] = $fechaInicio . " 00:00:00";
    $tipos .= 's';
}

if ($fechaFin !== '') {
    $condiciones[] = "a.fecha_hora <= ?";
    $parametros[] = $fechaFin . " 23:59:59";
    $tipos .= 's';
}

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

$sql = "
    SELECT a.fecha_hora, d.Nombre, a.tipo_accion, a.descripcion
    FROM actividad_log a
    LEFT JOIN datos d ON a.id_usuario = d.id
    $where
    ORDER BY a.fecha_hora DESC
";

$stmt = $conn->prepare($sql);
if (!empty($tipos)) {
    $stmt->bind_param($tipos, ...$parametros);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Actividades</title>
    <style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-top: 20px;
}

form {
    max-width: 1000px;
    margin: 20px auto;
    padding: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: space-between;
}

input, select {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
    flex: 1 1 180px;
    min-width: 150px;
}

button {
    padding: 10px 15px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    white-space: nowrap;
}

thead {
    background-color: #2c3e50;
    color: #fff;
}

.table-wrapper {
    overflow-x: auto;
    margin: 20px;
}

.btn-volver {
    display: inline-block;
    margin: 30px auto;
    background: #3498db;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
}

/* Navbar responsive */
.navbar {
    background-color: #2c3e50;
    padding: 12px 20px;
    color: #fff;
    position: sticky;
    top: 0;
    z-index: 1000;
}
.navbar-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}
.navbar-logo {
    font-size: 18px;
    font-weight: bold;
}
.nav-links {
    display: flex;
    gap: 15px;
}
.nav-links a {
    color: #fff;
    text-decoration: none;
    background-color: #3498db;
    padding: 8px 12px;
    border-radius: 5px;
}
.nav-links a.logout {
    background-color: #e74c3c;
}
.menu-toggle {
    display: none;
}

.nav-links {
    display: flex !important;
    gap: 15px;
}

@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }

    .nav-links {
        display: none !important;
        flex-direction: column;
        width: 100%;
        margin-top: 10px;
    }

    .nav-links.show {
        display: flex !important;
    }
    }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-logo">📋 Actividad</div>
        <button class="menu-toggle">☰</button>
        <div class="nav-links">
                <a href="admin_panel.php">🧍Usuarios</a>
            <a href="agenda_reuniones.php">📅 Ver Agenda</a>
            <a href="dashboard_admin.php">📊 Dashboard</a>
            <a href="logout.php" class="logout">🚪 Cerrar Sesión</a>
        </div>
    </div>
</nav>

<h2>📝 Historial de Actividades</h2>

<form method="GET">
    <input type="text" name="usuario" placeholder="Nombre de usuario" value="<?= htmlspecialchars($nombreFiltro) ?>">
    <select name="accion">
        <option value="">Todas las acciones</option>
        <option <?= $accionFiltro === 'Inicio de sesión' ? 'selected' : '' ?>>Inicio de sesión</option>
        <option <?= $accionFiltro === 'Intento fallido' ? 'selected' : '' ?>>Intento fallido</option>
        <option <?= $accionFiltro === 'Bloqueo de usuario' ? 'selected' : '' ?>>Bloqueo de usuario</option>
        <option <?= $accionFiltro === 'Intento de acceso' ? 'selected' : '' ?>>Intento de acceso</option>
        <option <?= $accionFiltro === 'Cambio de contraseña' ? 'selected' : '' ?>>Cambio de contraseña</option>
    </select>
    <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>">
    <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>">
    <button type="submit">🔍 Filtrar</button>
</form>

<?php if ($result->num_rows > 0): ?>
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($log = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($log['fecha_hora']) ?></td>
                <td><?= htmlspecialchars($log['Nombre'] ?? 'Desconocido') ?></td>
                <td><?= htmlspecialchars($log['tipo_accion']) ?></td>
                <td><?= htmlspecialchars($log['descripcion']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php else: ?>
<p style="text-align:center;">No hay actividades registradas.</p>
<?php endif; ?>


<script>
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.nav-links');

    toggle.addEventListener('click', () => {
        nav.classList.toggle('show');
    });
</script>

</body>
</html>
