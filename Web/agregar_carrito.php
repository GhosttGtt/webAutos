agregar_carrito.php
<?php
session_start();

// Verificamos si se pasó el ID del auto
if (!isset($_GET['id'])) {
    echo "Auto no especificado.";
    exit;
}

$car_id = $_GET['id'];
$api_url = "https://alexcg.de/autozone/api/cars.php";
$response = file_get_contents($api_url);

if ($response === FALSE) {
    echo "No se pudo obtener información del auto.";
    exit;
}

$json_data = json_decode($response, true);
$car_data = null;

if ($json_data && isset($json_data['data'])) {
    foreach ($json_data['data'] as $car) {
        if ($car['id'] == $car_id) {
            $car_data = $car;
            break;
        }
    }
}

// Si encontramos el auto, lo agregamos al carrito
if ($car_data) {
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Puedes evitar duplicados verificando si ya existe el ID
    $ya_existe = false;
    foreach ($_SESSION['carrito'] as $item) {
        if ($item['id'] == $car_data['id']) {
            $ya_existe = true;
            break;
        }
    }

    if (!$ya_existe) {
        $_SESSION['carrito'][] = [
            'id' => $car_data['id'],
            'modelo' => $car_data['modelo'],
            'precio' => $car_data['precio']
        ];
    }

    // Redirige al carrito
    header("Location: carrito.php");
    exit;
} else {
    echo "Auto no encontrado.";
}