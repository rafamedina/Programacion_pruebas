<?php
require_once '../modelo/class_IA.php';

class IAController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new IA();
    }


    public function PedirIngredientes($prompt)
    {
        return $this->modelo->PedirIngredientes($prompt);
    }
    public function PedirDesarrollo($prompt)
    {
        return $this->modelo->PedirDesarrollo($prompt);
    }
    public function PedirResumen($prompt)
    {
        return $this->modelo->PedirResumen($prompt);
    }
}
