<?php
require_once __DIR__ . '/../config/class_conexion.php';

class Rol
{
    private $id;
    private $nombre;
    private $correo;
    private $rol;

    public function __construct($id, $nombre, $correo, $rol)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->rol = $rol;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function getCorreo()
    {
        return $this->correo;
    }
    public function getRol()
    {
        return $this->rol;
    }
}

class Auth
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function iniciarSesion($correo, $contraseña)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $query = "SELECT * FROM Usuarios WHERE correo = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuarioData = $resultado->fetch_assoc();
        $stmt->close();

        if ($usuarioData && password_verify($contraseña, $usuarioData['contraseña'])) {
            $_SESSION['usuario'] = [
                'id' => $usuarioData['id_usuario'],
                'nombre' => $usuarioData['nombre'],
                'correo' => $usuarioData['correo'],
                'rol' => $usuarioData['rol']
            ];
            return true;
        }
        return false;
    }

    public function cerrarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        exit();
    }

    public function verificarRol($rolRequerido)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === $rolRequerido;
    }
}
