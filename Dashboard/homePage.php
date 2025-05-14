<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="user-info">
                <img src="assets/img/avatar.svg" alt="Avatar del Usuario" class="avatar">
                <h3>Nombre del Usuario</h3>
                <p>Cargo del Usuario</p>
                <p id="system-time"></p>
                <button class="logout-btn">Cerrar Sesión</button>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="?page=ventas">Ventas</a></li>
                    <li><a href="?page=productos">Productos</a></li>
                    <li><a href="?page=clientes">Clientes</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Bienvenido al Dashboard</h1>
            




            <?php
include 'db/connection.php'; // Asegúrate que existe este archivo con tu conexión

// Consulta: total de ventas del mes
$result_ventas = $conn->query("SELECT SUM(total) AS total_ventas FROM sales WHERE MONTH(dateSales) = MONTH(CURRENT_DATE())");
$row_ventas = $result_ventas->fetch_assoc();

// Consulta: número de clientes
$result_clientes = $conn->query("SELECT COUNT(*) AS total_clientes FROM clients");
$row_clientes = $result_clientes->fetch_assoc();

// Consulta: productos en stock
$result_productos = $conn->query("SELECT SUM(stock) AS total_stock FROM cars");
$row_productos = $result_productos->fetch_assoc();
?>

<div class="cards-container">
    <div class="card">
        <h2>Ventas del Mes</h2>
        <p>Q<?php echo number_format($row_ventas['total_ventas'], 2); ?></p>
    </div>
    <div class="card">
        <h2>Total de Clientes</h2>
        <p><?php echo $row_clientes['total_clientes']; ?></p>
    </div>
    <div class="card">
        <h2>Productos en Stock</h2>
        <p><?php echo $row_productos['total_stock']; ?></p>
    </div>
</div>





            <!-- Contenido dinámico -->
            <div class="dynamic-content">
                <?php
// Agregar el contenido dinámico basado en el botón del sidebar
if (isset($_GET['page'])) {
    $page = $_GET['page'];

    if ($page === 'clientes') {
        include 'includes/clientes.php';
    } elseif ($page === 'productos') {
        include 'includes/products.php';
    } elseif ($page === 'ventas') {
        include 'includes/sales.php';
    }
}

                
                ?>
            </div>
        </main>
    </div>
    <!-- Hora del sistema -->
    <script>
        function updateSystemTime() {
            const timeElement = document.getElementById('system-time');
            const now = new Date();
            timeElement.textContent = now.toLocaleTimeString();
        }
        setInterval(updateSystemTime, 1000);
        updateSystemTime();
    </script>
</body>
</html>
