<?php
session_start();

// Verifica si hay sesión y es administrador
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.'); window.location.href='../InicioP.html';</script>";
    exit();
}
