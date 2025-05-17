<?php
$conexion = new mysqli("localhost", "root", "1234", "ventadeautos");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$marca = "Toyota";
$modelo = "Corolla";
$anio = 2020;
$precio = 25000;
$image = "uploads/carro1.png"; // Ruta relativa a la imagen
$tipo = 1;
$stock = 5;

$sql = "INSERT INTO cars (brand, model, year, price, image, type, stock)
        VALUES ('$marca', '$modelo', $anio, $precio, '$image', $tipo, $stock)";

if ($conexion->query($sql) === TRUE) {
    echo "Carro insertado con éxito.";
} else {
    echo "Error: " . $conexion->error;
}

$conexion->close();
?>