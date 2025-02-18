<?php
session_start(); // Archivo que establece la conexión a la base de datos
require_once 'modelo/class_autentificacion.php';
require_once 'config/class_conexion.php';

$auth = new Auth($conexion);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Menú Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <?php if (!isset($_SESSION['usuario'])): ?>
            <h1 class="mt-4">Bienvenido</h1>
            <a href="vista/UsuarioInicioSesion.php" class="btn btn-primary">Iniciar Sesión</a>
            <br>
            <a href="vista/UsuarioRegistro.php" class="btn btn-secondary">Registrarse</a>
        <?php elseif ($auth->verificarRol('admin')): ?>
            <h1 class="mt-4">Vista de Admin</h1>
            <a href="vista/lista_socios.php" class="btn btn-primary">CRUD de Socios</a>
            <br>
            <a href="vista/lista_eventos.php" class="btn btn-primary">CRUD de Eventos</a>
            <br>
            <a href="vista/UsuarioLista.php" class="btn btn-primary">CRUD de Usuarios</a>
            <br>
            <a href="vista/cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a>
        <?php else: ?>
            <h1 class="mt-4">Vista de Usuario</h1>
            <a href="vista/lista_socios.php" class="btn btn-primary">Ver Socios</a>
            <br>
            <a href="vista/lista_eventos.php" class="btn btn-primary">Ver Eventos</a>
            <br>
            <a href="vista/cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a>
        <?php endif; ?>
    </div>
</body>

</html>





