<?php
session_start();

function registrarUsuario($data)
{
    $url = 'https://alexcg.de/autozone/api/user_create.php';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos requeridos estén presentes
    if (empty($_POST['name']) || empty($_POST['username']) || 
        empty($_POST['email']) || empty($_POST['password']) || 
        !isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = 'Todos los campos son obligatorios, incluyendo la foto de perfil';
        header('Location: register.php');
        exit();
    }

    // Procesar la foto
    $photo = $_FILES['photo'];
    $photoData = base64_encode(file_get_contents($photo['tmp_name']));

    // Preparar datos para la API
    $userData = [
        'name' => $_POST['name'],
        'username' => $_POST['username'],
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'photo' => $photoData
    ];

    // Intentar registrar el usuario
    $result = registrarUsuario($userData);

    if (isset($result['success']) && $result['success'] === true) {
        $_SESSION['success'] = 'Usuario registrado exitosamente';
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = $result['message'] ?? 'Error al registrar el usuario';
        header('Location: register.php');
        exit();
    }
} else {
    header('Location: register.php');
    exit();
}
