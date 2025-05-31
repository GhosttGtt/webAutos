<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autozone - Citas</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
   <div style='margin-top: 100px;'> 
    <div class="section" style="height: 200px;"></div>
<div class="citas-wrapper">   
   <div class="form-container">
        <h2>Agendar Cita</h2>
        <p>Reserva una cita para conocer tu próximo auto</p>
        <input type="text" placeholder="Nombre" />
        <input type="email" placeholder="Email" />
        <input type="datetime-local" placeholder="Fecha y hora" />
        <input type="number" placeholder="Cantidad de personas" />
        <button onclick="mostrarMensaje('mensaje-cita')">Agendar tu cita</button>
        <div id="mensaje-cita" class="mensaje-enviado">¡Cita agendada con éxito!</div>
    </div>
    <div class="citas-image-container">
        <img src="img/car-2.png" alt="Auto" />
    </div>
</div>
<script>
function mostrarMensaje(id) {
    var mensaje = document.getElementById(id);
    mensaje.classList.add("mostrar");
    setTimeout(function() {
        mensaje.classList.remove("mostrar");
    }, 3000);
}
</script>
</body>
</html>