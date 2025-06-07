<?php
session_start();
require_once 'api_functions.php';

$mensaje = $_GET['mensaje'] ?? '';
$tipo_mensaje = $_GET['tipo_mensaje'] ?? '';
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
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

        <form id="userForm" method="POST" class="form">
            <input type="hidden" id="user_id" name="user_id">
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
                <input type="password" id="password" name="password" class="text-field">
            </div>
            <div class="form-field">
                <label for="role">Rol</label>
                <select id="role" name="role" class="select-field" required>
                    <option value="">Seleccionar rol</option>
                    <option value="admin">Administrador</option>
                    <option value="user">Usuario</option>
                </select>
            </div>
            <button type="submit" id="submitBtn" class="button primary">Agregar Usuario</button>
            <button type="button" onclick="clearForm()" class="button secondary" style="margin-left: 10px;">Cancelar</button>
        </form>

         <?php if (!empty($usuarios)): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                     <?php foreach ($usuarios as $user): ?>
                        <tr data-id="<?= $user['id'] ?>" data-username="<?= $user['username'] ?>" data-email="<?= $user['email'] ?>" data-role="<?= $user['role'] ?>">
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['role'] ?></td>
                            <td>
                                <button onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)" class="button edit">Editar</button>
                            </td>
                        </tr>
                     <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
         <?php else: ?>
            <p>No hay usuarios registrados.</p>
      <?php endif; ?>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('user_id').value = user.id;
    document.getElementById('username').value = user.username;
    document.getElementById('email').value = user.email;
    document.getElementById('role').value = user.role;
    document.getElementById('password').value = ''; 
    document.getElementById('submitBtn').innerText = 'Actualizar Usuario';
    document.getElementById('userForm').action = ''; 
}

function clearForm() {
    document.getElementById('user_id').value = '';
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('role').value = '';
    document.getElementById('submitBtn').innerText = 'Agregar Usuario';
    document.getElementById('userForm').action = ''; 
}
</script>
</body>
</html>
