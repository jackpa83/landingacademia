<?php
session_start([
    'cookie_path' => '/'
]);

require_once '../conexion.php'; 

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Acceso denegado. Inicia sesión.'); window.location.href='../InicioP.html';</script>";
    exit();
}

$id_usuario = $_SESSION['user_id'];


$sql = "SELECT ingresosMensuales, gastoMensual, presupuestoMarketing FROM empresa WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if (!$empresa) {
    echo "No se encontraron datos financieros.";
    exit();
}

$ingreso = (float) $empresa['ingresosMensuales'];
$gasto = (float) $empresa['gastoMensual'];
$marketing = (float) $empresa['presupuestoMarketing'];

$ingresoProyectado = $ingreso * 1.35;
$gananciaActual = $ingreso - $gasto;
$gananciaProyectada = $ingresoProyectado - $gasto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Finanzas</title>
  <link rel="stylesheet" href="finanzas.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <header class="header">
    <div class="logo-container">
      <a href="../index.html"><img src="../images/Logo.png" alt="Logo" class="logo" /></a>
    </div>
    <button class="hamburger" aria-label="Menú">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>
    <nav class="nav-links">
      <a href="info-general.php">Información General</a>
      <a href="productos-servicios.php">Productos / Servicios</a>
      <a href="clientes-mercado.php">Clientes y Mercado</a>
      <a href="canales.php">Canales</a>
      <a href="finanzas.php" class="active">Finanzas</a>
      <a href="perfil.php">Perfil</a>
      <a href="../Administrador/logout.php" class="logout-btn">Cerrar sesión</a>
    </nav>
  </header>

  <main class="main-content">
    <div class="container">
      <h1>Proyección Financiera</h1>

      <div class="finance-summary">
        <div class="finance-item"><h3>Ingresos actuales</h3><p>$<?php echo number_format($ingreso, 2); ?></p></div>
        <div class="finance-item"><h3>Gastos mensuales</h3><p>$<?php echo number_format($gasto, 2); ?></p></div>
        <div class="finance-item"><h3>Presupuesto marketing</h3><p>$<?php echo number_format($marketing, 2); ?></p></div>
        <div class="finance-item highlight"><h3>Ingresos proyectados (+35%)</h3><p>$<?php echo number_format($ingresoProyectado, 2); ?></p></div>
        <div class="finance-item"><h3>Ganancia actual</h3><p>$<?php echo number_format($gananciaActual, 2); ?></p></div>
        <div class="finance-item highlight"><h3>Ganancia proyectada</h3><p>$<?php echo number_format($gananciaProyectada, 2); ?></p></div>
      </div>

      <div class="chart-container" style="margin-top:30px;">
        <canvas id="graficaIngresos"></canvas>
      </div>

      <div class="btns" style="margin-top: 30px;">
        <button onclick="window.print()" class="print-btn">🖨️ Descargar Graficas</button>
                            <form action="generarPDF.php" method="get" target="_blank" style="display:inline;">
                            <button type="submit" class="print-btn">📥 Descargar Análisis Completo en PDF</button>
                            </form>
      </div>
    </div>
  </main>

  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-column">
          <h3>JxA-Marketing</h3>
          <p>Ayudamos a empresas a crecer en el mundo digital con estrategias innovadoras.</p>
        </div>
        <div class="footer-column">
          <h3>Servicios</h3>
          <ul>
            <li><a href="https://josuearistimuno.com.br/marketing-y-ventas/">Marketing y Ventas</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Company</h3>
          <ul>
            <li><a href="../Footer/Acuerdo.html">Acuerdo de términos del servicio</a></li>
            <li><a href="../Footer/Politica.html">Política de privacidad</a></li>
          </ul>
        </div>
        <div class="footer-column">
          <h3>Contact</h3>
          <ul>
            <li><a href="mailto:leonardopiedra8@gmail.com">Correo Empresarial</a></li>
            <li><a href="https://w.app/ic0kxm">Whatsapp</a></li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        <p>&copy; 2025 MAKE Agency. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  <script>
    function cerrarSesion() {
      localStorage.removeItem('empresa');
      window.location.href = '../InicioP.html';
    }

    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('graficaIngresos').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Ingresos actuales', 'Ingresos proyectados'],
          datasets: [{
            label: 'Ingresos Mensuales',
            data: [<?php echo $ingreso; ?>, <?php echo $ingresoProyectado; ?>],
            backgroundColor: ['#3498db', '#2ecc71'],
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { callback: value => '$' + value }
            }
          }
        }
      });

      const hamburger = document.querySelector('.hamburger');
      const navLinks = document.querySelector('.nav-links');
      hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
      });
    });
  </script>
</body>
</html>
