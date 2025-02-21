<?php
require_once '../config/class_conexion.php';

class IA
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    public function ComprobarReceta($nombre)
    {
        $query = "SELECT * FROM LibroRecetas WHERE nombre = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if (!$resultado) {
            return false;
        } else {
            return true;
        }
    }


    public function MostrarReceta($nombre)
    {
        $query = "SELECT * FROM LibroRecetas WHERE nombre = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $receta = $resultado->fetch_assoc();
        return $receta;
    }

    function makeRequest($prompt)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://openrouter.ai/api/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = [
            "model" => "cognitivecomputations/dolphin3.0-r1-mistral-24b:free",
            "messages" =>
            array(
                array("role" => "system", "content" => "Responde siempre en español, el contente tiene que dividirse en Primero una descripcion de la receta, luego los ingredientes y por ultimo la elaboracion. el json tiene que tener la capacidad y estructura de esta tabla para poder ser procesado: Create Table LibroRecetas(
    id_receta int primary key AUTO_INCREMENT,
    nombre VARCHAR(255),
    descripcion text,
    preparacion text,
    categoria VARCHAR(255),
    ingredientes text,
    imagen VARCHAR(255)
    );"),
                array("role" => "user", "content" => $prompt)
            ),
            "temperature" => 0.7,
            "max_tokens" => -1,
            "stream" => false
        ];

        $json_data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        // 🔹 Inserta tu API Key real aquí
        $api_key = "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";

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $api_key, // ✅ Correcto
            'Content-Type: application/json'
        ]);

        $resultado = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Error en cURL: " . curl_error($ch) . "\n";
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $resultado;
    }
}
