<?php

function makeRequest($url, $prompt)
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

echo "Ingresa tu prompt: ";
$prompt = trim(fgets(STDIN));

if ($prompt) {
    $respuesta = makeRequest("https://openrouter.ai/api/v1/chat/completions", $prompt);

    if ($respuesta !== null) {
        $dataResponse = json_decode($respuesta, true);

        // ⚠️ Comprobar si hay un error en la API
        if (isset($dataResponse['error'])) {
            echo "\nError en la API: " . $dataResponse['error']['message'] . "\n";
        } elseif (isset($dataResponse['choices'][0]['message']['content'])) {
            echo "\nRespuesta recibida:\n";
            echo $dataResponse['choices'][0]['message']['content'] . "\n";
        } else {
            echo "\nNo se recibió una respuesta válida.\n";
        }
    } else {
        echo "\nNo se recibió respuesta.\n";
    }
} else {
    echo "\nPrompt vacío. Inténtalo de nuevo.\n";
}
