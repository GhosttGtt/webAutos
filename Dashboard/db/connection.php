<?php

// Conexión a la base de datos local @Isma
$host = 'localhost';
$user = 'root'; 
$password = ''; 
$dbname = 'webAutos'; 

$conn = new mysqli('localhost', 'root', '', 'webAutos');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


?>