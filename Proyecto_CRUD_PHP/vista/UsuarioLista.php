<?php
session_start(); // Iniciar sesión

// Verifico si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    session_destroy(); // Cierro la sesión por seguridad
    header("Location: ../index.php");  // Redirijo al login si no está logueado
    exit();
}
require_once '../controlador/UsuarioController.php';
$controller = new UsuarioController();
$usuarios = $controller->listarUsuarios();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Usuarios Registrados</h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario['id_usuario'] ?></td>
                        <td><?= $usuario['nombre'] ?></td>
                        <td><?= $usuario['correo'] ?></td>
                        <td><?= $usuario['rol'] ?></td>

                        <td>
                            <a href="Usuarioseditar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-warning">Editar</a>

                            <a href="Usuarioeliminar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-danger">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="UsuarioAlta.php" class="btn btn-primary">Agregar un nuevo Usuario</a>
        <a href="../index.php" class="btn btn-primary">volver</a>
    </div>
</body>

</html>