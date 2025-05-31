<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$api_url = "https://alexcg.de/autozone/api/cars.php";
$response = file_get_contents($api_url);

if ($response === FALSE) {
    echo "<p>Error al conectarse a la API.</p>";
} else {
    $json_data = json_decode($response, true);

    if ($json_data && isset($json_data['data'])) {
        $cars = $json_data['data'];

        // FUNCIÓN PARA MOSTRAR AUTOS POR TIPO
        function mostrarCarrosPorTipo($cars, $tipo, $max = 6) {
            $filtrados = [];

            foreach ($cars as $car) {
                if ($car['type_name'] === $tipo) {
                    $filtrados[] = $car;
                    if (count($filtrados) === $max) break;
                }
            }

            if (count($filtrados) > 0) {
                // SUBTITULOS SEGUN TIPO
                $subtitulo = match ($tipo) {
                    "Sedán" => "Comodidad y Economía",
                    "Pickup" => "Carga y Resistencia",
                    "SUV" => "Espacio y Seguridad",
                    "Hatchback" => "Compacto, moderno y eficiente",
                    default => "Descubre tu próximo auto ideal",
                };

                echo "<div class='tipo-auto' style='margin-top: 230px;'> 
                        <h4 class='tipo'>$tipo</h4>
                        <p class='subtitulo'>$subtitulo</p>
                    </div>";

                // ACA SE PRUEBA SI LA PERSONA ESTA LOGUEADA
                $logueado = isset($_SESSION['username']);

                // FILA CARRO 1
                echo "<div class='row'>";
                for ($i = 0; $i < 3 && $i < count($filtrados); $i++) {
                    $car = $filtrados[$i];
                    echo "
                    <div class='col s12 m4'>
                        <div class='card'>
                            <div class='card-image'>
                                <img src='{$car['image']}' alt='Carro'>
                            </div>
                            <div class='card-content'>
                                <p><strong>Marca:</strong> {$car['brand']}</p>
                                <p><strong>Modelo:</strong> {$car['model']}</p>
                                <p><strong>Año:</strong> {$car['year']}</p>";
                                
                                if ($logueado) {
                                    echo "<a href='detalle_auto.php?id={$car['id']}' class='button-masInfo'>Ver más</a>";
                                } else {
                                    echo "<a href='iniciar_sesion.php' class='button-masInfo' title='Debes iniciar sesión primero'>Ver más</a>";
                                }

                    echo "  </div>
                        </div>
                    </div>";
                }
                echo "</div>";

                // FILA CARRO 2
                echo "<div class='row'>";
                for ($i = 3; $i < 6 && $i < count($filtrados); $i++) {
                    $car = $filtrados[$i];
                    echo "
                    <div class='col s12 m4'>
                        <div class='card'>
                            <div class='card-image'>
                                <img src='{$car['image']}' alt='Carro'>
                            </div>
                            <div class='card-content'>
                                <p><strong>Marca:</strong> {$car['brand']}</p>
                                <p><strong>Modelo:</strong> {$car['model']}</p>
                                <p><strong>Año:</strong> {$car['year']}</p>";

                                if ($logueado) {
                                    echo "<a href='detalle_auto.php?id={$car['id']}' class='button-masInfo'>Ver más</a>";
                                } else {
                                    echo "<a href='iniciar_sesion.php' class='button-masInfo' title='Debes iniciar sesión primero'>Ver más</a>";
                                }

                    echo "  </div>
                        </div>
                    </div>";
                }
                echo "</div>";
            } else {
                echo "<p>No se encontraron autos del tipo $tipo.</p>";
            }
        }

        // MOSTRAR CATEGORÍAS
        mostrarCarrosPorTipo($cars, "Sedán");
        mostrarCarrosPorTipo($cars, "Pickup");
        mostrarCarrosPorTipo($cars, "SUV");
        mostrarCarrosPorTipo($cars, "Hatchback");
    } else {
        echo "<p>Respuesta inválida desde la API.</p>";
    }
}
?>
