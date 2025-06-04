<?php
// Obtener token de autenticación
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
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

$token = obtenerToken();

// Función: Lista de usuarios
function apiUsersList($token)
{
    if (!$token) return [];

    $url = 'https://alexcg.de/autozone/api/users.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true)['data'] ?? [];
}

// Variables de mensaje
$mensaje = '';
$tipo_mensaje = '';

// Obtener lista de usuarios
$usuarios = apiUsersList($token);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mui/material@5.14.0/dist/mui.min.css" />
    <link rel="stylesheet" href="assets/css/crud-styles.css">
</head>
<body>
<div class="container">
    <div class="paper">
        <h2 class="title">Gestión de Usuarios</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form id="userForm" method="POST" class="form">
            <input type="hidden" name="user_id" id="user_id">

            <div class="form-field">
                <label for="username">Nombre de Usuario</label>
                <input type="text" id="username" name="username" class="text-field" required>
            </div>

            <div class="form-field">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="text-field" required>
            </div>

            <div class="form-field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="text-field" required>
            </div>

            <div class="form-field">
                <label for="role">Rol</label>
                <select id="role" name="role" class="select-field" required>
                    <option value="">Seleccionar rol</option>
                    <option value="admin">Administrador</option>
                    <option value="user">Usuario</option>
                </select>
            </div>

            <button type="submit" name="create_user" id="submitBtn" class="button primary">
                Agregar Usuario
            </button>
        </form>

        <!-- Tabla de Usuarios -->
        <?php if (!empty($usuarios)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $user): ?>
                            <tr data-id="<?php echo htmlspecialchars($user['id']); ?>"
                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                data-role="<?php echo htmlspecialchars($user['role'] ?? ''); ?>">
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role'] ?? ''); ?></td>
                                <td class="actions">
                                    <button onclick="editUser(this.closest('tr'))" class="button edit">
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <form method="POST" style="display: inline;"
                                        onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                        <button type="submit" name="delete_user" class="button delete">
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
            <p class="no-data">No hay usuarios registrados.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function editUser(row) {
        document.getElementById("user_id").value = row.dataset.id;
        document.getElementById("username").value = row.dataset.username;
        document.getElementById("email").value = row.dataset.email;
        document.getElementById("role").value = row.dataset.role;
        document.getElementById("password").required = false;
        document.getElementById("submitBtn").name = "update_user";
        document.getElementById("submitBtn").textContent = "Actualizar Usuario";
        document.getElementById("userForm").scrollIntoView({ behavior: "smooth" });
    }
</script>
</body>
</html>
