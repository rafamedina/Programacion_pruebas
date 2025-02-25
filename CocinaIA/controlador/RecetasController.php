<?php
require_once '../modelo/Class_receta.php';

class RecetasController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new Receta();
    }
    public function GuardarReceta($nombre, $descripcion, $preparacion, $ingredientes)
    {
        return $this->modelo->GuardarReceta($nombre, $descripcion, $preparacion, $ingredientes);
    }
    public function obtenerRecetaPorNombre($nombre)
    {
        return $this->modelo->obtenerRecetaPorNombre($nombre);
    }
}
