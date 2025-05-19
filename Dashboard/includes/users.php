<?php
// Funciones API para usuarios
function apiUsersList()
{
    $url = 'https://alexcg.de/autozone/api/users.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response === false) {
        return [];
    }
    return json_decode($response, true);
}

function apiUserSingle($id)
{
    $url = 'https://alexcg.de/autozone/api/users_single.php?id=' . $id;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response === false) {
        return null;
    }
    return json_decode($response, true);
}

// Manejo de operaciones CRUD a través de API
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $data = array(
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'role' => $_POST['role']
    );

    $ch = curl_init('https://alexcg.de/autozone/api/create_user.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
}

// Obtener lista de usuarios
$usuarios = apiUsersList();

// HTML para gestionar usuarios
echo '<div class="usuarios-container">';
echo '<h2>Gestión de Usuarios</h2>';
echo '<form method="POST" class="usuarios-form">';
echo '<input type="text" name="username" placeholder="Nombre de usuario" required>';
echo '<input type="email" name="email" placeholder="Correo electrónico" required>';
echo '<input type="password" name="password" placeholder="Contraseña" required>';
echo '<select name="role" required>';
echo '<option value="">Seleccionar rol</option>';
echo '<option value="admin">Administrador</option>';
echo '<option value="user">Usuario</option>';
echo '</select>';
echo '<button type="submit" name="create_user" class="btn-primary">Agregar Usuario</button>';
echo '</form>';

if (!empty($usuarios)) {
    echo '<table class="usuarios-table">';
    echo '<tr><th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Acciones</th></tr>';
    foreach ($usuarios as $user) {
        echo '<tr>';
        echo '<td>' . $user['id'] . '</td>';
        echo '<td>' . $user['username'] . '</td>';
        echo '<td>' . $user['email'] . '</td>';
        echo '<td>' . $user['role'] . '</td>';
        echo '<td class="action-buttons">';
        echo '<button class="btn-edit">Editar</button>';
        echo '<button class="btn-delete">Eliminar</button>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No hay usuarios registrados.</p>';
}
echo '</div>';

// Estilos CSS
echo '<style>
.usuarios-container {
    padding: 2rem;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.usuarios-container h2 {
    color: #333;
    font-size: 1.8rem;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.usuarios-container h2::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: #8400ff;
    border-radius: 3px;
}

.usuarios-form {
    display: grid;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
    max-width: 600px;
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.usuarios-form input,
.usuarios-form select {
    padding: 0.8rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.usuarios-form input:focus,
.usuarios-form select:focus {
    border-color: #8400ff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(132, 0, 255, 0.1);
}

.usuarios-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 2rem;
    overflow: hidden;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.usuarios-table th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.usuarios-table th,
.usuarios-table td {
    padding: 1rem 1.5rem;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.usuarios-table tr:last-child td {
    border-bottom: none;
}

.usuarios-table tr:hover {
    background-color: #f8f9fa;
}

.action-buttons {
    display: flex;
    gap: 0.8rem;
}

.btn-primary {
    background: #8400ff;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

.btn-primary:hover {
    background: #6a00cc;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(132, 0, 255, 0.2);
}

.btn-edit,
.btn-delete {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.btn-edit {
    background: #e3f2fd;
    color: #1976D2;
    border: 1px solid #90caf9;
}

.btn-edit:hover {
    background: #1976D2;
    color: white;
    border-color: #1976D2;
}

.btn-delete {
    background: #ffebee;
    color: #d32f2f;
    border: 1px solid #ef9a9a;
}

.btn-delete:hover {
    background: #d32f2f;
    color: white;
    border-color: #d32f2f;
}

@media (max-width: 768px) {
    .usuarios-container {
        padding: 1rem;
    }

    .usuarios-form {
        padding: 1.5rem;
    }

    .usuarios-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    .usuarios-table th,
    .usuarios-table td {
        padding: 0.8rem 1rem;
    }

    .action-buttons {
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-edit,
    .btn-delete {
        width: 100%;
        text-align: center;
    }
}
</style>';
