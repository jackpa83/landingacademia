<?php
session_start();
require_once '../conexion.php'; 
include '../Administrador/verificar_sesion.php';

// Validar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    die("Error: El usuario no está logueado.");
    exit();
}

$id_usuario = $_SESSION['user_id'];

// Obtener datos del formulario
$nombreEmpresa = $_POST['nombreEmpresa'];
$tipoNegocio = $_POST['tipoNegocio'];
$tiempoMercado = $_POST['tiempoMercado'];
$tamano = $_POST['tamano'];
$tiposProductos = $_POST['tiposProductos'];
$caracteristicas = $_POST['caracteristicas'];
$preciosPoliticas = $_POST['preciosPoliticas'];
$publicoObjetivo = $_POST['publicoObjetivo'];
$perfilCliente = $_POST['perfilCliente'];
$satisfaccion = $_POST['satisfaccion'];
$canales = $_POST['canales'];
$presenciaDigital = $_POST['presenciaDigital'];
$ingresosMensuales = $_POST['ingresosMensuales'];
$gastoMensual = $_POST['gastoMensual'];
$presupuestoMarketing = $_POST['presupuestoMarketing'];

// Verifica si ya existe una empresa registrada por este usuario
$verificar = $conn->prepare("SELECT id FROM empresa WHERE id_usuario = ?");
$verificar->bind_param("i", $id_usuario);
$verificar->execute();
$verificar->store_result();

if ($verificar->num_rows > 0) {
    header("Location: ../index.html");
    exit();
}

// Insertar en la base de datos
$sql = "INSERT INTO empresa (
    id_usuario, nombreEmpresa, tipoNegocio, tiempoMercado, tamano,
    tiposProductos, caracteristicas, preciosPoliticas,
    publicoObjetivo, perfilCliente, satisfaccion,
    canales, presenciaDigital, ingresosMensuales,
    gastoMensual, presupuestoMarketing
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ississsssssssddd",
    $id_usuario,
    $nombreEmpresa,
    $tipoNegocio,
    $tiempoMercado,
    $tamano,
    $tiposProductos,
    $caracteristicas,
    $preciosPoliticas,
    $publicoObjetivo,
    $perfilCliente,
    $satisfaccion,
    $canales,
    $presenciaDigital,
    $ingresosMensuales,
    $gastoMensual,
    $presupuestoMarketing
);

if ($stmt->execute()) {
    // 🔴 Registrar actividad
    $accion = "Registro de empresa";
    $descripcion = "El usuario registró la información de su empresa.";
    $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (?, ?, ?)");
    $log->bind_param("iss", $id_usuario, $accion, $descripcion);
    $log->execute();

    header("Location: ../index.html");
    exit();
} else {
    echo "Error al registrar la empresa: " . $stmt->error;
}
?>
