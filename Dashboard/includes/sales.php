<?php
// Requiere conexión activa en $conn
$result_ventas = $conn->query("
    SELECT c.name AS client_name, COUNT(s.id) AS total_ventas 
    FROM sales s 
    JOIN clients c ON s.client_id = c.id
    WHERE MONTH(s.dateSales) = MONTH(CURRENT_DATE())
    GROUP BY c.name
");

$clientes = [];
$ventas = [];

while ($row = $result_ventas->fetch_assoc()) {
    $clientes[] = $row['client_name'];
    $ventas[] = $row['total_ventas'];
}
?>

<h2>Ventas por Cliente</h2>
<canvas id="ventasChart" style="max-width:800px; max-height:400px;"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('ventasChart').getContext('2d');
const ventasChart = new Chart(ctx, {
    type: 'bar', // Cambiar a gráfico de barras
    data: {
        labels: <?php echo json_encode($clientes); ?>, // Etiquetas de clientes
        datasets: [{
            label: 'Ventas por Cliente', 
            data: <?php echo json_encode($ventas); ?>, // Datos de ventas
            backgroundColor: 'rgba(75, 192, 192, 0.7)', // Color de las barras
            borderColor: 'rgba(75, 192, 192, 1)', // Color del borde
            borderWidth: 1 // Ancho del borde de las barras
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true, // Empezar desde cero en el eje Y
                ticks: {
                    stepSize: 1 // Paso entre las marcas del eje Y
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw + ' ventas';
                    }
                }
            }
        }
    }
});
</script>
