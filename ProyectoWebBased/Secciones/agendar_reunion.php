<!DOCTYPE html>
<?php
session_start();
require_once "../conexion.php";

// Validar sesión
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Acceso denegado. Inicia sesión.'); window.location.href='../InicioP.html';</script>";
    exit();
}

$id_usuario = $_SESSION['user_id'];

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT Email FROM datos WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Obtener nombre de la empresa
$stmt2 = $conn->prepare("SELECT nombreEmpresa FROM empresa WHERE id_usuario = ?");
$stmt2->bind_param("i", $id_usuario);
$stmt2->execute();
$result2 = $stmt2->get_result();
$empresa = $result2->fetch_assoc();

$email = $usuario['Email'] ?? '';
$nombreEmpresa = $empresa['nombreEmpresa'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fecha = $_POST['fecha'];

    // Verificar si la fecha ya está ocupada
    $check = $conn->prepare("SELECT id FROM reunion WHERE fecha = ?");
    $check->bind_param("s", $fecha);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('La fecha ya está ocupada. Por favor, elige otra.'); window.history.back();</script>";
        exit();
    }

    // Insertar reunión
    $insert = $conn->prepare("INSERT INTO reunion (id_usuario, fecha, nombreEmpresa, email) VALUES (?, ?, ?, ?)");
    $insert->bind_param("isss", $id_usuario, $fecha, $nombreEmpresa, $email);

    if ($insert->execute()) {
        // Registrar en actividad_log
        $accion = "Agendar reunión";
        $descripcion = "El usuario agendó una reunión para la fecha $fecha.";
        $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (?, ?, ?)");
        $log->bind_param("iss", $id_usuario, $accion, $descripcion);
        $log->execute();

        echo "<script>alert('Reunión agendada correctamente.'); window.location.href='../index.html';</script>";
        exit();
    } else {
        echo "<script>alert('Error al agendar la reunión.'); window.history.back();</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Reunión</title>
    <link rel="stylesheet" href="../Estilo de inicio/Styles.css"> <!-- Usa tu hoja base si aplica -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ecf0f1, #ffffff);
        }

        .reunion-section {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
        }

        .form-box {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.3s ease-in-out;
        }

        .form-box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .form-box label {
            display: block;
            margin: 12px 0 6px;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-box input {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-box input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.4);
        }

        .form-box input[readonly] {
            background-color: #f0f0f0;
        }

        .form-box button {
            margin-top: 25px;
            background-color: #3498db;
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-box button:hover {
            background-color: #2980b9;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .form-box {
                padding: 25px;
            }
        }
    </style>
</head>
<body>

<div class="reunion-section">
    <div class="form-box">
        <h2>📅 Agendar Reunión</h2>
        <form method="POST">
            <label>Correo:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>

            <label>Empresa:</label>
            <input type="text" name="empresa" value="<?= htmlspecialchars($nombreEmpresa) ?>" readonly>

            <label>Fecha para la reunión:</label>
            <input type="date" name="fecha" required min="<?= date('Y-m-d') ?>">

            <button type="submit">Confirmar Reunión</button>
        </form>
    </div>
</div>

</body>
</html>

