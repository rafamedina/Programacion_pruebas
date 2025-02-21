<?php
require_once '../modelo/class_IA.php';

class IAController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new IA();
    }

    public function ComprobarReceta($nombre)
    {
        return $this->modelo->ComprobarReceta($nombre);
    }

    function makeRequest($prompt)
    {

        return   $this->modelo->makeRequest($prompt);
    }


    public function MostrarReceta($nombre)
    {
        return $this->modelo->MostrarReceta($nombre);
    }
}
