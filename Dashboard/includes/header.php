<?php
// Div de ventas del mes, ganancias y gráfica

// Conexión a la base de datos
include '../db/connection.php';

// Obtener ventas del mes
$mes_actual = date('Y-m');
$ventas_mes = $conn->query("SELECT COUNT(*) as total_ventas, SUM(precio) as ganancias FROM ventas WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes_actual'")->fetch_assoc();

$total_ventas = $ventas_mes['total_ventas'] ?? 0;
$ganancias = $ventas_mes['ganancias'] ?? 0;

// Obtener datos para la gráfica
$grafica_datos = $conn->query("SELECT DATE_FORMAT(fecha, '%d') as dia, COUNT(*) as ventas FROM ventas WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes_actual' GROUP BY dia");
$datos_grafica = [];
while ($fila = $grafica_datos->fetch_assoc()) {
    $datos_grafica[] = $fila;
}

// Mostrar resumen de ventas y ganancias
// Agregar estilos para el resumen de ventas
echo '<div class="ventas-container">';
echo '<h2>Ventas del Mes</h2>';
echo '<p>Total de Ventas: ' . $total_ventas . '</p>';
echo '<p>Ganancias: Q' . $ganancias . '</p>';

if (!empty($datos_grafica)) {
    echo '<h3>Gráfica de Ventas</h3>';
    echo '<canvas id="ventasChart" width="400" height="200"></canvas>';
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    echo '<script>';
    echo 'const ctx = document.getElementById("ventasChart").getContext("2d");';
    echo 'const ventasChart = new Chart(ctx, {';
    echo '    type: "bar",';
    echo '    data: {';
    echo '        labels: ' . json_encode(array_column($datos_grafica, "dia")) . ',';
    echo '        datasets: [{' ;
    echo '            label: "Ventas",';
    echo '            data: ' . json_encode(array_column($datos_grafica, "ventas")) . ',';
    echo '            backgroundColor: "rgba(75, 192, 192, 0.2)",';
    echo '            borderColor: "rgba(75, 192, 192, 1)",';
    echo '            borderWidth: 1';
    echo '        }]';
    echo '    },';
    echo '    options: {';
    echo '        scales: {';
    echo '            y: {';
    echo '                beginAtZero: true';
    echo '            }';
    echo '        }';
    echo '    }';
    echo '});';
    echo '</script>';
} else {
    echo '<p>No hay datos para la gráfica este mes.</p>';
}
echo '</div>';
?>