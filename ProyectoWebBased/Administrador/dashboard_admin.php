<?php
session_start();
require_once '../conexion.php';

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado.'); window.location.href='../InicioP.html';</script>";
    exit();
}

// Contadores generales
$totalUsuarios = $conn->query("SELECT COUNT(*) FROM datos")->fetch_row()[0];
$usuariosConEmpresa = $conn->query("SELECT COUNT(*) FROM empresa")->fetch_row()[0];
$reuniones = $conn->query("SELECT COUNT(*) FROM reunion")->fetch_row()[0];

// Distribución de roles
$roles = $conn->query("SELECT rol, COUNT(*) as cantidad FROM datos GROUP BY rol");
$labelsRoles = [];
$datosRoles = [];
while ($row = $roles->fetch_assoc()) {
    $labelsRoles[] = ucfirst($row['rol']);
    $datosRoles[] = $row['cantidad'];
}

// Últimas actividades
$actividades = $conn->query("SELECT a.fecha_hora, d.Nombre, a.tipo_accion, a.descripcion
                              FROM actividad_log a
                              LEFT JOIN datos d ON a.id_usuario = d.id
                              ORDER BY a.fecha_hora DESC LIMIT 5");

// Próximas reuniones
$proximas = $conn->query("SELECT fecha, nombreEmpresa, email FROM reunion WHERE fecha >= CURDATE() ORDER BY fecha ASC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f9f9f9; }

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
            gap: 10px;
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
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                margin-top: 10px;
            }
            .nav-links.show {
                display: flex;
            }
        }

        h2 { color: #2c3e50; text-align: center; margin: 30px 0; }
        .dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .card h3 { margin-bottom: 10px; color: #34495e; }
        canvas { width: 100% !important; height: 300px !important; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2980b9; color: #fff; }
        tr:hover { background-color: #f1f1f1; }

        .btn-volver {
            display: block;
            margin: 30px auto;
            background: #27ae60;
            color: white;
            padding: 10px 25px;
            border: none;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
        }
        .btn-volver:hover {
            background: #1e8449;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-logo">📊 Admin</div>
        <button class="menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('show')">☰</button>
        <div class="nav-links">
            <a href="admin_panel.php">🧍Usuarios</a>
            <a href="agenda_reuniones.php">📅 Ver Agenda</a>
            <a href="actividad_log.php">📋 Actividad</a>
            <a href="logout.php" class="logout">🚪 Cerrar Sesión</a>
        </div>
    </div>
</nav>

<h2>📊 Dashboard del Panel de Administrador</h2>

<div class="dashboard">
    <div class="card">
        <h3>Resumen General</h3>
        <canvas id="resumenChart"></canvas>
    </div>

    <div class="card">
        <h3>Distribución de Roles</h3>
        <canvas id="rolesChart"></canvas>
    </div>

    <div class="card">
        <h3>Últimas Actividades</h3>
        <table>
            <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Descripción</th></tr></thead>
            <tbody>
                <?php while ($a = $actividades->fetch_assoc()): ?>
                    <tr>
                        <td><?= $a['fecha_hora'] ?></td>
                        <td><?= $a['Nombre'] ?? 'Desconocido' ?></td>
                        <td><?= $a['tipo_accion'] ?></td>
                        <td><?= $a['descripcion'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Próximas Reuniones</h3>
        <table>
            <thead><tr><th>Fecha</th><th>Empresa</th><th>Email</th></tr></thead>
            <tbody>
                <?php while ($r = $proximas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $r['fecha'] ?></td>
                        <td><?= $r['nombreEmpresa'] ?></td>
                        <td><?= $r['email'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="generar_pdf_dashboard.php" class="btn-volver" target="_blank">📄 Descargar PDF</a>

<script>
    const resumenChart = document.getElementById('resumenChart').getContext('2d');
    new Chart(resumenChart, {
        type: 'bar',
        data: {
            labels: ['Usuarios', 'Con Empresa', 'Empresas', 'Reuniones'],
            datasets: [{
                label: 'Totales',
                data: [<?= $totalUsuarios ?>, <?= $usuariosConEmpresa ?>, <?= $usuariosConEmpresa ?>, <?= $reuniones ?>],
                backgroundColor: ['#2980b9', '#27ae60', '#f39c12', '#8e44ad']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Resumen General' }
            }
        }
    });

    const rolesChart = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesChart, {
        type: 'pie',
        data: {
            labels: <?= json_encode($labelsRoles) ?>,
            datasets: [{
                data: <?= json_encode($datosRoles) ?>,
                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#9b59b6']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Distribución de Roles' }
            }
        }
    });
</script>

</body>
</html>