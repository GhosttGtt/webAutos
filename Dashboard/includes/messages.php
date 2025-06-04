<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Función para obtener el token de autenticación
function obtenerToken()
{
    $url = 'https://alexcg.de/autozone/api/login.php';
    $credenciales = [
        'username' => 'ghost',
        'password' => '12345'
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode($credenciales),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $respuesta = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($respuesta, true);
    return $data['token'] ?? null;
}

// Función para obtener los mensajes usando el token
function apiMessagesList($token)
{
    $url = 'https://alexcg.de/autozone/api/cars_comment.php';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $respuesta = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($respuesta, true);
    return $data['data'] ?? [];
}

// Obtener token y mensajes
$token = obtenerToken();
$mensajes = $token ? apiMessagesList($token) : [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mensajes de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">Mensajes de Clientes</h1>

        <?php if (!$token): ?>
            <div class="alert alert-danger" role="alert">
                Error: No se pudo obtener el token de autenticación.
            </div>
        <?php elseif (empty($mensajes)): ?>
            <div class="alert alert-info" role="alert">
                No hay mensajes disponibles en este momento.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Comentario</th>
                            <th>Estrellas</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mensajes as $msg): ?>
                            <tr>
                                <td><?= htmlspecialchars($msg['id']) ?></td>
                                <td><?= htmlspecialchars($msg['comment'] ?: 'Sin comentario') ?></td>
                                <td><?= htmlspecialchars($msg['stars']) ?> <i class="fas fa-star text-warning"></i></td>
                                <td><?= htmlspecialchars($msg['car_brand'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($msg['car_model'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($msg['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>