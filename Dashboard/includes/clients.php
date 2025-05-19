<?php
// Funciones API para clientes
function apiClientsList()
{
    $url = 'https://alexcg.de/autozone/api/clients.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Error en la API de clientes: ' . curl_error($curl));
        curl_close($curl);
        return [];
    }

    curl_close($curl);
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar JSON de la API de clientes: ' . json_last_error_msg());
        return [];
    }

    if (empty($data)) {
        error_log('La API de clientes devolvió un array vacío');
    }

    return $data;
}

function apiClientSingle($id)
{
    $url = 'https://alexcg.de/autozone/api/clients_single.php?id=' . $id;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response === false) {
        return null;
    }
    return json_decode($response, true);
}

// Manejo de operaciones CRUD a través de API
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_cliente'])) {
    $data = array(
        'name' => $_POST['nombre'],
        'email' => $_POST['email'],
        'phone' => $_POST['telefono']
    );

    $ch = curl_init('https://alexcg.de/autozone/api/create_client.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
}

// Obtener lista de clientes
$clientes = apiClientsList();
