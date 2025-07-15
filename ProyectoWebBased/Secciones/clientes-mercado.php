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


$sql = "SELECT publicoObjetivo, perfilCliente, satisfaccion FROM empresa WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if (!$empresa) {
    echo "No se encontraron datos de empresa.";
    exit();
}

$objetivo = $empresa['publicoObjetivo'];
$perfil = $empresa['perfilCliente'];
$satisfaccion = $empresa['satisfaccion'];

$recomendaciones = [
    'publicoObjetivo' => [
        "Jóvenes (18-25 años)" => "Usa redes sociales activas, influencers y contenido visual creativo.",
        "Adultos (26-45 años)" => "Apuesta por contenido profesional, promociones, reseñas y fidelización.",
        "Mayores (46+ años)" => "Prioriza la atención personalizada, mensajes claros y medios tradicionales.",
        "Empresas" => "Ofrece soluciones B2B claras, estudios de caso y canales como LinkedIn.",
        "Familias" => "Crea campañas emocionales, packs familiares y ofertas integradas."
    ],
    'perfilCliente' => [
        "Clientes frecuentes" => "Premia la fidelidad con descuentos, puntos o contenido exclusivo.",
        "Clientes de alto poder adquisitivo" => "Enfócate en exclusividad, lujo, valor agregado y experiencias premium.",
        "Clientes ocasionales" => "Convierte con remarketing, ofertas limitadas y mensajes directos.",
        "Empresas que buscan servicios" => "Ofrece soluciones claras, confianza profesional y garantía de resultados."
    ],
    'satisfaccion' => [
        "Muy Satisfecho" => "Fideliza con experiencias personalizadas y pide testimonios públicos.",
        "Satisfecho" => "Recoge feedback para convertir en promotores activos.",
        "Regular" => "Detecta puntos débiles y realiza mejoras rápidas para sorprender.",
        "Insatisfecho" => "Aplica encuestas, mejora urgente y realiza seguimiento proactivo."
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Clientes y Mercado</title>
  <link rel="stylesheet" href="clientes-mercado.css" />
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
      <a href="clientes-mercado.php" class="active">Clientes y Mercado</a>
      <a href="canales.php">Canales</a>
      <a href="finanzas.php">Finanzas</a>
      <a href="perfil.php">Perfil</a>
      <a href="../Administrador/logout.php" class="logout-btn">Cerrar sesión</a>
    </nav>
  </header>

  <main class="main-content">
    <div class="container">
      <h1>Clientes y Mercado</h1>
      <div class="cliente-box">
        <div class="info-section">
          <h3><i class="icon icon-users"></i> Público Objetivo</h3>
          <p class="data-value"><?php echo htmlspecialchars($objetivo); ?></p>
          <p class="recommendation"><?php echo $recomendaciones['publicoObjetivo'][$objetivo] ?? 'Sin recomendación disponible.'; ?></p>
        </div>

        <div class="info-section">
          <h3><i class="icon icon-profile"></i> Perfil del Cliente Ideal</h3>
          <p class="data-value"><?php echo htmlspecialchars($perfil); ?></p>
          <p class="recommendation"><?php echo $recomendaciones['perfilCliente'][$perfil] ?? 'Sin recomendación disponible.'; ?></p>
        </div>

        <div class="info-section">
          <h3><i class="icon icon-heart"></i> Nivel de Satisfacción</h3>
          <p class="data-value"><?php echo htmlspecialchars($satisfaccion); ?></p>
          <p class="recommendation"><?php echo $recomendaciones['satisfaccion'][$satisfaccion] ?? 'Sin recomendación disponible.'; ?></p>
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
