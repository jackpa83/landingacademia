<?php
session_start();

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}

include "../conexion.php";

// Aquí va la lógica de manejo de usuarios (crear, eliminar, etc.)
// ...
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
        }

        /* Navbar */
        .navbar {
            background-color: #2c3e50;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            flex-wrap: wrap;
        }
        .navbar h1 {
            font-size: 18px;
        }
        .nav-links {
            display: flex;
            gap: 10px;
        }
        .nav-links a {
            text-decoration: none;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
        }
        .nav-links a.logout {
            background-color: #e74c3c;
        }

        /* Responsive toggle */
        .menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                width: 100%;
                display: none;
                margin-top: 10px;
            }
            .nav-links.show {
                display: flex;
            }
            .menu-toggle {
                display: block;
                background: none;
                border: none;
                color: white;
            }
        }

        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-success { background-color: #2ecc71; }
        .btn-danger  { background-color: #e74c3c; }
        .btn-warning { background-color: #f39c12; color: white; }
        .btn-info    { background-color: #3498db; }
        .badge {
            padding: 5px 10px;
            border-radius: 10px;
            color: white;
            font-size: 13px;
        }
        .badge-success { background-color: #27ae60; }
        .badge-danger  { background-color: #c0392b; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <h1>📋 Panel de Administración</h1>
    <button class="menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('show')">☰</button>
    <div class="nav-links">
        <a href="agenda_reuniones.php">📅 Ver Agenda</a>
        <a href="actividad_log.php">🗓️ Actividad</a>
        <a href="dashboard_admin.php">📊 Dashboard</a>
        <a href="logout.php" class="logout">🚪 Cerrar Sesión</a>
    </div>
</div>

<!-- CONTENIDO -->
<div class="admin-container">
    <p>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?> (Administrador)</p>

    <!-- Ejemplo de tabla de usuarios -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM datos";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>".htmlspecialchars($row['Nombre'])."</td>";
                echo "<td>".htmlspecialchars($row['Email'])."</td>";
                echo "<td>{$row['rol']}</td>";
                echo "<td>" . ($row['bloqueado'] ? '<span class="badge badge-danger">Bloqueado</span>' : '<span class="badge badge-success">Activo</span>') . "</td>";
                echo "<td>";
                echo $row['bloqueado']
                    ? "<a href='gestion_usuario.php?action=unblock&id={$row['id']}' class='btn btn-success'>Desbloquear</a> "
                    : "<a href='gestion_usuario.php?action=block&id={$row['id']}' class='btn btn-warning'>Bloquear</a> ";
                echo "<a href='consultar_usuario.php?id={$row['id']}' class='btn btn-info'>Consultar</a> ";
                echo "<a href='modificar_usuario.php?id={$row['id']}' class='btn btn-warning'>Modificar</a> ";
                if ($row['id'] != $_SESSION['user_id']) {
                    echo "<a href='eliminar_usuario.php?id={$row['id']}' onclick='return confirm(\"¿Eliminar este usuario?\")' class='btn btn-danger'>Eliminar</a>";
                }
                echo "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
