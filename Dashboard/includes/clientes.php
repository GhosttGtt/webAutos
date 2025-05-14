<?php
// Requiere conexión activa en $conn
$result_clientes = $conn->query("
    SELECT lastname, COUNT(*) as total 
    FROM clients 
    GROUP BY lastname

");

$tipos = [];
$totales = [];

while ($row = $result_clientes->fetch_assoc()) {
    $tipos[] = $row['lastname'];
    $totales[] = $row['total'];
}
?>

<h2>Distribución de Clientes por Tipo</h2>
<canvas id="clientesChart" style="max-width:600px;"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('clientesChart').getContext('2d');
const clientesChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($tipos); ?>,
        datasets: [{
            label: 'Clientes por Tipo',
            data: <?php echo json_encode($totales); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true
    }
});
</script>
