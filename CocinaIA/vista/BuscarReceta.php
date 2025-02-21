<?php
require_once '../controlador/IAController.php'; // Incluyo el controlador

$controller = new IAController(); // Instancio el controlador
$error_message = ''; // Variable para manejar errores
$descripcion = ''; // Variable para almacenar la respuesta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Corregimos el nombre del input
    $prompt = $_POST['prompt'] ?? '';

    if (!empty($prompt)) {
        $respuesta = $controller->makeRequest($prompt);

        if ($respuesta !== null) {
            $dataResponse = json_decode($respuesta, true);

            if (isset($dataResponse['choices'][0]['message']['content'])) {
                // Decodificamos el JSON de la respuesta
                $jsonResponse = json_decode($dataResponse['choices'][0]['message']['content'], true);

                if ($jsonResponse) {
                    // Extraemos solo los campos deseados
                    $resultado = [
                        "nombre" => $jsonResponse['nombre'],
                        "ingredientes" => $jsonResponse['ingredientes'],
                        "preparacion" => $jsonResponse['preparacion']
                    ];

                    // Guardamos la descripción para mostrarla en el input
                    $descripcion = $jsonResponse['nombre']; // O podrías usar otro campo como 'descripcion'
                } else {
                    $error_message = "Error al procesar la respuesta JSON.";
                }
            } else {
                $error_message = "No se recibió una respuesta válida.";
            }
        } else {
            $error_message = "No se recibió respuesta del servidor.";
        }
    } else {
        $error_message = "El campo de búsqueda no puede estar vacío.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Buscar Receta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-alert-sandybrown {
            color: #fff;
            background-color: sandybrown;
            border-color: sandybrown;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Buscar Receta</h1>

        <?php if (!empty($error_message)): ?>
            <div class="alert custom-alert-sandybrown">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="mt-4">
            <div class="form-group">
                <label for="prompt">Buscar receta :</label>
                <input type="text" class="form-control" id="prompt" name="prompt" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Buscar</button>
        </form>

        <div class="mt-4">
            <label for="name">Descripción</label>
            <input type="text" class="form-control" id="name" name="name" required minlength="4" maxlength="50"
                value="<?= htmlspecialchars($descripcion) ?>" readonly />
        </div>
    </div>
</body>

</html>