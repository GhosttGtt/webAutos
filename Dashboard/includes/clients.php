<?php
// Obtener token de autenticación una sola vez
function obtenerToken()
{
    $url = 'https://alexcg.de/autozone/api/login.php';
    $credentials = [
        'username' => 'ghost',
        'password' => '12345'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

$token = obtenerToken(); // Obtener token al inicio

// API: Listar clientes
function apiClientsList($token)
{
    if (!$token) return [];

    $url = 'https://alexcg.de/autozone/api/clients.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);

    return $data['data'] ?? [];
}

// API: Crear cliente
function crearCliente($data, $token)
{
    if (!$token) return false;

    $url = 'https://alexcg.de/autozone/api/clients_create.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response, true);
    return $result['success'] ?? false;
}

// API: Actualizar cliente
function actualizarCliente($data, $token)
{
    if (!$token) return false;

    $url = 'https://alexcg.de/autozone/api/clients_update.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response, true);
    return $result['success'] ?? false;
}

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_cliente'])) {
        $clienteData = [
            'name' => $_POST['nombre'],
            'email' => $_POST['email'],
            'phone' => $_POST['telefono']
        ];

        if (crearCliente($clienteData, $token)) {
            $mensaje = 'Cliente creado exitosamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al crear el cliente';
            $tipo_mensaje = 'error';
        }
    } elseif (isset($_POST['update_cliente'])) {
        $clienteData = [
            'id' => $_POST['client_id'],
            'name' => $_POST['nombre'],
            'email' => $_POST['email'],
            'phone' => $_POST['telefono']
        ];

        if (actualizarCliente($clienteData, $token)) {
            $mensaje = 'Cliente actualizado exitosamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar el cliente';
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener lista de clientes
$clientes = apiClientsList($token);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mui/material@5.14.0/dist/mui.min.css" />
    <link rel="stylesheet" href="assets/css/crud-styles.css">
</head>

<body>
    <div class="container">
        <div class="paper">
            <h2 class="title">Gestión de Clientes</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="alert <?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de Cliente -->
            <form id="clientForm" method="POST" class="form">
                <input type="hidden" name="client_id" id="client_id">

                <div class="form-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="text-field" required>
                </div>

                <div class="form-field">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="text-field" required>
                </div>

                <div class="form-field">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="text-field" required>
                </div>

                <button type="submit" name="create_cliente" id="submitBtn" class="button primary">
                    Agregar Cliente
                </button>
            </form>

            <!-- Tabla de Clientes -->
            <?php if (!empty($clientes)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr data-id="<?= htmlspecialchars($cliente['id'] ?? '') ?>"
                                    data-nombre="<?= htmlspecialchars($cliente['name'] ?? '') ?>"
                                    data-email="<?= htmlspecialchars($cliente['email'] ?? '') ?>"
                                    data-telefono="<?= htmlspecialchars($cliente['phone'] ?? '') ?>">
                                    <td><?= htmlspecialchars($cliente['id'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($cliente['name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($cliente['email'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($cliente['phone'] ?? '') ?></td>
                                    <td class="actions">
                                        <button onclick="editCliente(this.closest('tr'))" class="button edit">
                                            <span class="material-icons">edit</span>
                                        </button>
                                        <button onclick="toggleCliente(this.closest('tr'))" class="button toggle">
                                            <span class="material-icons">toggle_off</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">No hay clientes registrados.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Citas de Clientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Servicio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="citasTableBody">
                                    <!-- Las citas se cargarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editCliente(row) {
            document.getElementById("client_id").value = row.dataset.id;
            document.getElementById("nombre").value = row.dataset.nombre;
            document.getElementById("email").value = row.dataset.email;
            document.getElementById("telefono").value = row.dataset.telefono;
            document.getElementById("submitBtn").name = "update_cliente";
            document.getElementById("submitBtn").textContent = "Actualizar Cliente";
            document.getElementById("clientForm").scrollIntoView({
                behavior: "smooth"
            });
        }

        // Función para limpiar el formulario
        function limpiarFormulario() {
            document.getElementById("client_id").value = "";
            document.getElementById("nombre").value = "";
            document.getElementById("email").value = "";
            document.getElementById("telefono").value = "";
            document.getElementById("submitBtn").name = "create_cliente";
            document.getElementById("submitBtn").textContent = "Agregar Cliente";
        }

        // Agregar botón de cancelar edición
        document.getElementById("clientForm").insertAdjacentHTML('beforeend', 
            '<button type="button" onclick="limpiarFormulario()" class="button secondary" style="margin-left: 10px;">Cancelar</button>'
        );

        function toggleCliente(row) {
            const cells = row.querySelectorAll('td:not(:last-child)');
            const toggleButton = row.querySelector('.toggle .material-icons');
            
            if (row.classList.contains('disabled')) {
                // Reactivar cliente
                cells.forEach(cell => {
                    cell.style.textDecoration = 'none';
                    cell.style.color = 'inherit';
                });
                toggleButton.textContent = 'toggle_off';
                row.classList.remove('disabled');
            } else {
                // Desactivar cliente
                cells.forEach(cell => {
                    cell.style.textDecoration = 'line-through';
                    cell.style.color = '#999';
                });
                toggleButton.textContent = 'toggle_on';
                row.classList.add('disabled');
            }
        }

        // Función para cargar las citas
        function loadCitas() {
            fetch('https://alexcg.de/autozone/api/citas.php', {
                headers: {
                    'Authorization': 'Bearer ' + '<?php echo $_SESSION['token']; ?>'
                }
            })
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('citasTableBody');
                tableBody.innerHTML = '';
                
                data.forEach(cita => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${cita.id}</td>
                        <td>${cita.client_name}</td>
                        <td>${cita.date}</td>
                        <td>${cita.time}</td>
                        <td>${cita.service}</td>
                        <td>
                            <span class="badge ${getStatusBadgeClass(cita.status)}">
                                ${cita.status}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="updateCitaStatus(${cita.id}, 'confirmada')">
                                <i class="material-icons">check</i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="updateCitaStatus(${cita.id}, 'cancelada')">
                                <i class="material-icons">close</i>
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar las citas');
            });
        }

        // Función para obtener la clase del badge según el estado
        function getStatusBadgeClass(status) {
            switch(status.toLowerCase()) {
                case 'pendiente':
                    return 'bg-warning';
                case 'confirmada':
                    return 'bg-success';
                case 'cancelada':
                    return 'bg-danger';
                default:
                    return 'bg-secondary';
            }
        }

        // Función para actualizar el estado de una cita
        function updateCitaStatus(citaId, newStatus) {
            // Aquí irá la URL de la API para actualizar el estado de la cita
            const apiUrl = 'URL_DE_LA_API_PARA_ACTUALIZAR_CITA';
            
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + '<?php echo $_SESSION['token']; ?>'
                },
                body: JSON.stringify({
                    id: citaId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadCitas(); // Recargar la lista de citas
                } else {
                    alert('Error al actualizar el estado de la cita: ' + (result.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estado de la cita');
            });
        }

        // Cargar las citas cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            loadCitas();
        });
    </script>
</body>

</html>