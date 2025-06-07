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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['token'] ?? null;
}

$token = obtenerToken();


?>