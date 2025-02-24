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
        return $this->GuardarReceta($nombre, $descripcion, $preparacion, $ingredientes);
    }
}
