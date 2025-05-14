<?php

// Conexión a la base de datos local @Isma
$servername = "localhost";
$user = 'root'; 
$password = ''; 
$dbname = 'autozone'; 

$conn = new mysqli('localhost', 'root', '', 'autozone');
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


?>