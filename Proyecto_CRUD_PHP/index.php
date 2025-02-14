<?php
session_start(); // Iniciar sesión para acceder a $_SESSION

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Menú Principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Menú Principal</h1>

        <?php
        // Si no hay usuario en sesión, mostrar botones de inicio y registro
        if (!isset($_SESSION['id_usuario'])) {
            echo '<a href="vista/UsuarioInicioSesion.php" class="btn btn-primary">Iniciar Sesión</a> ';
            echo '</br>';
            echo '<a href="vista/UsuarioRegistro.php" class="btn btn-secondary">Registrarse</a>';
        }
        // Si el usuario tiene rol de admin, mostrar opciones de administración
        elseif ($_SESSION['rol'] === 'admin') {
            echo '<a href="vista/lista_socios.php" class="btn btn-primary">CRUD de Socios</a> ';
            echo '</br>';
            echo '<a href="vista/lista_eventos.php" class="btn btn-primary">CRUD de Eventos</a>';
            echo '</br>';
            echo '<a href="vista/lista_eventos.php" class="btn btn-primary">CRUD de Usuarios</a>';
            echo '</br>';
            echo '<a href="vista/cerrar_sesion.php" class="btn btn-primary">Cerrar Sesion</a>';
        }
        // Si el usuario es normal, mostrar opciones de usuario
        else {
            echo '<a href="vista/lista_socios.php" class="btn btn-primary">CRUD de Socios</a> ';
            echo '</br>';
            echo '<a href="vista/lista_eventos.php" class="btn btn-primary">CRUD de Eventos</a>';
            echo '</br>';
            echo '<a href="vista/cerrar_sesion.php" class="btn btn-primary">Cerrar Sesion</a>';
        }
        ?>
    </div>
</body>

</html>