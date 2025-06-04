<?php
session_start();

if (!isset($_SESSION['token']) || !isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$userData = $_SESSION['user'];

$loggedInUsername = $userData['name'] ?? 'Usuario';
$loggedInUserRole = $userData['role'] ?? 'Sin rol';
$avatarUrl = $userData['img'] ?? 'assets/img/avatar.svg';

// Función para actualizar la foto
function actualizarFotoPerfil($photoData, $token) {
    $url = 'https://alexcg.de/autozone/api/user_photo_update.php';
    
    $data = [
        'photo' => $photoData
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Procesar actualización de foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_photo'])) {
    $photo = $_FILES['new_photo'];
    if ($photo['error'] === UPLOAD_ERR_OK) {
        $photoData = base64_encode(file_get_contents($photo['tmp_name']));
        $result = actualizarFotoPerfil($photoData, $_SESSION['token']);
        
        if (isset($result['success']) && $result['success'] === true) {
            // Actualizar la sesión con la nueva foto
            $_SESSION['user']['img'] = $result['photo_url'] ?? $avatarUrl;
            $avatarUrl = $_SESSION['user']['img'];
            $userData = $_SESSION['user'];
        }
    }
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
    <link rel="stylesheet" href="assets/css/index.css" />
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
                    <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar del Usuario" class="avatar" id="userAvatar" style="cursor: pointer;" />
                    <div class="avatar-status"></div>
                </div>
                <h3><?php echo htmlspecialchars($loggedInUsername); ?></h3>
                <p class="user-role">
                    <i class="material-icons">work</i> <?php echo htmlspecialchars($loggedInUserRole); ?>
                </p>
                <p id="system-time" class="system-time">
                    <i class="material-icons">access_time</i> <span></span>
                </p>
                <a href="?logout=true" class="logout-btn">
                    <i class="material-icons">exit_to_app</i> Cerrar Sesión
                </a>
            </div>
            <nav class="menu">
                <ul>
                    <li>
                        <div class="menu-item-parent">
                            <i class="material-icons">dashboard</i>
                            <span>Dashboard Ventas</span>
                            <i class="material-icons expand-icon">expand_more</i>
                        </div>
                        <ul class="submenu">
                            <li>
                                <a href="?page=usuarios" class="menu-item">
                                    <i class="material-icons">face</i>
                                    <span>Usuarios</span>
                                </a>
                            </li>
                            <li>
                                <a href="?page=productos" class="menu-item">
                                    <i class="material-icons">directions_car</i>
                                    <span>Productos</span>
                                </a>
                            </li>
                            <li>
                                <a href="?page=ventas" class="menu-item">
                                    <i class="material-icons">receipt</i>
                                    <span>Ventas</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="menu-item-parent">
                            <i class="material-icons">dashboard</i>
                            <span>Dashboard Clientes</span>
                            <i class="material-icons expand-icon">expand_more</i>
                        </div>
                        <ul class="submenu">

                            <li>
                                <a href="?page=mensajes" class="menu-item">
                                    <i class="material-icons">textsms</i>
                                    <span>Mensajes</span>
                                </a>
                            </li>
                            <li>
                                <a href="?page=clientes" class="menu-item">
                                    <i class="material-icons">people</i>
                                    <span>Clientes</span>
                                </a>
                            </li>
                            <li>
                                <a href="?page=estadisticas_clientes" class="menu-item">
                                    <i class="material-icons">bar_chart</i>
                                    <span>Estadísticas Clientes</span>
                                </a>
                            </li><li>
                                <a href="?page=dates" class="menu-item">
                                    <i class="material-icons">today</i>
                                    <span>Citas de Clientes</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <div class="dynamic-content">
                <?php
                $page = $_GET['page'] ?? 'productos';

                switch ($page) {
                    case 'clientes':
                        include 'includes/clients.php';
                        break;
                    case 'productos':
                        include 'includes/products.php';
                        break;
                    case 'ventas':
                        include 'includes/sales.php';
                        break;
                    case 'mensajes':
                        include 'includes/messages.php';
                        break;
                    case 'usuarios':
                        include 'includes/users.php';
                        break;
                    case 'estadisticas_clientes':
                        include 'includes/estadisticas_clientes.php';
                        break;
                    case 'dates':
                        include 'includes/dates.php';
                        break;
                    default:
                        echo "<p>Página no encontrada.</p>";
                        break;
                }
                ?>

            </div>
        </main>
    </div>

    <!-- Modal de Perfil -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Perfil de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar del Usuario" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        <form id="photoUpdateForm" method="POST" enctype="multipart/form-data" class="mt-3">
                            <div class="mb-3">
                                <label for="new_photo" class="form-label">Cambiar foto de perfil</label>
                                <input type="file" class="form-control" id="new_photo" name="new_photo" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Foto</button>
                        </form>
                    </div>
                    <div class="profile-info">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre:</label>
                            <p><?php echo htmlspecialchars($userData['name'] ?? ''); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Usuario:</label>
                            <p><?php echo htmlspecialchars($userData['username'] ?? ''); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p><?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Rol:</label>
                            <p><?php echo htmlspecialchars($userData['role'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateSystemTime() {
            const timeElement = document.getElementById('system-time').querySelector('span');
            const now = new Date();
            timeElement.textContent = now.toLocaleTimeString();
        }
        setInterval(updateSystemTime, 1000);
        updateSystemTime();

        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.getElementById('menu-toggle');
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        document.querySelectorAll('.menu-item-parent').forEach(item => {
            item.addEventListener('click', function() {
                const submenu = this.nextElementSibling;
                submenu.classList.toggle('active');
                const expandIcon = this.querySelector('.expand-icon');
                expandIcon.textContent = submenu.classList.contains('active') ? 'expand_less' : 'expand_more';
            });
        });

        // Mostrar modal al hacer clic en el avatar
        document.getElementById('userAvatar').addEventListener('click', function() {
            var profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
            profileModal.show();
        });

        // Manejar la actualización de la foto
        document.getElementById('photoUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la foto');
            });
        });
    </script>
</body>

</html>