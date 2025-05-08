<?php
// CRUD para productos (autos)

// Conexión a la base de datos
include '../db/connection.php';

// Crear producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_producto'])) {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $anio = $_POST['anio'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];

    $sql = "INSERT INTO productos (marca, modelo, anio, categoria, precio) VALUES ('$marca', '$modelo', '$anio', '$categoria', '$precio')";
    $conn->query($sql);
}

// Leer productos
$productos = $conn->query("SELECT * FROM productos");

// Actualizar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_producto'])) {
    $id = $_POST['id'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $anio = $_POST['anio'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];

    $sql = "UPDATE productos SET marca='$marca', modelo='$modelo', anio='$anio', categoria='$categoria', precio='$precio' WHERE id=$id";
    $conn->query($sql);
}

// Eliminar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_producto'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM productos WHERE id=$id";
    $conn->query($sql);
}

// HTML para gestionar productos
echo '<div class="productos-container">';
echo '<h2>Gestión de Autos</h2>';
echo '<form method="POST" class="productos-form">';
echo '<input type="text" name="marca" placeholder="Marca" required>';
echo '<input type="text" name="modelo" placeholder="Modelo" required>';
echo '<input type="number" name="anio" placeholder="Año" required>';
echo '<input type="text" name="categoria" placeholder="Categoría" required>';
echo '<input type="number" name="precio" placeholder="Precio" required>';
echo '<button type="submit" name="create_producto">Agregar Auto</button>';
echo '</form>';

if ($productos->num_rows > 0) {
    echo '<table class="productos-table">';
    echo '<tr><th>ID</th><th>Marca</th><th>Modelo</th><th>Año</th><th>Categoría</th><th>Precio</th><th>Acciones</th></tr>';
    while ($row = $productos->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['marca'] . '</td>';
        echo '<td>' . $row['modelo'] . '</td>';
        echo '<td>' . $row['anio'] . '</td>';
        echo '<td>' . $row['categoria'] . '</td>';
        echo '<td>' . $row['precio'] . '</td>';
        echo '<td>';
        echo '<form method="POST" style="display:inline;">';
        echo '<input type="hidden" name="id" value="' . $row['id'] . '">';
        echo '<button type="submit" name="delete_producto">Eliminar</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No hay autos registrados.</p>';
}
echo '</div>';
?>