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


// Obtener datos de la empresa
$sql = "SELECT tipoNegocio, tiposProductos, caracteristicas, preciosPoliticas FROM empresa WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if (!$empresa) {
    echo "No se encontró información de empresa registrada.";
    exit();
}

// Variables para mostrar
$tipoNegocio = strtolower($empresa['tipoNegocio']);
$tipoProducto = $empresa['tiposProductos'];
$caracteristica = $empresa['caracteristicas'];
$precio = $empresa['preciosPoliticas'];

// Recomendación personalizada
$mensajeExtra = match ($tipoNegocio) {
    "panadería" => "Amplía tu menú con desayunos rápidos y opciones saludables como panes integrales o sin azúcar. Promociona ediciones especiales por temporada.",
    "odontólogo" => "Publica videos educativos en redes, usa Google My Business para mejorar el posicionamiento local y ofrece promociones para primeras visitas.",
    "abogado" => "Crea contenido jurídico útil en blogs y redes, optimiza tu web y publica casos de éxito anónimos para generar confianza.",
    "publicista" => "Innova con formatos como filtros de AR, campañas en TikTok y estrategias basadas en datos que muestren resultados medibles.",
    "diseñador" => "Destaca tu proceso creativo en portafolios y redes. Usa Behance, Dribbble y un sitio web propio con casos de estudio detallados.",
    "tienda de ropa" => "Invierte en un e-commerce atractivo, usa redes sociales como escaparate y activa campañas de email marketing segmentadas.",
    "gimnasio" => "Ofrece clases únicas y personalizadas, fideliza con programas de recompensas y aprovecha plataformas como Instagram y YouTube.",
    "restaurante" => "Mejora el delivery con tu propio canal (WhatsApp, web), agrega valor con combos y empaques personalizados que fidelicen.",
    "consultoría" => "Vende tus servicios en formatos escalables (webinars, sesiones online), crea contenido educativo y sistematiza la captación de leads.",
    default => "Revisa si tus productos/servicios resuelven un problema específico, y comunica con claridad su valor. La diferenciación es clave."
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Productos / Servicios</title>
  <link rel="stylesheet" href="productos-servicios.css" />
</head>
<body>
  <header class="header">
    <div class="logo-container">
      <a href="../index.html"><img src="../images/Logo.png" alt="Logo" class="logo"></a>
    </div>
    <button class="hamburger" aria-label="Menú">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>
    <nav class="nav-links">
      <a href="info-general.php">Información General</a>
      <a href="productos-servicios.php" class="active">Productos / Servicios</a>
      <a href="clientes-mercado.php">Clientes y Mercado</a>
      <a href="canales.php">Canales</a>
      <a href="finanzas.php">Finanzas</a>
      <a href="perfil.php">Perfil</a>
      <a href="../Administrador/logout.php" class="logout-btn">Cerrar sesión</a>
    </nav>
  </header>

  <main class="contenido">
    <h1>Productos o Servicios</h1>
    <div class="producto-card">
      <h2>Producto/Servicio Principal</h2>
      <p><strong>Tipo:</strong> <?php echo htmlspecialchars($tipoProducto); ?></p>
      <p><strong>Características:</strong> <?php echo htmlspecialchars($caracteristica); ?></p>
      <p><strong>Precio o política de precios:</strong> <?php echo htmlspecialchars($precio); ?></p>
    </div>
    <div class="mensaje-extra">
      <h3>Recomendación para tu negocio:</h3>
      <p><?php echo $mensajeExtra; ?></p>
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
      localStorage.removeItem('empresa'); // ya no se usa, pero por seguridad
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
