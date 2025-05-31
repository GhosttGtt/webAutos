<?php
session_start();

// Inicializa el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verifica si se envió el formulario de compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
    $carrito = &$_SESSION['carrito'];
    $car_id = $_GET['id'];

    // Evita duplicados: solo añade si no está
    if (!in_array($car_id, $carrito)) {
        $carrito[] = $car_id;
    }

    // Redirige al inicio después de comprar
    header("Location: index.php");
    exit;
}

// Obtener detalles del auto
if (!isset($_GET['id'])) {
    echo "<p>Auto no especificado.</p>";
    exit;
}

$car_id = $_GET['id'];
$api_url = "https://alexcg.de/autozone/api/cars.php";
$response = file_get_contents($api_url);
$car_data = null;

if ($response !== FALSE) {
    $json_data = json_decode($response, true);
    if ($json_data && isset($json_data['data'])) {
        foreach ($json_data['data'] as $car) {
            if ($car['id'] == $car_id) {
                $car_data = $car;
                break;
            }
        }
    }
}

if (!$car_data) {
    echo "<p>No se encontró la información del auto.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Auto</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f8f8f8;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 40px;
        }

        .car-header {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .car-image {
            flex: 1;
        }

        .car-image img {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .car-info {
            flex: 1.5;
        }

        .car-info h2 {
            margin-top: 0;
            color: #5e2c82;
        }

        .car-info p {
            font-size: 17px;
            margin: 8px 0;
        }

        .highlight {
            background-color: #f0f0f0;
            padding: 8px;
            border-radius: 8px;
            font-weight: bold;
        }

        .rating {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .rating input[type="radio"] {
            display: none;
        }

        .rating label {
            font-size: 32px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.3s;
        }

        .rating input[type="radio"]:checked ~ label {
            color: #f1c40f;
        }

        .rating label:hover,
        .rating label:hover ~ label {
            color: #f1c40f;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-top: 10px;
            font-size: 15px;
            resize: vertical;
        }

        button {
            margin-top: 20px;
            background-color: #5e2c82;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #432162;
        }

        .buy-button {
            background-color: #27ae60;
        }

        .buy-button:hover {
            background-color: #1f8a4c;
        }

        .back-btn {
            background-color: #aaa;
            margin-top: 40px;
            display: inline-block;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #888;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="car-header">
        <div class="car-image">
            <img src="<?= $car_data['image'] ?>" alt="Imagen del auto">
        </div>
        <div class="car-info">
            <h2><?= $car_data['brand'] . " " . $car_data['model'] ?> (<?= $car_data['year'] ?>)</h2>
            <p><span class="highlight">Tipo:</span> <?= $car_data['type_name'] ?></p>
            <p><span class="highlight">Descripción:</span> <?= $car_data['description'] ?></p>
            <p><span class="highlight">Precio:</span> Q<?= number_format($car_data['price'], 2) ?></p>
        </div>
    </div>

    <form method="POST" action="">
        <h3 style="margin-top: 40px; color: #5e2c82;">Califica este auto:</h3>
        <div class="rating">
            <input type="radio" name="rating" id="star5" value="5"><label for="star5">&#9733;</label>
            <input type="radio" name="rating" id="star4" value="4"><label for="star4">&#9733;</label>
            <input type="radio" name="rating" id="star3" value="3"><label for="star3">&#9733;</label>
            <input type="radio" name="rating" id="star2" value="2"><label for="star2">&#9733;</label>
            <input type="radio" name="rating" id="star1" value="1"><label for="star1">&#9733;</label>
        </div>

        <label for="comentario">Deja tu comentario:</label>
        <textarea name="comentario" id="comentario" rows="4" placeholder="Escribe tu opinión sobre este auto..."></textarea>

        <button type="submit">Enviar</button>
    </form>

    <form method="GET" action="cita.php" style="margin-top: 20px;">
    <input type="hidden" name="id" value="<?= $car['id'] ?>">
    <button type="submit" class="buy-button">👨‍💻 Agendar Cita</button>
    </form>

    <a href="index.php" class="back-btn">← Regresar</a>
</div>

</body>
</html>