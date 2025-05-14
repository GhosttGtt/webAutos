<?php
// Consulta para obtener todos los productos (autos)
$result_productos = $conn->query("SELECT id, brand, model, price, stock FROM cars");

if ($result_productos->num_rows > 0) {
    echo "<h2>Productos en Stock</h2><ul>";
    while ($row = $result_productos->fetch_assoc()) {
        // Mostrar cada producto con su marca, modelo, precio y stock
        echo "<li>" . $row['brand'] . " " . $row['model'] . " - Precio: Q" . number_format($row['price'], 2) . " - Stock: " . $row['stock'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "No hay productos en stock.";
}
?>
