<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="dashboard-container">
        <button id="menu-toggle" class="menu-toggle">
            <span class="material-icons">menu</span>
        </button>
        <aside class="sidebar">
            <div class="user-info">
                <div class="avatar-container">
                    <img src="assets/img/avatar.svg" alt="Avatar del Usuario" class="avatar">
                    <div class="avatar-status"></div>
                </div>
                <h3>Nombre del Usuario</h3>
                <p class="user-role"><i class="material-icons">work</i> Cargo del Usuario</p>
                <p id="system-time" class="system-time"><i class="material-icons">access_time</i> <span></span></p>
                <button class="logout-btn">
                    <i class="material-icons">exit_to_app</i>
                    Cerrar Sesión
                </button>
            </div>
            <nav class="menu">
                <ul>
                    <li>
                    <li>
                        <a href="?page=usuarios" class="menu-item">
                            <i class="material-icons">face</i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=clientes" class="menu-item">
                            <i class="material-icons">people</i>
                            <span>Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=ventas" class="menu-item">
                            <i class="material-icons">trending_up</i>
                            <span>Ventas</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=productos" class="menu-item">
                            <i class="material-icons">directions_car</i>
                            <span>Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=mensajes" class="menu-item">
                            <i class="material-icons">textsms</i>
                            <span>Mensajes</span>
                        </a>
                    </li>
                    </li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">






            <!-- Contenido dinámico -->
            <div class="dynamic-content">
                <?php
                // Agregar el contenido dinámico basado en el botón del sidebar
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];

                    if ($page === 'clientes') {
                        include 'includes/clients.php';
                    } elseif ($page === 'productos') {
                        include 'includes/products.php';
                    } elseif ($page === 'ventas') {
                        include 'includes/sales.php';
                    } elseif ($page === 'mensajes') {
                        include 'includes/messages.php';
                    } elseif ($page === 'usuarios') {
                        include 'includes/users.php';
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

        // Código para el menú hamburguesa
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Cerrar el menú al hacer clic fuera de él
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.getElementById('menu-toggle');
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>

</html>