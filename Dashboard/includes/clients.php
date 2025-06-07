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
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error: ' . json_last_error_msg());
        error_log('API response: ' . $response);
    }
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
    if (curl_errno($ch)) {
        error_log('cURL error in apiClientsList: ' . curl_error($ch));
    }
    curl_close($ch);
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error in apiClientsList: ' . json_last_error_msg());
        error_log('API response in apiClientsList: ' . $response);
    }
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
    if (curl_errno($ch)) {
        error_log('cURL error in crearCliente: ' . curl_error($ch));
    }
    curl_close($ch);
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error in crearCliente: ' . json_last_error_msg());
        error_log('API response in crearCliente: ' . $response);
    }
    return $result['success'] ?? false;
}

// API: Actualizar cliente
function actualizarCliente($data, $token)
{
    if (!$token) return false;

    $url = 'https://alexcg.de/autozone/api/client_edit.php';
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
    if (curl_errno($ch)) {
        error_log('cURL error in crearCliente: ' . curl_error($ch));
    }
    curl_close($ch);
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error in crearCliente: ' . json_last_error_msg());
        error_log('API response in crearCliente: ' . $response);
    }
    return $result['success'] ?? false;
}

// Variables para mensajes
$mensaje = $_GET['mensaje'] ?? '';
$tipo_mensaje = $_GET['tipo_mensaje'] ?? '';

// Procesar formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_cliente'])) {
        $clienteData = [
            'name' => $_POST['nombre'],
            'lastname' => $_POST['apellido'],
            'email' => $_POST['email'],
            'phone' => $_POST['telefono']
        ];

        if (!empty($_POST['password'])) {
            $clienteData['password'] = $_POST['password'];
        }

        if (crearCliente($clienteData, $token)) {
            header('Location: clients.php?mensaje=Cliente creado exitosamente&tipo_mensaje=success');
            exit();
        } else {
            $mensaje = 'Error al crear el cliente';
            $tipo_mensaje = 'error';
        }
    } elseif (isset($_POST['update_cliente'])) {
        $clienteData = [
            'id' => $_POST['client_id'],
            'name' => $_POST['nombre'],
            'lastname' => $_POST['apellido'],
            'email' => $_POST['email'],
            'phone' => $_POST['telefono']
        ];

        if (!empty($_POST['password'])) {
            $clienteData['password'] = $_POST['password'];
        }

        if (actualizarCliente($clienteData, $token)) {
            header('Location: clients.php?mensaje=Cliente actualizado exitosamente&tipo_mensaje=success');
            exit();
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
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" class="text-field" required>
                </div>

                <div class="form-field">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="text-field" required>
                </div>

                <div class="form-field">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" class="text-field" required>
                </div>

                <div class="form-field">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="text-field" placeholder="Dejar en blanco para no cambiar">
                </div>

                <button type="submit" name="create_cliente" id="submitBtn" class="button primary">
                    Agregar Cliente
                </button>
                <button type="button" onclick="limpiarFormulario()" class="button secondary" style="margin-left: 10px;">Cancelar</button>
            </form>

            <!-- Tabla de Clientes -->
            <?php if (!empty($clientes)): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Contraseña</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr data-id="<?= htmlspecialchars($cliente['id'] ?? '') ?>"
                                    data-nombre="<?= htmlspecialchars($cliente['name'] ?? '') ?>"
                                    data-apellido="<?= htmlspecialchars($cliente['lastname'] ?? '') ?>"
                                    data-email="<?= htmlspecialchars($cliente['email'] ?? '') ?>"
                                    data-telefono="<?= htmlspecialchars($cliente['phone'] ?? '') ?>"
                                    data-password="<?= htmlspecialchars($cliente['password'] ?? '') ?>">
                                    <td><?= htmlspecialchars($cliente['id'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($cliente['name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($cliente['lastname'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($cliente['email'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($cliente['phone'] ?? '') ?></td>
                                    <td>********</td>
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



    <script>
        function editCliente(row) {
            document.getElementById("client_id").value = row.dataset.id;
            document.getElementById("nombre").value = row.dataset.nombre;
            document.getElementById("apellido").value = row.dataset.apellido;
            document.getElementById("email").value = row.dataset.email;
            document.getElementById("telefono").value = row.dataset.telefono;
            document.getElementById("password").value = ''; // Password should not be pre-filled for security reasons
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
            document.getElementById("apellido").value = "";
            document.getElementById("email").value = "";
            document.getElementById("telefono").value = "";
            document.getElementById("password").value = "";
            document.getElementById("submitBtn").name = "create_cliente";
            document.getElementById("submitBtn").textContent = "Agregar Cliente";
        }



        function toggleCliente(row) {
            const cells = row.querySelectorAll('td:not(:last-child)');
            const toggleButton = row.querySelector('.toggle .material-icons');
            
            if (row.classList.contains('disabled')) {
                cells.forEach(cell => {
                    cell.style.textDecoration = 'none';
                    cell.style.color = 'inherit';
                });
                toggleButton.textContent = 'toggle_off';
                row.classList.remove('disabled');
            } else {
                cells.forEach(cell => {
                    cell.style.textDecoration = 'line-through';
                    cell.style.color = '#999';
                });
                toggleButton.textContent = 'toggle_on';
                row.classList.add('disabled');
            }
        }


    </script>
</body>

</html>