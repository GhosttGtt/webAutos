<?php
// Funciones API para clientes
function apiClientsList()
{
    $url = 'https://alexcg.de/autozone/api/clients.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Error en la API de clientes: ' . curl_error($curl));
        curl_close($curl);
        return [];
    }

    curl_close($curl);
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar JSON de la API de clientes: ' . json_last_error_msg());
        return [];
    }

    if (empty($data)) {
        error_log('La API de clientes devolvió un array vacío');
    }

    return $data;
}

function apiClientSingle($id)
{
    $url = 'https://alexcg.de/autozone/api/clients_single.php?id=' . $id;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Error en la API de cliente individual: ' . curl_error($curl));
        curl_close($curl);
        return null;
    }

    curl_close($curl);
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar JSON de la API de cliente individual: ' . json_last_error_msg());
        return null;
    }

    return $data;
}

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Crear cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_cliente'])) {
    $data = array(
        'name' => $_POST['nombre'],
        'email' => $_POST['email'],
        'phone' => $_POST['telefono']
    );

    $ch = curl_init('https://alexcg.de/autozone/api/create_client.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if ($result && isset($result['success'])) {
        $mensaje = 'Cliente creado exitosamente';
        $tipo_mensaje = 'success';
    } else {
        $mensaje = 'Error al crear el cliente';
        $tipo_mensaje = 'error';
    }
}

// Obtener lista de clientes
$clientes = apiClientsList();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            <!-- Formulario de Cliente con Material UI -->
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

            <!-- Tabla de Clientes con Material UI -->
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
                                <tr data-id="<?php echo htmlspecialchars($cliente['id']); ?>"
                                    data-nombre="<?php echo htmlspecialchars($cliente['name']); ?>"
                                    data-email="<?php echo htmlspecialchars($cliente['email']); ?>"
                                    data-telefono="<?php echo htmlspecialchars($cliente['phone']); ?>">
                                    <td><?php echo htmlspecialchars($cliente['id']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['name']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['phone']); ?></td>
                                    <td class="actions">
                                        <button onclick="editCliente(this.closest('tr'))" class="button edit">
                                            <span class="material-icons">edit</span>
                                        </button>
                                        <form method="POST" style="display: inline;"
                                            onsubmit="return confirm('¿Estás seguro de que deseas eliminar este cliente?')">
                                            <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($cliente['id']); ?>">
                                            <button type="submit" name="delete_cliente" class="button delete">
                                                <span class="material-icons">delete</span>
                                            </button>
                                        </form>
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
            document.getElementById("email").value = row.dataset.email;
            document.getElementById("telefono").value = row.dataset.telefono;
            document.getElementById("submitBtn").name = "update_cliente";
            document.getElementById("submitBtn").textContent = "Actualizar Cliente";
            document.getElementById("clientForm").scrollIntoView({
                behavior: "smooth"
            });
        }
    </script>
</body>

</html>