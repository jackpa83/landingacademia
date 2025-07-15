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


// Obtener los datos de la empresa del usuario
$sql = "SELECT nombreEmpresa, tipoNegocio, tiempoMercado FROM empresa WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if (!$empresa) {
    echo "No se encontró información registrada para esta empresa.";
    exit();
}

// Variables extraídas
$nombreEmpresa = $empresa['nombreEmpresa'];
$tipoNegocio = $empresa['tipoNegocio'];
$tiempo = $empresa['tiempoMercado'];

// Lógica de descripción personalizada
$descripcion = match ($tipoNegocio) {
    'Panadería' => "Tu panadería, con $tiempo años de tradición y sabor, puede aumentar su alcance implementando combos, redes sociales, y panadería saludable.",
    'Odontólogo' => "Tu clínica odontológica, con $tiempo años de experiencia, puede atraer nuevos pacientes con marketing digital y presencia en Google Maps.",
    'Abogado' => "Tu despacho jurídico, con $tiempo años de trayectoria, puede posicionarse mediante contenido en redes, artículos legales y portales especializados.",
    'Publicista' => "Tu agencia con $tiempo años de creatividad puede expandirse mostrando resultados, dominando nuevas plataformas y automatizando procesos.",
    'Diseñador' => "Tu estudio de diseño, activo hace $tiempo años, tiene potencial para escalar con portafolios impactantes y estrategias inbound.",
    'Tienda de Ropa' => "Con $tiempo años en el mercado, tu tienda puede crecer integrando e-commerce, promociones inteligentes y redes sociales visuales.",
    'Gimnasio' => "Tu gimnasio, con $tiempo años, puede diferenciarse con clases innovadoras, campañas locales y fidelización online.",
    'Restaurante' => "Con $tiempo años, tu restaurante puede aumentar pedidos con delivery optimizado, marketing por WhatsApp y presencia digital constante.",
    'Consultoría' => "Con $tiempo años de consultoría, puedes escalar vendiendo tu metodología online y automatizando captación de clientes.",
    default => "Tu empresa con $tiempo años de existencia puede optimizar sus resultados mediante una estrategia digital integral y análisis de oportunidades.",
};
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Información General</title>
  <link rel="stylesheet" href="info-general.css" />
</head>
<body>
  <header class="header">
    <div class="logo-container">
      <a href="../index.html"><img src="../images/Logo.png" alt="Logo de la empresa" class="logo" /></a>
    </div>
    <button class="hamburger" aria-label="Menú">
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
      <span class="hamburger-line"></span>
    </button>
    <nav class="nav-links">
      <a href="info-general.php" class="active">Información General</a>
      <a href="productos-servicios.php">Productos / Servicios</a>
      <a href="clientes-mercado.php">Clientes y Mercado</a>
      <a href="canales.php">Canales</a>
      <a href="finanzas.php">Finanzas</a>
      <a href="perfil.php">Perfil</a>
      <a href="../Administrador/logout.php" class="logout-btn">Cerrar sesión</a>
    </nav>
  </header>

  <main class="content">
    <h1><?php echo htmlspecialchars($nombreEmpresa); ?></h1>
    <p><?php echo $descripcion; ?></p>
  </main>

  <footer>
  <div class="container">
    <div class="footer-content">
      <div class="footer-column">
        <h3>JxA-Marketing</h3>
        <p>Ayudar a empresas y emprendedores a destacar en el mundo digital con estrategias innovadoras y resultados medibles.</p>
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
      <p>&copy; 2025 MAKE Agency. All rights reserved.</p>
    </div>
  </div>
</footer>


  <script>
    function cerrarSesion() {
      localStorage.removeItem('empresa'); // ya no se usa, pero lo dejamos por seguridad
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
