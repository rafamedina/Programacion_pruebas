<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Verifica si el usuario tiene el rol adecuado
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once '../controlador/UsuarioController.php';

$Controller = new UsuarioController();

if (isset($_GET['id'])) {
    $idusuario = $_GET['id'];
    $Usuario = $Controller->obtenerUsuarioPorId($idusuario);


    if (!$Usuario) {
        echo "usuario no encontrado.";
        exit();
    }
} else {
    header("Location: UsuarioLista.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    $Controller->actualizarUsuario($Usuario["id_usuario"], $nombre, $email, $rol);
    header("Location: UsuarioLista.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Editar Usuario</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($Usuario['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($Usuario['correo']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select class="form-control" id="rol" name="rol" required>
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        </form>
        <a href="UsuarioLista.php" class="btn btn-secondary mt-3">Volver a la lista de Usuarios</a>
    </div>