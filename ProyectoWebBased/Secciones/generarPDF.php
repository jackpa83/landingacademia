<?php
session_start([
    'cookie_path' => '/'
]);

require_once '../conexion.php';
require_once '../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

if (!isset($_SESSION['user_id'])) {
    die("No se logueo el usuario");
    exit();
}

$id_usuario = $_SESSION['user_id'];

// Consulta los datos de la empresa
$sql = "SELECT * FROM empresa WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if (!$empresa) {
    die("No se encontraron datos.");
}

extract($empresa);

$gananciaActual = $ingresosMensuales - $gastoMensual;
$ingresoProyectado = $ingresosMensuales * 1.35;
$gananciaProyectada = $ingresoProyectado - $gastoMensual;

$logo = "../images/Logo.png"; // ajusta si está en otra ruta

// HTML con estilos integrados
$html = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; font-size: 12px; }
    header { text-align: center; padding-bottom: 20px; border-bottom: 2px solid #27ae60; }
    header img { width: 100px; }
    h1 { color: #27ae60; font-size: 22px; margin-bottom: 0; }
    h2 { color: #2c3e50; margin-top: 20px; font-size: 16px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
    p { margin: 4px 0; }
    .seccion { margin-bottom: 20px; }
    .resumen { background-color: #ecf0f1; padding: 8px; border-radius: 5px; }
    footer { text-align: center; font-size: 10px; margin-top: 30px; color: #888; border-top: 1px solid #ccc; padding-top: 10px; }
  </style>
</head>
<body>

<header>
  <img src='$logo' alt='Logo' />
  <h1>Informe Estratégico de Empresa</h1>
</header>

<div class='seccion'>
  <h2>1. Información General</h2>
  <p><strong>Nombre:</strong> $nombreEmpresa</p>
  <p><strong>Tipo de Negocio:</strong> $tipoNegocio</p>
  <p><strong>Tiempo en el Mercado:</strong> $tiempoMercado años</p>
  <p><strong>Tamaño:</strong> $tamano</p>
</div>

<div class='seccion'>
  <h2>2. Producto o Servicio</h2>
  <p><strong>Tipo:</strong> $tiposProductos</p>
  <p><strong>Características:</strong> $caracteristicas</p>
  <p><strong>Política de Precios:</strong> $preciosPoliticas</p>
</div>

<div class='seccion'>
  <h2>3. Clientes y Mercado</h2>
  <p><strong>Público Objetivo:</strong> $publicoObjetivo</p>
  <p><strong>Cliente Ideal:</strong> $perfilCliente</p>
  <p><strong>Nivel de Satisfacción:</strong> $satisfaccion</p>
</div>

<div class='seccion'>
  <h2>4. Marketing y Canales</h2>
  <p><strong>Uso de Canales:</strong> $canales</p>
  <p><strong>Presencia Digital:</strong> $presenciaDigital</p>
</div>

<div class='seccion resumen'>
  <h2>5. Finanzas</h2>
  <p><strong>Ingresos Mensuales:</strong> $".number_format($ingresosMensuales, 2)."</p>
  <p><strong>Gastos Mensuales:</strong> $".number_format($gastoMensual, 2)."</p>
  <p><strong>Presupuesto de Marketing:</strong> $".number_format($presupuestoMarketing, 2)."</p>
  <p><strong>Ganancia Actual:</strong> $".number_format($gananciaActual, 2)."</p>
  <p><strong>Proyección de Ingresos (+35%):</strong> $".number_format($ingresoProyectado, 2)."</p>
  <p><strong>Ganancia Proyectada:</strong> $".number_format($gananciaProyectada, 2)."</p>
</div>

<footer>
  Informe generado automáticamente por el sistema JxA-Marketing.<br>
  &copy; 2025 MAKE Agency. Todos los derechos reservados.
</footer>

</body>
</html>
";

// Generar y mostrar PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("analisis_empresa_$nombreEmpresa.pdf", ["Attachment" => true]);
?>
