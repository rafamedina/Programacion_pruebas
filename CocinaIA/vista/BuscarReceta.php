<?php require_once '../controlador/IAController.php'; // Incluyo el controlador  
$controller = new IAController(); // Instancio el controlador
$error_message = ''; // Variable para manejar errores
$descripcion = ''; // Variable para almacenar la respuesta  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Corregimos el nombre del input
    $prompt = $_POST['prompt'] ?? '';
    $respuesta = $controller->makeRequest($prompt);

    if ($respuesta !== null) {
        // Decodificar la respuesta JSON
        $dataResponse = json_decode($respuesta, true);

        // Verificar si la respuesta tiene el formato esperado
        if (isset($dataResponse['choices'][0]['message']['content'])) {
            // Guardamos el contenido en la variable $descripcion en lugar de hacer echo
            $descripcion = $dataResponse['choices'][0]['message']['content'];
        } else {
            $error_message = "No se recibió una respuesta válida.";
        }
    } else {
        $error_message = "No se recibió respuesta.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Tu código de head sin cambios -->
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