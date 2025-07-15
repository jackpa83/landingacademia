<?php
session_start();
require_once "../conexion.php";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Acceso denegado. Inicia sesión.'); window.location.href='../InicioP.html';</script>";
    exit();
}

$id_usuario = $_SESSION['user_id'];

// Obtener datos de la empresa
$stmt = $conn->prepare("SELECT * FROM empresa WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();

// Obtener citas del usuario
$citas = $conn->query("SELECT * FROM reunion WHERE id_usuario = $id_usuario");

// Actualizar datos de la empresa
if (isset($_POST['actualizar_empresa'])) {
    $nombreEmpresa = $_POST['nombreEmpresa'];
    $ingresosMensuales = $_POST['ingresosMensuales'];
    $gastoMensual = $_POST['gastoMensual'];
    $presupuestoMarketing = $_POST['presupuestoMarketing'];

    $update = $conn->prepare("UPDATE empresa SET nombreEmpresa = ?, ingresosMensuales = ?, gastoMensual = ?, presupuestoMarketing = ? WHERE id_usuario = ?");
    $update->bind_param("sdddi", $nombreEmpresa, $ingresosMensuales, $gastoMensual, $presupuestoMarketing, $id_usuario);
    $update->execute();

    echo "<script>alert('Empresa actualizada correctamente'); window.location.href='perfil.php';</script>";
}


// Cambiar contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_contrasena'])) {
    $nueva = $_POST['nueva_contrasena'];
    if (strlen($nueva) < 6) {
        echo "<script>alert('La contraseña debe tener al menos 6 caracteres');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE datos SET Contraseña=? WHERE id=?");
        $stmt->bind_param("si", $nueva, $id_usuario);
        $stmt->execute();
        echo "<script>alert('Contraseña actualizada'); window.location.href='perfil.php';</script>";
    }
}

// Eliminar cita
if (isset($_GET['eliminar_cita'])) {
    $id_cita = intval($_GET['eliminar_cita']);
    $stmt = $conn->prepare("DELETE FROM reunion WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_cita, $id_usuario);
    $stmt->execute();
    echo "<script>alert('Cita eliminada'); window.location.href='perfil.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="perfil.css">
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


<form method="POST" action="perfil.php" class="form-empresa">
  <h3>🧾 Información de la Empresa</h3>

  <label>Nombre de la Empresa:</label>
  <input type="text" name="nombreEmpresa" value="<?= $empresa['nombreEmpresa'] ?>" required>

  <label>Tipo de Negocio:</label>
  <input type="text" value="<?= $empresa['tipoNegocio'] ?>" readonly>

  <label>Tiempo en el mercado:</label>
  <input type="text" value="<?= $empresa['tiempoMercado'] ?>" readonly>

  <label>Tamaño de la empresa:</label>
  <input type="text" value="<?= $empresa['tamano'] ?>" readonly>

  <label>Tipos de productos o servicios:</label>
  <input type="text" value="<?= $empresa['tiposProductos'] ?>" readonly>

  <label>Características diferenciadoras:</label>
  <input type="text" value="<?= $empresa['caracteristicas'] ?>" readonly>

  <label>¿Cómo determina sus precios?</label>
  <input type="text" value="<?= $empresa['preciosPoliticas'] ?>" readonly>

  <label>Público objetivo:</label>
  <input type="text" value="<?= $empresa['publicoObjetivo'] ?>" readonly>

  <label>Perfil del cliente ideal:</label>
  <input type="text" value="<?= $empresa['perfilCliente'] ?>" readonly>

  <label>Nivel de satisfacción del cliente:</label>
  <input type="text" value="<?= $empresa['satisfaccion'] ?>" readonly>

  <label>Canales de comunicación:</label>
  <input type="text" value="<?= $empresa['canales'] ?>" readonly>

  <label>Presencia digital:</label>
  <input type="text" value="<?= $empresa['presenciaDigital'] ?>" readonly>

  <label>Ingresos mensuales estimados (USD):</label>
  <input type="number" name="ingresosMensuales" step="0.01" value="<?= $empresa['ingresosMensuales'] ?>" required>

  <label>Gasto mensual estimado (USD):</label>
  <input type="number" name="gastoMensual" value="<?= $empresa['gastoMensual'] ?>" required>

  <label>Presupuesto para marketing (USD):</label>
  <input type="number"  name="presupuestoMarketing" value="<?= $empresa['presupuestoMarketing'] ?>" required>

  <button type="submit" name="actualizar_empresa">💾 Guardar Cambios</button>
</form>



<h2>🔐 Cambiar Contraseña</h2>
<form method="POST">
    <input type="hidden" name="cambiar_contrasena" value="1">
    <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña" required>
    <button type="submit">Cambiar</button>
</form>

<h2>📅 Citas Agendadas</h2>
<?php if ($citas->num_rows > 0): ?>
    <table>
        <thead>
            <tr><th>Fecha</th><th>Empresa</th><th>Email</th><th>Acción</th></tr>
        </thead>
        <tbody>
            <?php while ($cita = $citas->fetch_assoc()): ?>
                <tr>
                    <td><?= $cita['fecha'] ?></td>
                    <td><?= $cita['nombreEmpresa'] ?></td>
                    <td><?= $cita['email'] ?></td>
                    <td><a href="?eliminar_cita=<?= $cita['id'] ?>" class="eliminar" onclick="return confirm('¿Eliminar esta cita?')">Eliminar</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No tienes citas agendadas.</p>
<?php endif; ?>

</body>
</html>
