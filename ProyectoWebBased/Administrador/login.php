<?php
session_start();
include "../conexion.php";

if (isset($_POST["login"])) {
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT id, Nombre, Contraseña, rol, bloqueado, intentos_fallidos FROM datos WHERE Nombre = ?");
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);
        $id_usuario = $usuario['id'];

        if ($usuario['bloqueado']) {
            // Registrar intento de acceso bloqueado
            $accion = "Intento de acceso";
            $descripcion = "Intento de acceso fallido: usuario bloqueado.";
            $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (?, ?, ?)");
            $log->bind_param("iss", $id_usuario, $accion, $descripcion);
            $log->execute();

            echo '<script>alert("Usuario bloqueado. Contacte al administrador."); window.location.href="../InicioP.html";</script>';
            exit();
        }

        if ($password === $usuario['Contraseña']) {
            $reset = mysqli_prepare($conn, "UPDATE datos SET intentos_fallidos = 0 WHERE id = ?");
            mysqli_stmt_bind_param($reset, "i", $id_usuario);
            mysqli_stmt_execute($reset);

            $_SESSION['user_id'] = $id_usuario;
            $_SESSION['user_name'] = $usuario['Nombre'];
            $_SESSION['user_rol'] = $usuario['rol'];

            // Registrar inicio de sesión exitoso
            $accion = "Inicio de sesión";
            $descripcion = "El usuario inició sesión correctamente.";
            $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (?, ?, ?)");
            $log->bind_param("iss", $id_usuario, $accion, $descripcion);
            $log->execute();

            if ($usuario['rol'] === 'admin') {
                header("Location: admin_panel.php");
                exit();
            }

            $check = mysqli_prepare($conn, "SELECT id FROM empresa WHERE id_usuario = ?");
            mysqli_stmt_bind_param($check, "i", $id_usuario);
            mysqli_stmt_execute($check);
            $res = mysqli_stmt_get_result($check);

            if (mysqli_num_rows($res) > 0) {
                header("Location: ../index.html");
            } else {
                header("Location: ../Registroempresa/registroEmpresa.html");
            }
            exit();
        } else {
            $intentos = $usuario['intentos_fallidos'] + 1;
            $bloquear = $intentos >= 3;

            $query = "UPDATE datos SET intentos_fallidos = ?" . ($bloquear ? ", bloqueado = TRUE, fecha_bloqueo = NOW()" : "") . " WHERE id = ?";
            $update = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($update, "ii", $intentos, $id_usuario);
            mysqli_stmt_execute($update);

            // Registrar intento fallido
            $accion = $bloquear ? "Bloqueo de usuario" : "Intento fallido";
            $descripcion = $bloquear 
                ? "El usuario fue bloqueado tras 3 intentos fallidos." 
                : "Contraseña incorrecta. Intento $intentos de 3.";
            $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (?, ?, ?)");
            $log->bind_param("iss", $id_usuario, $accion, $descripcion);
            $log->execute();

            if ($bloquear) {
                echo '<script>alert("Usuario bloqueado por 3 intentos fallidos."); window.location.href="../InicioP.html";</script>';
            } else {
                echo '<script>alert("Contraseña incorrecta. Intentos restantes: ' . (3 - $intentos) . '"); window.location.href="../InicioP.html";</script>';
            }
            exit();
        }
    } else {
        // Registrar intento con usuario inexistente
        $accion = "Intento de acceso";
        $descripcion = "Intento de inicio de sesión con usuario no registrado: $name";
        $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (NULL, ?, ?)");
        $log->bind_param("ss", $accion, $descripcion);
        $log->execute();

        echo '<script>alert("Usuario no encontrado"); window.location.href="../InicioP.html";</script>';
        exit();
    }
}
?>

