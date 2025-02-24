<?php
require_once '../controlador/IAController.php';
require_once '../controlador/RecetasController.php';

$controller = new IAController();
$controllerreceta = new RecetasController;

// Inicializar variables para los datos de la receta
$error_message = '';
$success_message = '';

// Procesar la búsqueda de recetas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prompt'])) {
    $prompt = $_POST['prompt'] ?? '';

    if (!empty($prompt)) {
        $nombre = $prompt;
        // Primer paso: obtener ingredientes
        $Resingredientes = $controller->PedirIngredientes($prompt);
        $Resingredientes = json_decode($Resingredientes, true); // Decodificar JSON a array

        if (is_array($Resingredientes) && isset($Resingredientes['choices'][0]['message']['content'])) {
            $ingredientes = $Resingredientes['choices'][0]['message']['content'];

            // Segundo paso: obtener preparación (cambiado de orden)
            $Respreparacion = $controller->PedirDesarrollo($prompt);
            $Respreparacion = json_decode($Respreparacion, true);

            if (is_array($Respreparacion) && isset($Respreparacion['choices'][0]['message']['content'])) {
                $preparacion = $Respreparacion['choices'][0]['message']['content'];

                // Tercer paso: obtener resumen (cambiado de orden)
                $Resdescripcion = $controller->PedirResumen($prompt);
                $Resdescripcion = json_decode($Resdescripcion, true);

                if (is_array($Resdescripcion) && isset($Resdescripcion['choices'][0]['message']['content'])) {
                    $descripcion = $Resdescripcion['choices'][0]['message']['content'];

                    // Cuarto paso: obtener imagen (mantiene su posición)
                    $Resimagen = $controller->PedirImagen($prompt);
                    $Resimagen = json_decode($Resimagen, true);

                    if (is_array($Resimagen) && isset($Resimagen['choices'][0]['message']['content'])) {
                        $imagen = $Resimagen['choices'][0]['message']['content'];
                    } else {
                        $error_message = 'No se pudo obtener la imagen.';
                    }
                } else {
                    $error_message = 'No se pudo obtener la descripción.';
                }
            } else {
                $error_message = 'No se pudo obtener la preparación.';
            }
        } else {
            $error_message = 'No se pudieron obtener los ingredientes.';
        }
    } else {
        $error_message = 'No se encontró un prompt.';
    }
}

// Procesar la acción de guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'guardar') {
    // Obtener los datos del formulario de guardar
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $ingredientes = $_POST['ingredientes'] ?? '';
    $pasos = $_POST['preparacion'] ?? '';

    // Verificar que tenemos todos los datos necesarios
    if (!empty($nombre) && !empty($descripcion) && !empty($ingredientes)) {
        // Llamar al método para guardar la receta
        if ($controllerreceta->GuardarReceta($nombre, $descripcion, $preparacion, $ingredientes)) {
            $success_message = "¡Receta guardada con éxito!";
            // Mantener los datos para seguir mostrando la receta después de guardar
            $prompt = $nombre;
        } else {
            $error_message = "Error al guardar la receta";
        }
    } else {
        $error_message = "Faltan datos necesarios para guardar la receta";
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

        .custom-alert-success {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
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

        .recipe-image {
            max-width: 100%;
            height: auto;
            margin: 15px 0;
            border-radius: 8px;
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

        <?php if (!empty($success_message)): ?>
            <div class="alert custom-alert-success">
                <?= htmlspecialchars($success_message) ?>
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

                <?php if (isset($nombre) && !empty($nombre)): ?>
                    <div class="recipe-card">
                        <div class="recipe-header">
                            <h2 class="text-center mb-0"><?= htmlspecialchars($nombre) ?></h2>
                        </div>
                        <div class="text-center mt-3 mb-3">
                            <!-- Formulario para guardar con campos ocultos en lugar de un enlace -->
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="guardar">
                                <input type="hidden" name="nombre" value="<?= htmlspecialchars($nombre) ?>">
                                <input type="hidden" name="descripcion" value="<?= htmlspecialchars($descripcion ?? '') ?>">
                                <input type="hidden" name="ingredientes" value="<?= htmlspecialchars($ingredientes ?? '') ?>">
                                <input type="hidden" name="preparacion" value="<?= htmlspecialchars($imagen ?? '') ?>">
                                <button type="submit" class="btn btn-success">Guardar Receta</button>
                            </form>
                        </div>
                        <div class="recipe-body">
                            <?php if (isset($descripcion) && !empty($descripcion)): ?>
                                <div class="recipe-section">
                                    <h4>Descripción</h4>
                                    <p><?= nl2br(htmlspecialchars($descripcion)) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($ingredientes) && !empty($ingredientes)): ?>
                                <div class="recipe-section">
                                    <h4>Ingredientes</h4>
                                    <div class="ingredient-list">
                                        <?= nl2br(htmlspecialchars($ingredientes)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($imagen) && !empty($imagen)): ?>
                                <div class="recipe-section">
                                    <h4>Preparación</h4>
                                    <div class="steps"><?= nl2br(htmlspecialchars($imagen)) ?></div>
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