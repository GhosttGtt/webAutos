<?php
// Funciones API para mensajes
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

function updateMessageStatus($id, $status)
{
    $data = array(
        'id' => $id,
        'status' => $status
    );

    $ch = curl_init('https://alexcg.de/autozone/api/update_message_status.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Obtener lista de mensajes
$mensajes = apiMessagesList();

// Actualizar estado del mensaje si se hace clic
if (isset($_POST['message_id'])) {
    $messageId = $_POST['message_id'];
    updateMessageStatus($messageId, 1);
    // Recargar la lista de mensajes
    $mensajes = apiMessagesList();
}

// HTML para mostrar mensajes
echo '<div class="mensajes-container">';
echo '<h2>Mensajes</h2>';

if (!empty($mensajes)) {
    echo '<div class="mensajes-grid">';
    foreach ($mensajes as $mensaje) {
        $statusClass = $mensaje['status'] == 0 ? 'no-leido' : 'leido';
        echo '<div class="mensaje-card ' . $statusClass . '">';
        echo '<div class="mensaje-header">';
        echo '<span class="fecha">' . date('d/m/Y H:i', strtotime($mensaje['date'])) . '</span>';
        echo '<span class="estado">' . ($mensaje['status'] == 0 ? 'No leído' : 'Leído') . '</span>';
        echo '</div>';
        echo '<div class="mensaje-contenido">';
        echo '<h3>' . htmlspecialchars($mensaje['subject']) . '</h3>';
        echo '<p>' . htmlspecialchars($mensaje['message']) . '</p>';
        echo '</div>';
        if ($mensaje['status'] == 0) {
            echo '<form method="POST" class="mensaje-action">';
            echo '<input type="hidden" name="message_id" value="' . $mensaje['id'] . '">';
            echo '<button type="submit" class="btn-marcar-leido">Marcar como leído</button>';
            echo '</form>';
        }
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>No hay mensajes disponibles.</p>';
}
echo '</div>';

// Estilos CSS
echo '<style>
.mensajes-container {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.mensajes-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

.mensaje-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    background: #f9f9f9;
    transition: all 0.3s ease;
}

.mensaje-card.no-leido {
    background: #fff;
    border-left: 4px solid #2196F3;
}

.mensaje-card.leido {
    opacity: 0.8;
}

.mensaje-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #666;
}

.mensaje-contenido h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.mensaje-contenido p {
    margin: 0;
    color: #666;
    line-height: 1.5;
}

.btn-marcar-leido {
    background: #2196F3;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 15px;
    width: 100%;
}

.btn-marcar-leido:hover {
    background: #1976D2;
}

.estado {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
}

.no-leido .estado {
    background: #e3f2fd;
    color: #2196F3;
}

.leido .estado {
    background: #f5f5f5;
    color: #666;
}
</style>';
