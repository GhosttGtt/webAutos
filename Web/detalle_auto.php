<?php
session_start();

// Verifica si se envió un comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario'])) {
    $comentario = trim($_POST['comentario']);
    $rating = intval($_POST['rating'] ?? 0);
    $car_id = $_GET['id'];
    $user_id = $_SESSION['user_id'] ?? '1'; // Default si no hay login

    $post_data = http_build_query([
        'car_id' => $car_id,
        'user_id' => $user_id,
        'rating' => $rating,
        'comment' => $comentario
    ]);

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded",
            'method'  => 'POST',
            'content' => $post_data
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents('https://www.alexcg.de/autozone/api/cars_comment_add.php', false, $context);

    if ($result !== false) {
        header("Location: detalle_auto.php?id=$car_id&mensaje=gracias");
        exit;
    }
}

// Inicializa el carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
    $carrito = &$_SESSION['carrito'];
    $car_id = $_GET['id'];

    if (!in_array($car_id, $carrito)) {
        $carrito[] = $car_id;
    }

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

// Obtener comentarios del auto
$comentarios = [];
$comentarios_url = "https://www.alexcg.de/autozone/api/cars_comment.php";
$comentarios_response = file_get_contents($comentarios_url);

if ($comentarios_response !== FALSE) {
    $comentarios_data = json_decode($comentarios_response, true);
    if ($comentarios_data && isset($comentarios_data['data'])) {
        foreach ($comentarios_data['data'] as $c) {
            if ($c['car_id'] == $car_id) {
                $comentarios[] = $c;
            }
        }
    }
}

$mensaje_exito = null;
if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'gracias') {
    $mensaje_exito = "¡Comentario enviado con éxito!";
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Auto</title>
    <style>
        body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background-color: #f4f6f8;
    color: #333;
}

.container {
    max-width: 1000px;
    margin: 40px auto;
    background-color: white;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-radius: 12px;
    padding: 40px;
}

.car-header {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    align-items: center;
}

.car-image {
    flex: 1;
    min-width: 280px;
}

.car-image img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.car-info {
    flex: 2;
}

.car-info h2 {
    margin-top: 0;
    font-size: 28px;
    color: #4a2b8c;
}

.car-info p {
    font-size: 17px;
    margin: 10px 0;
}

.highlight {
    background-color: #f0f0f0;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    display: inline-block;
}

.rating-only {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-top: 20px;
}

.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-start;
    gap: 5px;
    margin-bottom: 15px;
}

.rating input[type="radio"] {
    display: none;
}

.rating label {
    font-size: 30px;
    color: #ccc;
    cursor: pointer;
    transition: color 0.2s;
}

.rating input[type="radio"]:checked ~ label,
.rating label:hover,
.rating label:hover ~ label {
    color: #f1c40f;
}

textarea {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    resize: vertical;
    margin-top: 10px;
    transition: border 0.3s;
}

textarea:focus {
    border-color: #5e2c82;
    outline: none;
}

button {
    margin-top: 20px;
    background-color:rgb(125, 63, 169);
    color: white;
    border: none;
    padding: 12px 28px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #432162;
}

.back-btn {
    background-color: #7a7a7a;
    margin-top: 40px;
    display: inline-block;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 6px;
    color: white;
    transition: background-color 0.3s ease;
}

.back-btn:hover {
    background-color: #5a5a5a;
}

.comments-section {
    margin-top: 50px;
}

.comment-card {     
    background: #f9f9f9;
    padding: 15px 20px;
    border-left: 5px solidrgb(132, 69, 177);
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 1px 5px rgba(0,0,0,0.05);
}

.comment-card p {
    margin: 6px 0;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.4);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #d4edda;
    color: #155724;
    padding: 25px 35px;
    border-radius: 12px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    position: relative;
    animation: fadeIn 0.3s ease;
}

.modal .close {
    position: absolute;
    top: 10px;
    right: 16px;
    color: #155724;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
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
    <h3 style="color: #5e2c82;">Califica este auto y deja un comentario:</h3>

    <form method="POST" action="" style="margin-top: 10px;">

    <div class="rating-only">
    <div class="rating">
        <input type="radio" name="rating" id="star5" value="5"><label for="star5">&#9733;</label>
        <input type="radio" name="rating" id="star4" value="4"><label for="star4">&#9733;</label>
        <input type="radio" name="rating" id="star3" value="3"><label for="star3">&#9733;</label>
        <input type="radio" name="rating" id="star2" value="2"><label for="star2">&#9733;</label>
        <input type="radio" name="rating" id="star1" value="1"><label for="star1">&#9733;</label>
    </div>


    <textarea name="comentario" id="comentario" placeholder="Escribe tu opinión sobre este auto..." required></textarea>

    <button type="submit">Enviar</button>
</form>


    <?php if (!empty($comentarios)): ?>
    <div class="comments-section">
        <h3 style="color: #5e2c82;">Comentarios recientes:</h3>
        <?php foreach ($comentarios as $c): ?>
            <div class="comment-card"> <?php
                            for ($i = 1; $i <= $c['stars']; $i++) {
                                echo " <span style='color:#cdcd0d'>&#9733;</span>";
                            }
                            ?>
                            <?php

                            for ($i = 1; $i <= 5 - $c['stars']; $i++) {
                                echo "<span style='color:#cdcdcd'>&#9733;</span>";
                            }
                            ?>
                <p><strong>Usuario:</strong> <?= htmlspecialchars($c['client_name']) ?></p>
                <p><?= htmlspecialchars($c['comment']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

    <a href="index.php" class="back-btn">← Regresar</a>
</div>

<?php if ($mensaje_exito): ?>
<div id="modal-exito" class="modal">
  <div class="modal-content">
    <span class="close" onclick="cerrarModal()">&times;</span>
    <p><?= $mensaje_exito ?></p>
  </div>
</div>
<script>
  window.onload = () => {
    const modal = document.getElementById("modal-exito");
    if (modal) {
      modal.style.display = "flex";
    }
  };

  function cerrarModal() {
    const modal = document.getElementById("modal-exito");
    modal.style.display = "none";

    const url = new URL(window.location.href);
    url.searchParams.delete('mensaje');
    history.replaceState(null, '', url);
  }
</script>
<?php endif; ?>

</body>
</html>