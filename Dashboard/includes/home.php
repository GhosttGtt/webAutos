<?php
// Funciones API necesarias
function apiMessagesList()
{
    $url = 'https://alexcg.de/autozone/api/messages.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Error en la API de mensajes: ' . curl_error($curl));
        curl_close($curl);
        return [];
    }

    curl_close($curl);
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar JSON de la API de mensajes: ' . json_last_error_msg());
        return [];
    }

    if (empty($data)) {
        error_log('La API de mensajes devolvió un array vacío');
    }

    return $data;
}

function apiSalesList()
{
    $url = 'https://alexcg.de/autozone/api/sales.php';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    if ($response === false) {
        return [];
    }
    return json_decode($response, true);
}

// Obtener datos
$mensajes = apiMessagesList();
$ventas = apiSalesList();

// Calcular estadísticas
$mensajesNoLeidos = 0;
foreach ($mensajes as $mensaje) {
    if ($mensaje['status'] == 0) {
        $mensajesNoLeidos++;
    }
}

// Calcular el auto más vendido
$ventasPorModelo = array();
foreach ($ventas as $venta) {
    $modelo = $venta['model'];
    if (!isset($ventasPorModelo[$modelo])) {
        $ventasPorModelo[$modelo] = 0;
    }
    $ventasPorModelo[$modelo]++;
}

arsort($ventasPorModelo);
$modeloMasVendido = key($ventasPorModelo);
$cantidadVentas = current($ventasPorModelo);

// HTML para mostrar estadísticas
echo '<div class="dashboard-container">';

// Widget de mensajes no leídos
echo '<div class="widget mensajes-widget">';
echo '<div class="widget-icon"><i class="fas fa-envelope"></i></div>';
echo '<div class="widget-content">';
echo '<h3>Mensajes no leídos</h3>';
echo '<div class="widget-number">' . $mensajesNoLeidos . '</div>';
echo '</div>';
echo '</div>';

// Widget de auto más vendido
echo '<div class="widget ventas-widget">';
echo '<div class="widget-icon"><i class="fas fa-car"></i></div>';
echo '<div class="widget-content">';
echo '<h3>Auto más vendido</h3>';
echo '<div class="widget-info">';
echo '<p class="modelo">' . htmlspecialchars($modeloMasVendido) . '</p>';
echo '<p class="cantidad">' . $cantidadVentas . ' unidades vendidas</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Estilos CSS
echo '<style>
.dashboard-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.widget {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.widget:hover {
    transform: translateY(-5px);
}

.widget-icon {
    background: #f8f9fa;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
}

.widget-icon i {
    font-size: 24px;
    color: #4CAF50;
}

.mensajes-widget .widget-icon i {
    color: #2196F3;
}

.ventas-widget .widget-icon i {
    color: #4CAF50;
}

.widget-content {
    flex-grow: 1;
}

.widget-content h3 {
    margin: 0;
    font-size: 1.1em;
    color: #666;
}

.widget-number {
    font-size: 2em;
    font-weight: bold;
    color: #2196F3;
    margin-top: 5px;
}

.widget-info .modelo {
    font-size: 1.2em;
    font-weight: bold;
    color: #4CAF50;
    margin: 5px 0;
}

.widget-info .cantidad {
    font-size: 0.9em;
    color: #666;
    margin: 0;
}
</style>';
