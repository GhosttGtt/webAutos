<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//funcion Api Ventas @Isma
function apiSalesList()
{
    $url = 'https://alexcg.de/autozone/api/sales.php';
    //inicializar curl
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Error en la API de ventas: ' . curl_error($curl));
        curl_close($curl);
        return [];
    }

    curl_close($curl);
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar JSON de la API de ventas: ' . json_last_error_msg());
        return [];
    }

    if (empty($data)) {
        error_log('La API de ventas devolvió un array vacío');
    }

    return $data;
}
// Inicializar filtros
$filtros = [
    'periodo' => isset($_POST['periodo']) ? $_POST['periodo'] : 'mes',
    'año' => isset($_POST['año']) ? $_POST['año'] : date('Y'),
    'mes' => isset($_POST['mes']) ? $_POST['mes'] : date('m'),
    'semana' => isset($_POST['semana']) ? $_POST['semana'] : date('W'),
    'modelo' => isset($_POST['modelo']) ? $_POST['modelo'] : '',
    'precio_min' => isset($_POST['precio_min']) ? $_POST['precio_min'] : '',
    'precio_max' => isset($_POST['precio_max']) ? $_POST['precio_max'] : '',
    'tipo_carro' => isset($_POST['tipo_carro']) ? $_POST['tipo_carro'] : ''
];

// Obtener datos de ventas a través de la API
$ventas = apiSalesList();

// Filtrar los datos según los criterios
$ventas_filtradas = array_filter($ventas, function ($venta) use ($filtros) {
    if (!is_array($venta)) {
        return false;
    }

    // Verificar que los índices necesarios existan
    if (
        !isset($venta['sale_date']) || !isset($venta['car_model']) ||
        !isset($venta['total']) || !isset($venta['car_type'])
    ) {
        return false;
    }

    $fecha_venta = strtotime($venta['sale_date']);
    if ($fecha_venta === false) {
        return false;
    }

    $cumple_filtros = true;

    // Filtrar por período
    if ($filtros['periodo'] === 'mes') {
        $cumple_filtros = $cumple_filtros &&
            date('Y', $fecha_venta) == $filtros['año'] &&
            date('m', $fecha_venta) == $filtros['mes'];
    } elseif ($filtros['periodo'] === 'semana') {
        $cumple_filtros = $cumple_filtros &&
            date('Y', $fecha_venta) == $filtros['año'] &&
            date('W', $fecha_venta) == $filtros['semana'];
    }

    // Filtrar por modelo
    if (!empty($filtros['modelo'])) {
        $cumple_filtros = $cumple_filtros &&
            stripos($venta['car_model'], $filtros['modelo']) !== false;
    }

    // Filtrar por precio
    if (!empty($filtros['precio_min'])) {
        $cumple_filtros = $cumple_filtros &&
            floatval($venta['total']) >= floatval($filtros['precio_min']);
    }
    if (!empty($filtros['precio_max'])) {
        $cumple_filtros = $cumple_filtros &&
            floatval($venta['total']) <= floatval($filtros['precio_max']);
    }

    // Filtrar por tipo de carro
    if (!empty($filtros['tipo_carro'])) {
        $cumple_filtros = $cumple_filtros &&
            $venta['car_type'] === $filtros['tipo_carro'];
    }

    return $cumple_filtros;
});

// Procesar datos filtrados para las gráficas
$datos = [
    'clientes' => [],
    'ventas' => [],
    'modelos' => [],
    'totales' => []
];

foreach ($ventas_filtradas as $venta) {
    $datos['clientes'][] = $venta['client_name'];
    $datos['ventas'][] = 1; // Contador de ventas
    $datos['modelos'][] = $venta['car_model'];
    $datos['totales'][] = floatval($venta['total']);
}
?>

<!-- Material UI Dependencies -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="assets/css/sales.css" rel="stylesheet">
<h1>Panel de ventas</h1>
<div class="sales-container">
    <div class="filters-section">

        <button id="openFiltersBtn" class="mui-btn">
            <i class="material-icons">filter_list</i>
            Filtrar Datos
        </button>
    </div>

    <div class="charts-container">
        <div class="chart-wrapper">
            <canvas id="ventasChart"></canvas>
        </div>
        <div class="chart-wrapper">
            <canvas id="ventasPorModeloChart"></canvas>
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div id="filterModal" class="modal">
    <div class="modal-content">
        <h2>Filtros de Ventas</h2>
        <form id="filterForm" method="POST">
            <div class="form-group">
                <label>Período</label>
                <select name="periodo" class="mui-select">
                    <option value="mes">Mes</option>
                    <option value="semana">Semana</option>
                    <option value="año">Año</option>
                </select>
            </div>

            <div class="form-group">
                <label>Año</label>
                <input type="number" name="año" value="<?php echo date('Y'); ?>" class="mui-input">
            </div>

            <div class="form-group">
                <label>Rango de Precios</label>
                <div class="price-range">
                    <input type="number" name="precio_min" placeholder="Mínimo" class="mui-input">
                    <input type="number" name="precio_max" placeholder="Máximo" class="mui-input">
                </div>
            </div>

            <div class="form-group">
                <label>Marca de Carro</label>
                <input type="text" name="modelo" class="mui-input">
            </div>

            <div class="modal-actions">
                <button type="submit" class="mui-btn mui-btn--primary">Aplicar Filtros</button>
                <button type="button" class="mui-btn mui-btn--flat" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración de gráficas
    const ventasConfig = {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($datos['clientes']); ?>,
            datasets: [{
                label: 'Ventas por Cliente',
                data: <?php echo json_encode($datos['ventas']); ?>,
                backgroundColor: 'rgba(132, 0, 255, 0.7)',
                borderColor: 'rgba(132, 0, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ventas por Cliente',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'bottom'
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    };

    const modelosConfig = {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($datos['modelos']); ?>,
            datasets: [{
                data: <?php echo json_encode($datos['totales']); ?>,
                backgroundColor: [
                    'rgba(132, 0, 255, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ventas por Modelo',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    };

    // Inicializar gráficas
    const ventasChartCtx = document.getElementById('ventasChart').getContext('2d');
    new Chart(ventasChartCtx, ventasConfig);

    const modelosChartCtx = document.getElementById('ventasPorModeloChart').getContext('2d');
    new Chart(modelosChartCtx, modelosConfig);

    // Funciones del modal
    function openModal() {
        document.getElementById('filterModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('filterModal').style.display = 'none';
    }

    // Event listeners
    document.getElementById('openFiltersBtn').addEventListener('click', openModal);

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('filterModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>