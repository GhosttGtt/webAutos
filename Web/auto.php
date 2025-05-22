<?php                
    $api_url = "https://alexcg.de/autozone/api/cars.php";
$response = file_get_contents($api_url);
if ($response === FALSE) {

    echo "<p>Error al conectarse a la API.</p>";
} else {
    $json_data = json_decode($response, true);

    if ($json_data && isset($json_data['data'])) {
        $cars = $json_data['data'];

        // ACA MUESSTRA POR FILTRO LOS AUTOS
        function mostrarCarrosPorTipo($cars, $tipo, $max = 6) {
            $filtrados = [];

            foreach ($cars as $car) {
                if ($car['type_name'] === $tipo) {
                    $filtrados[] = $car;
                    if (count($filtrados) === $max) break;
                }
            }

            if (count($filtrados) > 0) {
       
                // SUBTITULOS
              $subtitulo = "";

              switch ($tipo) {
                case "Sedán":
                      $subtitulo = "Comodidad y Economía";                                       
                      break;
                       case "Pickup":
                      $subtitulo = "Carga y Resistencia";
                      break;
                  case "SUV":
                      $subtitulo = "Espacio y Seguridad";
                      break;
                  case "Hatchback":
                      $subtitulo = "Compacto, moderno y eficiente";
                      break;              
                  default:
                      $subtitulo = "Descubre tu próximo auto ideal";
              }          

                echo "<div class='tipo-auto' style='margin-top: 230px;'> 
                  <h4 class='tipo'>$tipo</h4>
                  <p class='subtitulo' >$subtitulo</p>
                </div>";

                
                // Fila 1
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
                                <p><strong>Año:</strong> {$car['year']}</p>
                                <p><strong>Descripción:</strong> {$car['description']}</p>
                                <p><strong>Precio:</strong> Q" . number_format($car['price'], 2) . "</p>
                                <button class='button-masInfo');\">Ver más</button>

                            </div>
                        </div>
                    </div>";
                }
                echo "</div>";

                // Fila 2
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
                                <p><strong>Año:</strong> {$car['year']}</p>
                                <p><strong>Precio:</strong> Q" . number_format($car['price'], 2) . "</p>
                                 <button class='button-masInfo');\">Ver más</button>
                            </div>
                        </div>
                    </div>";
                }
                echo "</div>";
            } else {
                echo "<p>No se encontraron autos del tipo $tipo.</p>";
            }
        }

        //LLAMADAS PARA CADA TIPO DE AUTO
        mostrarCarrosPorTipo($cars, "Sedán");
             mostrarCarrosPorTipo($cars, "Pickup");
        mostrarCarrosPorTipo($cars, "SUV");
        mostrarCarrosPorTipo($cars, "Hatchback");      

    } else {
        echo "<p>Respuesta inválida desde la API.</p>";
    }
}
?> 