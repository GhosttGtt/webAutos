<?php

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
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

$token = obtenerToken();

function apiUsersList($token)
{
    if (!$token) return [];

    $url = 'https://alexcg.de/autozone/api/users.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true)['data'] ?? [];
}



// API: Crear usuario
function crearUsuario($data, $token)
{
    if (!$token) return false;

    $url = 'https://alexcg.de/autozone/api/users_create.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("cURL Error in actualizarUsuario: " . $curl_error);
        return false;
    }

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error in actualizarUsuario: " . json_last_error_msg() . " Response: " . $response);
        return false;
    }

    error_log("actualizarUsuario API Response (HTTP Code: " . $http_code . "): " . print_r($result, true));
    return $result['success'] ?? false;
}

// API: Actualizar usuario
function actualizarUsuario($data, $token)
{
    if (!$token) return false;

    $url = 'https://alexcg.de/autozone/api/user_edit.php';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("cURL Error in actualizarUsuario: " . $curl_error);
        return false;
    }

    $result = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON Decode Error in actualizarUsuario: " . json_last_error_msg() . " Response: " . $response);
        return false;
    }

    error_log("actualizarUsuario API Response (HTTP Code: " . $http_code . "): " . print_r($result, true));
    return $result['success'] ?? false;
}

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        // Actualizar usuario
        $userData = [
            'id' => $_POST['user_id'],
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'role' => $_POST['role']
        ];
        if (!empty($_POST['password'])) {
            $userData['password'] = $_POST['password'];
        }

        if (actualizarUsuario($userData, $token)) {
            $mensaje = 'Usuario actualizado exitosamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al actualizar el usuario';
            $tipo_mensaje = 'error';
        }
    } else {
        // Crear usuario
        $userData = [
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'role' => $_POST['role']
        ];

        if (crearUsuario($userData, $token)) {
            $mensaje = 'Usuario creado exitosamente';
            $tipo_mensaje = 'success';
        } else {
            $mensaje = 'Error al crear el usuario';
            $tipo_mensaje = 'error';
        }
    }
}

$usuarios = apiUsersList($token);
?>