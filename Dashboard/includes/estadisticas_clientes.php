<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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


function apiClientsList($token)
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
$sales_data = apiClientsList($token);

$client_purchases = [];
foreach ($sales_data as $sale) {
    $client_name = $sale['client_name'] ?? 'Desconocido';
    $client_purchases[$client_name] = ($client_purchases[$client_name] ?? 0) + 1;
}

arsort($client_purchases);

$top_clients = array_slice($client_purchases, 0, 5, true);

$chart_labels = json_encode(array_keys($top_clients));
$chart_data = json_encode(array_values($top_clients));


?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mui/material@5.14.0/dist/mui.min.css" />

<div class="container-fluid mt-4">
    <h1 class="mb-4">Estadísticas de Clientes</h1>

    <!-- Sección de Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtrar Estadísticas</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4 mb-3">
                    <label for="filterAmount" class="form-label">Monto:</label>
                    <input type="number" class="form-control" id="filterAmount" placeholder="Monto mínimo">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="filterDate" class="form-label">Fecha:</label>
                    <input type="date" class="form-control" id="filterDate">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="filterClientName" class="form-label">Nombre del Cliente:</label>
                    <input type="text" class="form-control" id="filterClientName" placeholder="Nombre del cliente">
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary me-2">Aplicar Filtros</button>
                    <button type="button" class="btn btn-secondary" id="clearFilters">Limpiar Filtros</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Clientes con Más Compras</h5>
        </div>
        <div class="card-body">
            <canvas id="topClientsChart"></canvas>
        </div>
    </div>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientData = {
            labels: <?php echo $chart_labels; ?>,
            datasets: [{
                label: 'Número de Autos Comprados',
                data: <?php echo $chart_data; ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            data: clientData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Top Clientes por Compras'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Autos'
                        }
                    }
                }
            },
        };

        var topClientsChart = new Chart(
            document.getElementById('topClientsChart'),
            config
        );

        const filterForm = document.getElementById('filterForm');
        const clearFiltersBtn = document.getElementById('clearFilters');

        filterForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const amount = document.getElementById('filterAmount').value;
            const date = document.getElementById('filterDate').value;
            const clientName = document.getElementById('filterClientName').value;

            console.log('Aplicando filtros:', {
                amount,
                date,
                clientName
            });

        });

        clearFiltersBtn.addEventListener('click', function() {
            filterForm.reset();
            console.log('Filtros limpiados');
        });
    });
</script>