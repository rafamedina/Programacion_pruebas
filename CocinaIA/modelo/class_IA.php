<?php
require_once '../config/class_conexion.php';

class IA
{
    // Clave de API para autenticar solicitudes a OpenRouter AI
    private $api_key = "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";

    // URL de la API a la que se enviarán las solicitudes
    private $api_url = "https://openrouter.ai/api/v1/chat/completions";

    // Variable para almacenar el contexto de una conversación o datos temporales
    private $contexto;

    // Variable para manejar la conexión a la base de datos
    private $conexion;

    public function __construct()
    {
        // Inicializo la conexión a la base de datos al crear una instancia de la clase
        $this->conexion = new Conexion();
    }

    // Método para guardar una receta en la base de datos
    public function GuardarReceta($nombre, $descripcion, $preparacion, $ingredientes)
    {
        // Verifico si la receta ya existe en la base de datos
        $existe = $this->obtenerRecetaPorNombre($nombre);
        if (!$existe) {
            // Si no existe, inserto la nueva receta
            $query = "INSERT INTO librorecetas (nombre, descripcion, preparacion, ingredientes) VALUES (?,?,?,?)";
            $stmt = $this->conexion->conexion->prepare($query);
            $stmt->bind_param("ssss", $nombre, $descripcion, $preparacion, $ingredientes);

            if ($stmt->execute()) {
                return true;
            } else {
                error_log("Error al agregar Receta: " . $stmt->error);
                return false;
            }
        } else {
            // Si la receta ya existe, actualizo sus datos
            $query = "UPDATE librorecetas SET descripcion = ?, preparacion = ?, ingredientes = ? WHERE nombre = ?";
            $stmt = $this->conexion->conexion->prepare($query);
            $stmt->bind_param("ssss", $descripcion, $preparacion, $ingredientes, $nombre);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Método para obtener una receta de la base de datos por su nombre
    public function obtenerRecetaPorNombre($nombre)
    {
        $query = "SELECT * FROM librorecetas WHERE nombre = ?";
        $stmt = $this->conexion->conexion->prepare($query);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();

        // Retorno los resultados como un array asociativo
        $planes = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $planes;
    }

    // Método privado para realizar una solicitud HTTP a la API de OpenRouter AI
    private function realizarSolicitud($mensaje)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        // Defino los parámetros de la solicitud
        $data = [
            "model" => "cognitivecomputations/dolphin3.0-mistral-24b:free",
            "system" => "Habla en español",
            "messages" => [["role" => "user", "content" => $mensaje]],
            "temperature" => 0.1, // Configuración para evitar respuestas aleatorias
            "max_tokens" => -1,
            "stream" => false
        ];

        // Envío los datos como JSON en la solicitud
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);

        // Ejecuto la solicitud
        $respuesta = curl_exec($ch);

        // Verifico si hubo errores en la solicitud CURL
        if (curl_errno($ch)) {
            curl_close($ch);
            return "Error de CURL: " . curl_error($ch);
        }

        curl_close($ch);

        // Decodifico la respuesta de JSON a un array asociativo
        $respuesta_json = json_decode($respuesta, true);

        // Retorno el contenido de la respuesta, si existe
        return $respuesta_json['choices'][0]['message']['content'] ?? "Error en la respuesta de la API";
    }

    // Método para obtener solo los ingredientes de una receta usando la API
    public function PedirIngredientes($prompt)
    {
        $mensaje = "Proporciona únicamente la lista de ingredientes de la receta: $prompt. Sigue este formato exacto:\n- Ingrediente 1 (cantidad)\n- Ingrediente 2 (cantidad)\n- Ingrediente 3 (cantidad)... No agregues introducciones, encabezados, notas ni explicaciones.";
        $ingredientes = $this->realizarSolicitud($mensaje);
        $this->SetContexto($ingredientes);
        return $ingredientes;
    }

    // Método para obtener un resumen de una receta sin detalles de preparación ni ingredientes
    public function PedirResumen($prompt)
    {
        $mensaje = "Explica brevemente en español de qué trata la receta $prompt. El contexto que tienes es el siguiente: $this->contexto. No des los pasos de preparación ni los ingredientes, solo una descripción general del plato y su contexto de consumo.";
        return $this->realizarSolicitud($mensaje);
    }

    // Método para generar un nombre adecuado para una receta
    public function obtenerNombre($prompt)
    {
        $mensaje = "Genera un nombre adecuado y bien estructurado en español para la siguiente receta: '$prompt'. El nombre debe ser claro, atractivo y representativo del plato. No incluyas descripciones, ingredientes ni pasos de preparación, SOLO proporciona el nombre del plato.";
        return $this->realizarSolicitud($mensaje);
    }

    // Método para generar los pasos de preparación de una receta
    public function PedirDesarrollo()
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
        $this->EliminarContexto();
        return $this->realizarSolicitud($mensaje);
    }

    // Método para establecer el contexto de la conversación
    private function SetContexto($contexto)
    {
        $this->contexto = $contexto;
    }

    // Método para eliminar el contexto después de una consulta
    private function EliminarContexto()
    {
        $this->contexto = null;
    }
}
