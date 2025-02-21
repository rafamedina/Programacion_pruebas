<?php require_once '../controlador/IAController.php';

$controller = new IAController();
$error_message = '';

// Inicializar variables para los datos de la receta
$nombre = '';
$descripcion = '';
$ingredientes = [];
$preparacion = '';
$categoria = '';
$imagen = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = $_POST['prompt'] ?? '';

    if (!empty($prompt)) {
        $respuesta = $controller->makeRequest($prompt);

        if ($respuesta !== null) {
            // Decodificar la respuesta JSON
            $dataResponse = json_decode($respuesta, true);

            // Verificar si la respuesta tiene el formato esperado
            if (isset($dataResponse['choices'][0]['message']['content'])) {
                $contenido = $dataResponse['choices'][0]['message']['content'];

                // Intentar extraer información del texto libre
                // Extraer nombre (asumimos que está en el prompt)
                $nombre = ucwords($prompt);
                if (strpos($nombre, "Receta") === 0) {
                    $nombre = substr($nombre, 7); // Quitar "Receta "
                }

                // Extraer descripción (primer párrafo normalmente)
                $parrafos = explode("\n\n", $contenido);
                if (!empty($parrafos[0])) {
                    $descripcion = $parrafos[0];
                }

                // Extraer ingredientes
                if (preg_match('/###\s*Ingredientes:(.+?)###/s', $contenido, $matches)) {
                    $ingredientesTexto = $matches[1];
                    $lineas = explode("\n", $ingredientesTexto);
                    foreach ($lineas as $linea) {
                        $linea = trim($linea);
                        if (strpos($linea, '-') === 0) {
                            $ingredientes[] = trim(substr($linea, 1));
                        }
                    }
                }

                // Extraer preparación
                if (preg_match('/###\s*Instrucciones:(.+?)(?:###|$)/s', $contenido, $matches)) {
                    $preparacion = trim($matches[1]);
                }

                // Asignar categoría por defecto
                $categoria = "Plato principal";
            } else {
                $error_message = "No se recibió una respuesta con el formato esperado.";
            }
        } else {
            $error_message = "No se recibió respuesta del servicio.";
        }
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

        .recipe-card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 25px;
            background-color: #fff;
        }

        .recipe-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            border-radius: 8px 8px 0 0;
        }

        .recipe-body {
            padding: 20px;
        }

        .recipe-section {
            margin-bottom: 20px;
        }

        .recipe-section h4 {
            color: #e67e22;
            border-bottom: 2px solid #e67e22;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .ingredient-list {
            list-style-type: disc;
            padding-left: 20px;
        }

        .steps {
            white-space: pre-line;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <h1 class="mt-4 text-center">Buscar Receta</h1>

        <?php if (!empty($error_message)): ?>
            <div class="alert custom-alert-sandybrown">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <form method="POST" action="" class="mt-4 p-4 bg-white rounded shadow-sm">
                    <div class="form-group mb-3">
                        <label for="prompt" class="form-label">¿Qué receta buscas?</label>
                        <input type="text" class="form-control" id="prompt" name="prompt"
                            placeholder="Ej: Receta de paella valenciana" required
                            value="<?= htmlspecialchars($prompt ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </form>

                <?php if (!empty($nombre) || !empty($descripcion)): ?>
                    <div class="recipe-card">
                        <div class="recipe-header">
                            <h2 class="text-center mb-0"><?= htmlspecialchars($nombre) ?></h2>
                            <?php if (!empty($categoria)): ?>
                                <p class="text-center text-muted mb-0">Categoría: <?= htmlspecialchars($categoria) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="recipe-body">
                            <?php if (!empty($descripcion)): ?>
                                <div class="recipe-section">
                                    <h4>Descripción</h4>
                                    <p><?= nl2br(htmlspecialchars($descripcion)) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($ingredientes)): ?>
                                <div class="recipe-section">
                                    <h4>Ingredientes</h4>
                                    <ul class="ingredient-list">
                                        <?php foreach ($ingredientes as $ingrediente): ?>
                                            <li><?= htmlspecialchars($ingrediente) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($preparacion)): ?>
                                <div class="recipe-section">
                                    <h4>Preparación</h4>
                                    <div class="steps"><?= nl2br(htmlspecialchars($preparacion)) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>