<?php
session_start(); // Iniciar sesión para gestionar la autenticación

require_once '../controlador/UsuarioController.php'; // Incluir el controlador
$controller = new UsuarioController(); // Instanciar el controlador
$error_message = null; // Variable para manejar errores

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo']; // Obtener correo ingresado
    $contraseña = $_POST['contraseña']; // Obtener contraseña ingresada

    // Intentar iniciar sesión
    $usuario = $controller->iniciarSesion($correo, $contraseña);

    if (!$usuario) {
        // Si las credenciales son incorrectas, mostrar un mensaje de error
        $error_message = "Datos equivocados, prueba de nuevo.";
    } else {
        // Guardar la información del usuario en la sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['rol'] = $usuario['rol']; // Guardar rol en la sesión

        // Redirigir a la página correspondiente según el rol
        if ($usuario['rol'] === 'admin') {
            header("location: ../index.php"); // Página de administrador
        } else {
            header("location: ../index.php"); // Página de usuario normal
        }
        exit(); // Asegurar que el script se detiene después de redirigir
    }
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="mt-4">INICIAR SESION</h1>

        <?php if (isset($error_message)): ?>
            <p style="color:red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <p style="color:green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="" class="mt-4">
            <div class="form-group">
                <label for="correo">Email:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="contraseña">Contraseña:</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary">Iniciar Sesion</button>

        </form>
    </div>
</body>

</html>