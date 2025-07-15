<?php
session_start();
include "../conexion.php";

$mensaje = "";

if (isset($_POST['verificar_usuario'])) {
    $usuario = trim($_POST['usuario']);
    
    // Verificar si el usuario existe
    $stmt = mysqli_prepare($conn, "SELECT id, Nombre FROM datos WHERE Nombre = ?");
    mysqli_stmt_bind_param($stmt, "s", $usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($resultado) > 0) {
        $datos_usuario = mysqli_fetch_assoc($resultado);
        $_SESSION['usuario_reset'] = $datos_usuario['id'];
        // Mostrar formulario de cambio de contraseña
        $mostrar_formulario = true;
    } else {
        $mensaje = "<div class='error'>Usuario no encontrado</div>";
    }
}

if (isset($_POST['cambiar_contrasena'])) {
    $nueva_contrasena = trim($_POST['nueva_contrasena']);
    $confirmar_contrasena = trim($_POST['confirmar_contrasena']);
    
    if ($nueva_contrasena !== $confirmar_contrasena) {
        $mensaje = "<div class='error'>Las contraseñas no coinciden</div>";
        $mostrar_formulario = true;
    } elseif (isset($_SESSION['usuario_reset'])) {
        $usuario_id = $_SESSION['usuario_reset'];

        // Actualizar contraseña directamente
        $stmt = mysqli_prepare($conn, "UPDATE datos SET Contraseña = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nueva_contrasena, $usuario_id);
        mysqli_stmt_execute($stmt);

        // Registrar actividad
        $accion = "Cambio de contraseña";
        $descripcion = "El usuario cambió su contraseña con éxito.";
        $log = $conn->prepare("INSERT INTO actividad_log (id_usuario, tipo_accion, descripcion) VALUES (?, ?, ?)");
        $log->bind_param("iss", $usuario_id, $accion, $descripcion);
        $log->execute();

        $mensaje = "<div class='exito'>Contraseña cambiada exitosamente</div>";
        unset($_SESSION['usuario_reset']);
        echo "<script>setTimeout(function(){ window.location.href = '../InicioP.html'; }, 2000);</script>";
    }
}
?>


<<!DOCTYPE html>
<html>
<head>
    <title>Recuperar Contraseña</title>
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Layout general */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        /* Contenedor principal dividido */
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            flex-direction: column;
            background-color: #ffffff;
        }

        .container h2 {
            margin-bottom: 1.5rem;
            color: #333;
            font-size: 1.8rem;
            text-align: center;
        }

        .container form {
            width: 100%;
            max-width: 350px;
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 1rem;
            width: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.9rem;
        }

        .container input[type="text"],
        .container input[type="password"] {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9rem;
            width: 100%;
        }

        .container button {
            padding: 0.75rem;
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
            font-weight: bold;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            width: 100%;
        }

        .container button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #dc3545;
            margin: 1rem 0;
            padding: 0.75rem;
            background: #f8d7da;
            border-radius: 5px;
            font-size: 0.9rem;
            text-align: center;
        }

        .exito {
            color: #28a745;
            margin: 1rem 0;
            padding: 0.75rem;
            background: #d4edda;
            border-radius: 5px;
            font-size: 0.9rem;
            text-align: center;
        }

        .container a {
            display: block;
            margin-top: 1.5rem;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .container a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }
            
            .container h2 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        <?php echo $mensaje; ?>
        
        <?php if (!isset($mostrar_formulario)): ?>
        <!-- Formulario para verificar usuario -->
        <form method="POST">
            <div class="form-group">
                <label>Nombre de usuario:</label>
                <input type="text" name="usuario" required>
            </div>
            <button type="submit" name="verificar_usuario">Verificar Usuario</button>
        </form>
        <?php else: ?>
        <!-- Formulario para cambiar contraseña -->
        <form method="POST">
            <div class="form-group">
                <label>Nueva Contraseña:</label>
                <input type="password" name="nueva_contrasena" required>
            </div>
            <div class="form-group">
                <label>Confirmar Contraseña:</label>
                <input type="password" name="confirmar_contrasena" required>
            </div>
            <button type="submit" name="cambiar_contrasena">Cambiar Contraseña</button>
        </form>
        <?php endif; ?>
        
        <a href="../InicioP.html">Volver al login</a>
    </div>
</body>
</html>