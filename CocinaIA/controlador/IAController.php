<?php
require_once '../modelo/class_IA.php';

class IAController
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = new IA();
    }
    function makeRequest($prompt)
    {
        return $this->modelo->makeRequest($prompt);
    }
}
