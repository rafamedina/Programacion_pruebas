<?php
require_once '../config/class_conexion.php';

class Usuario
{
    private $conexion;

    // Constructor que inicializa la conexión a la base de datos.
    public function __construct()
    {
        $this->conexion = new Conexion();
    }
    public function ComprobarCorreo($correo)
    {
        $query = "SELECT id_usuario FROM usuarios WHERE correo = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $id_usuario = null;
        if ($fila = $resultado->fetch_assoc()) {
            $id_usuario = $fila['id_usuario'];
        }

        $stmt->close();
        return $id_usuario; // Devuelve el ID si existe, o null si no existe
    }

    // Nueva función para contar usuarios
    public function contarUsuarios()
    {
        $query = "SELECT COUNT(*) AS total FROM usuarios";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $stmt->close();

        return $fila['total']; // Devuelve el número total de usuarios en la base de datos
    }

    public function agregarUsuario($nombre, $correo, $contraseña, $rol)
    {
        $id_usuario = $this->ComprobarCorreo($correo);
        if ($id_usuario !== null) {
            return false; // El correo ya está registrado
        }

        // Cifrar la contraseña
        $contraseñaHashed = password_hash($contraseña, PASSWORD_DEFAULT);

        // Si no hay usuarios, el primero será admin
        if ($rol == null) {
            $rol = ($this->contarUsuarios() == 0) ? 'admin' : 'user';
        }


        // Insertar el usuario con el rol determinado
        $query = "INSERT INTO Usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("ssss", $nombre, $correo, $contraseñaHashed, $rol);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Error al agregar usuario: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    // Obtiene un usuario específico por su ID.
    public function obtenerUsuarioporid($id_usuario)
    {
        $query = "SELECT * FROM Usuarios WHERE id_usuario = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();

        // Devuelve el usuario encontrado o null si no existe.
        return $resultado->fetch_assoc();
    }


    public function ComprobarRol($correo)
    {
        $query = "SELECT rol FROM usuarios WHERE correo = ?";

        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
        if ($resultado == "user") {
            return True;
        } else {
            return false;
        }
    }


    // Inicia sesión verificando las credenciales del administrador.
    public function iniciarSesion($correo, $password)
    {
        $query = "SELECT * FROM Usuarios WHERE correo = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        // Verificar si el usuario existe
        if (!$usuario) {
            return false; // Correo no encontrado
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['password'])) {
            return false; // Contraseña incorrecta
        }

        // Iniciar sesión
        session_start();
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['rol'] = $usuario['rol']; // Guardamos el rol en la sesión

        return $usuario; // Devuelve los datos del usuario si la autenticación es correcta
    }
    public function ListarUsuarios()
    {
        $query = "SELECT * FROM Usuarios";
        $resultado = $this->conexion->conexion->query($query);
        $socios = [];
        while ($fila = $resultado->fetch_assoc()) {
            $socios[] = $fila;
        }
        return $socios;
    }

    public function actualizarUsuario($idusuario, $nombre, $email, $rol)
    {
        $query = "UPDATE Usuarios SET nombre = ?, correo = ?, rol = ? WHERE id_usuario = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("sssi", $nombre, $email, $rol, $idusuario);

        if ($stmt->execute()) {
            echo "Usuario actualizado con éxito.";
        } else {
            echo "Error al actualizar Usuario: " . $stmt->error;
        }

        $stmt->close();
    }
    public function eliminarUsuario($id_Usuario)
    {
        $query = "DELETE FROM Usuarios WHERE id_usuario = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("i", $id_Usuario);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Error al eliminar Usuario: " . $stmt->error);
            return false;
        }

        $stmt->close();
    }
}
