<?php
function obtenerToken() {
    $url = 'https://alexcg.de/autozone/api/login.php';
    $credentials = [
        'username' => 'ghost',
        'password' => '12345'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

function obtenerVentas($token) {
    $url = 'https://alexcg.de/autozone/api/sales.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$token = obtenerToken();
$ventasData = obtenerVentas($token);
$ventas = $ventasData['data'] ?? [];

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ventas - Prueba</title>
</head>
<body>
    <h2>Lista de Ventas</h2>
    <?php if (!empty($ventas)): ?>
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Vehículo</th>
                    <th>Año</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th>Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta['client_name'] . ' ' . $venta['client_lastname']) ?></td>
                        <td><?= htmlspecialchars($venta['cars_name'] . ' ' . $venta['cars_model']) ?></td>
                        <td><?= htmlspecialchars($venta['cars_year']) ?></td>
                        <td>$<?= number_format($venta['cars_price'], 2) ?></td>
                        <td>$<?= number_format($venta['total'], 2) ?></td>
                        <td><?= htmlspecialchars($venta['payment']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron ventas.</p>
    <?php endif; ?>
</body>
</html>
