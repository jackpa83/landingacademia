<?php
include "../conexion.php";

if (isset($_POST["register"])) {
    $errors = [];

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $rol = 'usuario';

    // Validaciones
    if (empty($name)) {
        $errors[] = "El nombre es requerido";
    } elseif (!preg_match('/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/', $name)) {
        $errors[] = "El nombre no puede contener números ni caracteres especiales";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido";
    }

    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden";
    }

    // Verificar duplicados
    $stmt1 = mysqli_prepare($conn, "SELECT id FROM datos WHERE Nombre = ?");
    mysqli_stmt_bind_param($stmt1, "s", $name);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_store_result($stmt1);
    if (mysqli_stmt_num_rows($stmt1) > 0) {
        $errors[] = "El nombre de usuario ya está registrado";
    }

    $stmt2 = mysqli_prepare($conn, "SELECT id FROM datos WHERE Email = ?");
    mysqli_stmt_bind_param($stmt2, "s", $email);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_store_result($stmt2);
    if (mysqli_stmt_num_rows($stmt2) > 0) {
        $errors[] = "El correo electrónico ya está registrado";
    }

    if (empty($errors)) {
        $fecha = date('Y-m-d H:i:s');

        // ❌ Guardar la contraseña tal como fue escrita (sin hash)
        $stmt3 = mysqli_prepare($conn, "INSERT INTO datos (Nombre, Email, Contraseña, rol, fecha) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt3, "sssss", $name, $email, $password, $rol, $fecha);

        if (mysqli_stmt_execute($stmt3)) {
            echo '<script>
                alert("¡Registro exitoso! Ahora puedes iniciar sesión.");
                window.location.href = "../InicioP.html";
            </script>';
            exit();
        } else {
            echo '<script>alert("Error al registrar: ' . mysqli_error($conn) . '");</script>';
            exit();
        }
    } else {
        echo '<script>alert("' . implode("\\n", $errors) . '"); window.history.back();</script>';
        exit();
    }
}
?>
