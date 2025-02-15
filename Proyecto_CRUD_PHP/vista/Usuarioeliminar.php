<?php
session_start(); // Iniciar sesión

// Verifico si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    session_destroy(); // Cierro la sesión por seguridad
    header("Location: ../index.php");  // Redirijo al login si no está logueado
    exit();
}
require_once '../controlador/UsuarioController.php';

$UsuarioController = new UsuarioController();

if (isset($_GET['id'])) {
    $id_Usuario = $_GET['id'];
    $Usuario = $UsuarioController->obtenerUsuarioPorId($id_Usuario);


    if (!$Usuario) {
        echo "Usuario no encontrado.";
        exit();
    }
} else {
    header("Location: UsuarioLista.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_Usuario = $_POST['id'];
    $UsuarioController->eliminarUsuario($id_Usuario);
    header("Location: UsuarioLista.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Eliminar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Eliminar Usuario</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="id" class="form-label">ID:</label>
                <input type="text" name="id" class="form-control" value="<?php echo htmlspecialchars($Usuario['id_usuario']); ?>" required>
            </div>
            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar Usuario</button>


        </form>
        <a href="UsuarioLista.php" class="btn btn-secondary mt-3">Volver a la lista de Usuario</a>
    </div>
</body>

</html>