<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener token
function obtenerToken()
{
    $url = 'https://alexcg.de/autozone/api/login.php';
    $credentials = ['username' => 'ghost', 'password' => '12345'];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode($credentials),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

// Obtener ventas
function apiSalesList($token)
{
    if (!$token) return [];

    $url = 'https://alexcg.de/autozone/api/sales.php';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['data'] ?? [];
}

$token = obtenerToken();
$ventas = apiSalesList($token);

// Filtros
$filtros = [
    'periodo' => $_POST['periodo'] ?? '',
    'año' => $_POST['año'] ?? '',
    'mes' => $_POST['mes'] ?? '',
    'semana' => $_POST['semana'] ?? '',
    'modelo' => $_POST['modelo'] ?? '',
    'precio_min' => $_POST['precio_min'] ?? '',
    'precio_max' => $_POST['precio_max'] ?? '',
    'tipo_carro' => $_POST['tipo_carro'] ?? ''
];

// Aplicar filtros si existen
$ventas_filtradas = array_filter($ventas, function ($venta) use ($filtros) {
    if (!is_array($venta)) return false;
    if (!isset($venta['cars_model'], $venta['total'], $venta['cars_type'])) return false;

    // Como no hay fecha real, usamos fecha actual
    $fecha_venta = time();

    $cumple = true;

    if ($filtros['periodo'] === 'mes' && $filtros['año'] && $filtros['mes']) {
        $cumple &= date('Y', $fecha_venta) == $filtros['año'] && date('m', $fecha_venta) == str_pad($filtros['mes'], 2, '0', STR_PAD_LEFT);
    } elseif ($filtros['periodo'] === 'semana' && $filtros['año'] && $filtros['semana']) {
        $cumple &= date('Y', $fecha_venta) == $filtros['año'] && date('W', $fecha_venta) == str_pad($filtros['semana'], 2, '0', STR_PAD_LEFT);
    }

    if (!empty($filtros['modelo'])) {
        $cumple &= stripos($venta['cars_model'], $filtros['modelo']) !== false;
    }
    if (!empty($filtros['precio_min'])) {
        $cumple &= floatval($venta['total']) >= floatval($filtros['precio_min']);
    }
    if (!empty($filtros['precio_max'])) {
        $cumple &= floatval($venta['total']) <= floatval($filtros['precio_max']);
    }
    if (!empty($filtros['tipo_carro'])) {
        $cumple &= $venta['cars_type'] === $filtros['tipo_carro'];
    }

    return $cumple;
});

// Si no se aplicaron filtros, mostrar todo
if (empty(array_filter($filtros))) {
    $ventas_filtradas = $ventas;
}

// Preparar datos para gráficos
$datos = [
    'clientes' => [],
    'totales_modelo' => [],
    'tipos' => [],
    'por_mes' => []
];

foreach ($ventas_filtradas as $venta) {
    $cliente = $venta['client_name'] ?? 'Desconocido';
    $modelo = $venta['cars_model'];
    $tipo = $venta['cars_type'];
    $monto = floatval($venta['total']);
    $fecha = date('Y-m-d'); // Fecha actual porque no viene en la API
    $mes = date('Y-m', strtotime($fecha));

    $datos['clientes'][$cliente] = ($datos['clientes'][$cliente] ?? 0) + 1;
    $datos['totales_modelo'][$modelo] = ($datos['totales_modelo'][$modelo] ?? 0) + $monto;
    $datos['tipos'][$tipo] = ($datos['tipos'][$tipo] ?? 0) + 1;
    $datos['por_mes'][$mes] = ($datos['por_mes'][$mes] ?? 0) + $monto;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            padding-top: 20px;
        }

        .chart-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .chart-wrapper {
            width: 48%;
        }

        @media (max-width: 768px) {
            .chart-wrapper {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <h1>Panel de Ventas</h1>

    <form method="POST" class="mb-4 p-3 border rounded bg-light">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="periodo" class="form-label">Periodo:</label>
                <select class="form-select" id="periodo" name="periodo">
                    <option value="" <?= $filtros['periodo'] === '' ? 'selected' : '' ?>>--</option>
                    <option value="mes" <?= $filtros['periodo'] === 'mes' ? 'selected' : '' ?>>Mes</option>
                    <option value="semana" <?= $filtros['periodo'] === 'semana' ? 'selected' : '' ?>>Semana</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="año" class="form-label">Año:</label>
                <input type="number" class="form-control" id="año" name="año" value="<?= htmlspecialchars($filtros['año']); ?>">
            </div>
            <div class="col-md-4">
                <label for="mes" class="form-label">Mes:</label>
                <input type="number" class="form-control" id="mes" name="mes" min="1" max="12" value="<?= htmlspecialchars($filtros['mes']); ?>">
            </div>
            <div class="col-md-4">
                <label for="semana" class="form-label">Semana:</label>
                <input type="number" class="form-control" id="semana" name="semana" min="1" max="53" value="<?= htmlspecialchars($filtros['semana']); ?>">
            </div>
            <div class="col-md-4">
                <label for="modelo" class="form-label">Modelo:</label>
                <input type="text" class="form-control" id="modelo" name="modelo" value="<?= htmlspecialchars($filtros['modelo']); ?>">
            </div>
            <div class="col-md-4">
                <label for="precio_min" class="form-label">Precio Min:</label>
                <input type="number" class="form-control" id="precio_min" step="0.01" name="precio_min" value="<?= htmlspecialchars($filtros['precio_min']); ?>">
            </div>
            <div class="col-md-4">
                <label for="precio_max" class="form-label">Precio Max:</label>
                <input type="number" class="form-control" id="precio_max" step="0.01" name="precio_max" value="<?= htmlspecialchars($filtros['precio_max']); ?>">
            </div>
            <div class="col-md-4">
                <label for="tipo_carro" class="form-label">Tipo:</label>
                <input type="text" class="form-control" id="tipo_carro" name="tipo_carro" value="<?= htmlspecialchars($filtros['tipo_carro']); ?>">
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='sales.php'">Limpiar Filtros</button>
        </div>
    </form>

    <div class="chart-container">
        <div class="chart-wrapper"><canvas id="ventasCliente"></canvas></div>
        <div class="chart-wrapper"><canvas id="ventasModelo"></canvas></div>
        <div class="chart-wrapper"><canvas id="ventasTipo"></canvas></div>
        <div class="chart-wrapper"><canvas id="ventasMes"></canvas></div>
    </div>

    <h2 class="mt-4">Tabla de Ventas</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Modelo</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas_filtradas as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta['client_name'] ?? 'Desconocido') ?></td>
                        <td><?= htmlspecialchars($venta['cars_model']) ?></td>
                        <td><?= htmlspecialchars($venta['cars_type']) ?></td>
                        <td>$<?= number_format($venta['total'], 2) ?></td>
                        <td><?= date('Y-m-d') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const chart = (id, type, labels, data, title) => new Chart(document.getElementById(id), {
            type,
            data: {
                labels,
                datasets: [{
                    label: title,
                    data,
                    backgroundColor: ['#845EC2', '#00C9A7', '#FFC75F', '#FF6F91', '#0081CF', '#B0A8B9'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: title
                    }
                }
            }
        });

        chart('ventasCliente', 'bar', Object.keys(<?= json_encode($datos['clientes']); ?>), Object.values(<?= json_encode($datos['clientes']); ?>), 'Ventas por Cliente');
        chart('ventasModelo', 'pie', Object.keys(<?= json_encode($datos['totales_modelo']); ?>), Object.values(<?= json_encode($datos['totales_modelo']); ?>), 'Ventas por Modelo');
        chart('ventasTipo', 'doughnut', Object.keys(<?= json_encode($datos['tipos']); ?>), Object.values(<?= json_encode($datos['tipos']); ?>), 'Ventas por Tipo de Carro');
        chart('ventasMes', 'line', Object.keys(<?= json_encode($datos['por_mes']); ?>), Object.values(<?= json_encode($datos['por_mes']); ?>), 'Ventas por Mes');
    </script>
</body>

</html>