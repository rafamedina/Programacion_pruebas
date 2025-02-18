<?php

function makeRequest($url, $prompt)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $data = [
        "model" => "qwen/qwen-vl-plus:free",
        "messages" => [
            ["role" => "user", "content" => $prompt]
        ]
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
