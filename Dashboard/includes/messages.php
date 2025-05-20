<?php
// Funciones API para mensajes @Ismael
function apiMessagesList()
{
    $url = 'https://alexcg.de/autozone/api/messages.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response === false) {
        return [];
    }
    return json_decode($response, true);
}

// Obtener lista de mensajes 
$mensajes = apiMessagesList();
if (isset($_POST['message_id'])) {
    $messageId = $_POST['message_id'];
    $mensajes = apiMessagesList();
}
