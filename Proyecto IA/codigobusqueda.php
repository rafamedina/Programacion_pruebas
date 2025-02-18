<?php

function makeRequest($url, $prompt)
{
    // Configuración de la solicitud cURL
    $ch = curl_init();

    // Establecer la URL de destino
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    // Añadir los datos del prompt a la solicitud en formato JSON
    $data = [
        "messages" => [["role" => "user", "content" => $prompt]]
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

// Obtener el prompt del usuario
echo "Ingresa tu prompt: ";
$prompt = trim(fgets(STDIN));

if ($prompt) {
    // Realizar la solicitud HTTP con el prompt proporcionado
    $respuesta = makeRequest("http://localhost:1234/v1/chat/completions", $prompt);

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
