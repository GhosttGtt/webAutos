<?php
session_start();
$carrito = $_SESSION['carrito'] ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            margin-top: 50px;
        }
        table {
            margin-top: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h4><i class="material-icons left">shopping_cart</i>Datos del auto</h4>

    <?php if (count($carrito) === 0): ?>
        <p>Tu carrito está vacío.</p>
    <?php else: ?>
        <table class="highlight">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Modelo</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $auto): ?>
                    <tr>
                        <td><?= htmlspecialchars($auto['id']) ?></td>
                        <td><?= htmlspecialchars($auto['modelo']) ?></td>
                        <td>$<?= htmlspecialchars($auto['precio']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="index.php" class="btn blue">
        <i class="material-icons left">arrow_back</i>Volver al inicio
    </a>
</div>

</body>
</html>