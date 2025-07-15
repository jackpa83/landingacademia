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


// Obtener información de canales desde la tabla empresa
$sql = "SELECT canales, presenciaDigital FROM empresa WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if (!$empresa) {
    echo "No se encontraron datos de empresa.";
    exit();
}

$canales = $empresa['canales'];
$presencia = $empresa['presenciaDigital'];

$recomendacionesCanales = [
    "Sí" => "Aprovecha tus canales actuales para probar campañas multicanal, medir resultados y escalar lo que funcione.",
    "No" => "Es hora de comenzar con al menos una campaña básica. Puedes iniciar con redes sociales o Google Ads de bajo costo."
];

$recomendacionesPresencia = [
    "Alta" => "Sigue innovando. Evalúa nuevas plataformas, SEO avanzado y automatizaciones.",
    "Media" => "Invierte en una web profesional, contenido regular y campañas pagadas.",
    "Baja" => "Prioriza lo básico: redes sociales, página web simple, perfil en Google y WhatsApp Business."
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Canales</title>
  <link rel="stylesheet" href="Canales.css" />
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
      <a href="canales.php" class="active">Canales</a>
      <a href="finanzas.php">Finanzas</a>
      <a href="perfil.php">Perfil</a>
      <a href="../Administrador/logout.php" class="logout-btn">Cerrar sesión</a>
    </nav>
  </header>

  <main class="main-content">
    <div class="container">
      <h1>Canales de Comunicación y Venta</h1>
      <div class="canales-box">
        <div class="info-section">
          <h3><i class="icon icon-channels"></i> Canales actuales</h3>
          <p class="data-value"><?php echo htmlspecialchars($canales); ?></p>
          <p class="recommendation"><?php echo $recomendacionesCanales[$canales] ?? 'Sin recomendación disponible.'; ?></p>
        </div>

        <div class="info-section">
          <h3><i class="icon icon-digital"></i> Presencia digital</h3>
          <p class="data-value"><?php echo htmlspecialchars($presencia); ?></p>
          <p class="recommendation"><?php echo $recomendacionesPresencia[$presencia] ?? 'Sin recomendación disponible.'; ?></p>
        </div>
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
