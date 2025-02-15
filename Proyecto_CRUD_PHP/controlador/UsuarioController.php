<?php
require_once '../modelo/class_usuario.php';

class UsuarioController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Usuario();
    }

    public function agregarUsuario($nombre, $correo, $contraseña, $rol)
    {
        return $this->modelo->agregarUsuario($nombre, $correo, $contraseña, $rol);
    }


    public function obtenerUsuarioporid($id_usuario)
    {
        return $this->modelo->obtenerUsuarioporid($id_usuario);
    }

    public function iniciarSesion($correo, $contraseña)
    {
        return $this->modelo->iniciarSesion($correo, $contraseña);
    }
    public function ListarUsuarios()
    {
        return $this->modelo->ListarUsuarios();
    }
}
