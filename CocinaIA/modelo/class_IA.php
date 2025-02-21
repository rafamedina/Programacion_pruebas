<?php
require_once '../config/class_conexion.php';

class IA
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = new Conexion();
    }

    function makeRequest($prompt)
    {
        // Configuración de la solicitud cURL
        $ch = curl_init();

        // Establecer la URL de destino
        curl_setopt($ch, CURLOPT_URL, "http://localhost:1234/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        // El mensaje del sistema debe ir en la clave "system" y los mensajes en la clave "messages"
        $data = [
            "model" => "llama-3.2-3b-instruct",
            "system" => "Actúa como un asistente de cocina experto y responde siempre en español/castellano.

Cuando te pregunten sobre una receta, proporciona la información completa estructurada de la siguiente manera:

{
  \"nombre\": \"[Nombre completo de la receta]\",
  \"descripcion\": \"[Breve descripción de la receta, su origen e historia]\",
  \"preparación\": \"[Instrucciones paso a paso para preparar la receta]\",
  \"categoria\": \"[Categoría de la receta: Entrante, Plato principal, Postre, etc.]\",
  \"ingredientes\": [\"Ingrediente 1\", \"Ingrediente 2\", \"Ingrediente 3\", ...],
  \"imagen\": \"[Descripción de cómo se vería el plato terminado]\"
}

Asegúrate de que todos los campos estén completos y sean detallados. Mantén siempre este formato para todas tus respuestas sobre recetas.

Si la consulta no es sobre una receta, responde de manera natural en español, pero manteniendo un estilo informativo y profesional.",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ]
        ];

        $json_data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        // Establecer el tipo de contenido a enviar (JSON)
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data)
        ]);

        // Ejecutar la solicitud y almacenar el resultado
        $resultado = curl_exec($ch);

        // Verificar si hubo un error en la solicitud
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }

        // Cerrar la sesión cURL
        curl_close($ch);

        return $resultado;
    }
    /*
// Obtener el prompt del usuario
echo "Ingresa tu prompt: ";
$prompt = trim(fgets(STDIN));

if ($prompt) {
    // Realizar la solicitud HTTP con el prompt proporcionado
    $respuesta = makeRequest(, $prompt);

    if ($respuesta !== null) {
        // Decodificar la respuesta JSON
        $dataResponse = json_decode($respuesta, true);

        // Verificar si la respuesta tiene el formato esperado
        if (isset($dataResponse['choices'][0]['message']['content'])) {
            echo "Respuesta recibida:\n";
            echo $dataResponse['choices'][0]['message']['content'] . "\n";
        } else {
            echo "No se recibió una respuesta válida.\n";
        }
    } else {
        echo "No se recibió respuesta.\n";
    }
} else {
    echo "Prompt vacío. Inténtalo de nuevo.\n";
}


*/
}
