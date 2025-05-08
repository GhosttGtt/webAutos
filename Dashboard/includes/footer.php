<?php
// CRUD para clientes

// Conexión a la base de datos
include '../db/connection.php';

// Crear cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_cliente'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    $sql = "INSERT INTO clientes (nombre, email, telefono) VALUES ('$nombre', '$email', '$telefono')";
    $conn->query($sql);
}

// Leer clientes
$clientes = $conn->query("SELECT * FROM clientes");

// Actualizar cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cliente'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    $sql = "UPDATE clientes SET nombre='$nombre', email='$email', telefono='$telefono' WHERE id=$id";
    $conn->query($sql);
}

// Eliminar cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_cliente'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM clientes WHERE id=$id";
    $conn->query($sql);
}

// HTML para gestionar clientes
echo '<div class="clientes-container">';
echo '<h2>Gestión de Clientes</h2>';
echo '<form method="POST" class="clientes-form">';
echo '<input type="text" name="nombre" placeholder="Nombre" required>';
echo '<input type="email" name="email" placeholder="Email" required>';
echo '<input type="text" name="telefono" placeholder="Teléfono" required>';
echo '<button type="submit" name="create_cliente">Agregar Cliente</button>';
echo '</form>';

if ($clientes->num_rows > 0) {
    echo '<table class="clientes-table">';
    echo '<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Acciones</th></tr>';
    while ($row = $clientes->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['nombre'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td>' . $row['telefono'] . '</td>';
        echo '<td>';
        echo '<form method="POST" style="display:inline;">';
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<button type="submit" name="delete_cliente">Eliminar</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No hay clientes registrados.</p>';
}
echo '</div>';
?>