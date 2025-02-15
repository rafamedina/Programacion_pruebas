<?php

require_once '../controlador/UsuarioController.php';
$controller2 = new UsuarioController();
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $contraseña = $_POST['password'];


    $usuario = $controller2->agregarUsuario($nombre, $email, $contraseña, $rol);
    if (!$usuario) {
        $error_message = "Error al agregar usuario. Por favor, verifica los datos.";
    } else {
        $success_message = "usuario agregado con éxito.";
        header("Location: UsuariosLista.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<head>
    <meta charset="UTF-8">
    <title>Agregar Usuarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<body>
    <div class="container">
        <h1 class="mt-4">Agregar Usuarios</h1>

        <?php if (isset($error_message)): ?>
            <p style="color:red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <p style="color:green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="" class="mt-4">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="rol">Rol:</label>
                <select class="form-control" id="rol" name="rol" required>
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>


            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Agregar Usuario</button>
        </form>
        <a href="UsuarioLista.php" class="btn btn-primary">volver</a>
    </div>
</body>

</html>