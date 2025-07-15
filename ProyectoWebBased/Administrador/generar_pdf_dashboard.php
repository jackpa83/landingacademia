<?php
require '../conexion.php';
require_once '../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$totalUsuarios = $conn->query("SELECT COUNT(*) FROM datos")->fetch_row()[0];
$usuariosConEmpresa = $conn->query("SELECT COUNT(*) FROM empresa")->fetch_row()[0];
$reuniones = $conn->query("SELECT COUNT(*) FROM reunion")->fetch_row()[0];

$roles = $conn->query("SELECT rol, COUNT(*) as cantidad FROM datos GROUP BY rol");

$actividades = $conn->query("SELECT a.fecha_hora, d.Nombre, a.tipo_accion, a.descripcion
    FROM actividad_log a
    LEFT JOIN datos d ON a.id_usuario = d.id
    ORDER BY a.fecha_hora DESC LIMIT 5");

$proximas = $conn->query("SELECT fecha, nombreEmpresa, email FROM reunion WHERE fecha >= CURDATE() ORDER BY fecha ASC LIMIT 5");

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; color: #2c3e50; }
        .section { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #2980b9; color: white; }
    </style>
</head>
<body>
    <h1>📄 Informe del Dashboard</h1>

    <div class="section">
        <h2>Resumen General</h2>
        <table>
            <tr><th>Total de Usuarios</th><td>' . $totalUsuarios . '</td></tr>
            <tr><th>Usuarios con Empresa</th><td>' . $usuariosConEmpresa . '</td></tr>
            <tr><th>Empresas Registradas</th><td>' . $usuariosConEmpresa . '</td></tr>
            <tr><th>Reuniones Agendadas</th><td>' . $reuniones . '</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Distribución de Roles</h2>
        <table>
            <thead><tr><th>Rol</th><th>Cantidad</th></tr></thead>
            <tbody>';
while ($rol = $roles->fetch_assoc()) {
    $html .= '<tr><td>' . ucfirst($rol['rol']) . '</td><td>' . $rol['cantidad'] . '</td></tr>';
}
$html .= '</tbody></table></div>';

$html .= '
<div class="section">
    <h2>Últimas Actividades</h2>
    <table>
        <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Descripción</th></tr></thead>
        <tbody>';
while ($a = $actividades->fetch_assoc()) {
    $html .= '<tr>
        <td>' . $a['fecha_hora'] . '</td>
        <td>' . ($a['Nombre'] ?? 'Desconocido') . '</td>
        <td>' . $a['tipo_accion'] . '</td>
        <td>' . $a['descripcion'] . '</td>
    </tr>';
}
$html .= '</tbody></table></div>';

$html .= '
<div class="section">
    <h2>Próximas Reuniones</h2>
    <table>
        <thead><tr><th>Fecha</th><th>Empresa</th><th>Email</th></tr></thead>
        <tbody>';
while ($r = $proximas->fetch_assoc()) {
    $html .= '<tr>
        <td>' . $r['fecha'] . '</td>
        <td>' . $r['nombreEmpresa'] . '</td>
        <td>' . $r['email'] . '</td>
    </tr>';
}
$html .= '</tbody></table></div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("informe_dashboard.pdf", ["Attachment" => false]);
exit;
