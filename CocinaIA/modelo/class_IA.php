<?php

class IA
{
    private $contexto;

    public function actualizarContexto($respuesta)
    {
        $this->contexto = $respuesta;
    }

    public function SetContexto($respuesta)
    {
        $this->contexto = $respuesta;
    }
    public function EliminarContexto()
    {
        $this->contexto = " ";
    }


    public function PedirIngredientes($prompt)
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
            "system" => "Habla en español",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Proporciona únicamente la lista de ingredientes de la receta: $prompt.
Sigue este formato exacto:

Ingrediente 1 (cantidad)
Ingrediente 2 (cantidad)
Ingrediente 3 (cantidad)
(y así sucesivamente).
La respuesta debe seguir exactamente este formato sin variaciones en cada consulta, sin importar cuántas veces se repita la pregunta.
No agregues explicaciones, encabezados, notas, introducciones ni conclusiones.
Solo devuelve la lista estrictamente en el formato especificado.
No uses caracteres en negrita, emoticonos ni ninguna otra variación de formato.
No olvides incluir los guiones al inicio de cada línea."
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
        $respuesta = curl_exec($ch);

        // Verificar si hubo un error en la solicitud
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }

        // Cerrar la sesión cURL
        curl_close($ch);
        $this->SetContexto($respuesta);
        return $respuesta;
    }



    public function PedirResumen($prompt)
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
            "system" => "habla en español",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Explica brevemente en español de qué trata la receta " . $prompt . ", el conexto que tienes sobre la receta es el siguient" . $this->contexto . ";. No des los pasos de preparación ni los ingredientes, solo una descripción general de qué tipo de plato es, sus características principales y en qué contexto suele consumirse. Si " . $prompt . " no es una receta de cocina, responde con: 'No puedo ayudarte con eso, ya que no parece ser una receta válida'."
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
        $respuesta = curl_exec($ch);

        // Verificar si hubo un error en la solicitud
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }

        // Cerrar la sesión cURL
        curl_close($ch);
        return $respuesta;
    }

    public function PedirDesarrollo($prompt)
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
            "system" => "Habla en español",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Con base en la información que tienes sobre " . $this->contexto . " y los ingredientes listados, proporciona **únicamente** los pasos exactos para su preparación en este formato estricto:

1. Paso 1...
2. Paso 2...
3. Paso 3...
...

No agregues introducciones, explicaciones, resúmenes ni conclusiones. No incluyas información adicional sobre el origen o características del platillo. Solo devuelve la lista de pasos en el formato indicado.  
No uses negritas, emoticonos ni otro formato adicional.  

Si " . $prompt . " no es una receta válida o no tienes información suficiente, responde con: ''No puedo ayudarte con eso, ya que no parece ser una receta válida.'
"
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
        $respuesta = curl_exec($ch);

        // Verificar si hubo un error en la solicitud
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }

        // Cerrar la sesión cURL
        curl_close($ch);
        $this->actualizarContexto($respuesta);
        return $respuesta;
    }


    public function PedirImagen($prompt)
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
            "system" => "Habla en español",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Con base en la información que tienes sobre " . $this->contexto . " y los ingredientes listados, proporciona **únicamente** los pasos exactos para su preparación en este formato estricto:

1. Paso 1...
2. Paso 2...
3. Paso 3...
...

No agregues introducciones, explicaciones, resúmenes ni conclusiones. No incluyas información adicional sobre el origen o características del platillo. Solo devuelve la lista de pasos en el formato indicado.  
No uses negritas, emoticonos ni otro formato adicional.  

Si " . $prompt . " no es una receta válida o no tienes información suficiente, responde con: ''No puedo ayudarte con eso, ya que no parece ser una receta válida.'"

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
        $respuesta = curl_exec($ch);

        // Verificar si hubo un error en la solicitud
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
            curl_close($ch);
            return null;
        }

        // Cerrar la sesión cURL
        curl_close($ch);
        $this->EliminarContexto();
        return $respuesta;
    }
}
