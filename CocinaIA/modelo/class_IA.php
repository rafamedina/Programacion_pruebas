<?php
require_once '../controlador/RecetasController.php';
$controller = new RecetasController();
class IA
{
    private $api_key = "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";
    private $api_url = "https://openrouter.ai/api/v1/chat/completions";
    private $contexto;


    private function realizarSolicitud($mensaje)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $data = [
            "model" => "cognitivecomputations/dolphin3.0-mistral-24b:free",
            "system" => "Habla en español",
            "messages" => [["role" => "user", "content" => $mensaje]],
            "temperature" => 0.1,
            "max_tokens" => -1,
            "stream" => false
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);

        $respuesta = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return "Error de CURL: " . curl_error($ch);
        }

        curl_close($ch);

        $respuesta_json = json_decode($respuesta, true);

        return $respuesta_json['choices'][0]['message']['content'] ?? "Error en la respuesta de la API";
    }

    public function PedirIngredientes($prompt)
    {
        $mensaje = "Proporciona únicamente la lista de ingredientes de la receta: $prompt. Sigue este formato exacto:\n- Ingrediente 1 (cantidad)\n- Ingrediente 2 (cantidad)\n- Ingrediente 3 (cantidad)... No agregues introducciones, encabezados, notas ni explicaciones.";
        $ingredientes = $this->realizarSolicitud($mensaje);
        $this->SetContexto($ingredientes);
        return $ingredientes;
    }

    public function PedirResumen($prompt)
    {
        $mensaje = "Explica brevemente en español de qué trata la receta $prompt. El contexto que tienes es el siguiente: $this->contexto. No des los pasos de preparación ni los ingredientes, solo una descripción general del plato y su contexto de consumo.";
        return $this->realizarSolicitud($mensaje);
    }

    public function PedirDesarrollo($prompt)
    {
        $mensaje = "Genera exclusivamente los pasos de preparación de la receta basándote en los ingredientes listados $this->contexto. 

**Formato obligatorio:**
1. Paso 1...
2. Paso 2...
3. Paso 3...

⚠ **Reglas estrictas:**
- No incluyas ninguna introducción, historia, descripción o información adicional sobre la receta.
- No menciones los ingredientes, solo úsalos implícitamente en los pasos.
- No agregues explicaciones sobre el plato o sugerencias de presentación.
- No uses adjetivos ni comentarios sobre el sabor, textura o tradición del plato.
- Si la entrada no corresponde a una receta válida, responde únicamente: 'No puedo ayudarte con eso, ya que no parece ser una receta válida.'

**Ejemplo de salida esperada:**
1. Pela y corta las patatas en rodajas finas.
2. Fríe las patatas en aceite caliente hasta que estén doradas.
3. Retira las patatas y fríe los huevos en el mismo aceite.
4. Coloca las patatas en un plato, añade los huevos fritos encima y rómpelos ligeramente con un tenedor.
";

        return $this->realizarSolicitud($mensaje);
        $this->EliminarContexto();
    }



    private function SetContexto($contexto)
    {
        $this->contexto = $contexto;
    }

    private function EliminarContexto()
    {
        $this->contexto = null;
    }
}
