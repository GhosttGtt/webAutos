<?php
header('Content-Type: application/json');

function obtenerToken()
{
    $url = 'https://alexcg.de/autozone/api/login.php';
    $credentials = [
        'username' => 'ghost',
        'password' => '12345'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

$token = obtenerToken();
if (!$token) {
    echo json_encode(['error' => 'No se pudo obtener token']);
    exit;
}

// Capturar datos POST
$id = $_POST['id'] ?? null;
$username = $_POST['username'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$role = $_POST['role'] ?? null;

if (!$id || !$username || !$email || !$role) {
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

$datos = compact('id', 'username', 'email', 'role');
if ($password) $datos['password'] = $password;

// Enviar a la API real
$ch = curl_init('https://alexcg.de/autozone/api/user_edit.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

// Enviar respuesta al frontend
echo $response;
