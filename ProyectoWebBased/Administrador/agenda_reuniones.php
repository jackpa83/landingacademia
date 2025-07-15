<?php
session_start();
require_once "../conexion.php";

// Proteger solo para administradores
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}

// Eliminar reunión si se envió solicitud
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM reunion WHERE id = $id");
    header("Location: agenda_reuniones.php");
    exit();
}

// Filtros
$busqueda = $_GET['buscar'] ?? '';
$where = "";

if ($busqueda !== '') {
    $busqueda = $conn->real_escape_string($busqueda);
    $where = "WHERE reunion.fecha LIKE '%$busqueda%' OR reunion.email LIKE '%$busqueda%' OR datos.Nombre LIKE '%$busqueda%'";
}

// Consulta de reuniones
$sql = "SELECT reunion.id, reunion.fecha, reunion.nombreEmpresa, reunion.email, datos.Nombre 
        FROM reunion 
        JOIN datos ON reunion.id_usuario = datos.id 
        $where
        ORDER BY reunion.fecha ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Reuniones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .top-nav {
            background-color: #2c3e50;
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-nav h1 {
            margin: 0;
            font-size: 18px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-links a {
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .nav-links a:hover {
            background-color: #2980b9;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 30px 20px;
            background-color: white;
            border-radius: 10px;
            margin-top: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .search-form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 280px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button, .nav-links a.logout {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

          .nav-links a.logout {
    background-color: #e74c3c;
  }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #3498db;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        .btn-volver {
            display: inline-block;
            margin-top: 30px;
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            text-decoration: none;
            border-radius: 5px;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                width: 100%;
                flex-direction: column;
                margin-top: 10px;
            }

            .nav-links.show {
                display: flex;
            }
        }
    </style>
</head>
<body>

<!-- Navegación -->
<div class="top-nav">
    <h1>📅Agenda de Reuniones</h1>
    <button class="menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('show')">☰</button>
    <div class="nav-links">
    <a href="admin_panel.php">🧍Usuarios</a>
    <a href="actividad_log.php">📋 Actividad</a>
    <a href="dashboard_admin.php">📊 Dashboard</a>
    <a href="logout.php" class="logout">🚪 Cerrar Sesión</a>
    </div>
</div>

<div class="container">

    <h2>📅 Agenda de Reuniones</h2>

    <div class="search-form">
        <form method="GET">
            <input type="text" name="buscar" placeholder="Buscar por fecha, correo o usuario" value="<?= htmlspecialchars($busqueda) ?>">
            <button type="submit">Buscar</button>
            <a href="agenda_reuniones.php" class="btn-volver">Limpiar</a>
        </form>
    </div>

    <?php if ($resultado->num_rows > 0): ?>
        <table>
            <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Empresa</th>
                <th>Acción</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['fecha']) ?></td>
                    <td><?= htmlspecialchars($row['Nombre']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['nombreEmpresa']) ?></td>
                    <td><a href="?eliminar=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('¿Eliminar esta reunión?')">Eliminar</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No hay reuniones encontradas.</p>
    <?php endif; ?>

</div>

</body>
</html>
