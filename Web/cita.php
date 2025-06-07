<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Autozone - Citas</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="css/style.css" />
<style>
  #modalCita {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0; width: 100%; height: 100%;
    background-color: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
  }
  #modalCita .modal-content {
    background: white;
    padding: 25px 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    font-family: 'Roboto', sans-serif;
  }
  #modalCita .success {
    color: green;
    font-weight: 700;
    font-size: 1.2rem;
  }
  #modalCita .error {
    color: red;
    font-weight: 700;
    font-size: 1.2rem;
  }
  #modalCita button.close-btn {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    color: white;
    font-weight: 600;
    cursor: pointer;
  }
  #modalCita button.close-btn:hover {
    background-color: #0056b3;
  }
</style>
</head>
<body>

<div style='margin-top: 100px;'> 
    <div class="section" style="height: 200px;"></div>
    <div class="citas-wrapper">   
        <div class="form-container">
            <h2>Agendar Cita</h2>
            <p>Reserva una cita para conocer tu próximo auto</p>
            <form id="formCita">
                <input type="text" name="name" placeholder="Nombre" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="datetime-local" name="date" placeholder="Fecha y hora" required />
                <input type="number" name="people" placeholder="Cantidad de personas" required />
                <button type="submit">Agendar tu cita</button>
            </form>
        </div>
        <div class="citas-image-container">
            <img src="img/car-2.png" alt="Auto" />
        </div>
    </div>
</div>

<div id="modalCita">
  <div class="modal-content">
    <p id="mensajeModalCita"></p>
    <button class="close-btn" onclick="cerrarModalCita()">Cerrar</button>
  </div>
</div>

<script>
  const modalCita = document.getElementById('modalCita');
  const mensajeModalCita = document.getElementById('mensajeModalCita');
  const formCita = document.getElementById('formCita');

  formCita.addEventListener('submit', async function(event) {
    event.preventDefault(); // evitar recargar la página
    
    const formData = new FormData(formCita);
    const data = {
      name: formData.get('name'),
      email: formData.get('email'),
      date: formData.get('date'),
      people: formData.get('people')
    };

    try {
      const response = await fetch('https://alexcg.de/autozone/api/citas_create.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });

      if (response.ok) {
        mensajeModalCita.textContent = '¡Cita agendada con éxito!';
        mensajeModalCita.className = 'success';
        modalCita.style.display = 'flex';
        formCita.reset();
      } else {
        mensajeModalCita.textContent = 'Hubo un error al agendar la cita.';
        mensajeModalCita.className = 'error';
        modalCita.style.display = 'flex';
      }
    } catch (error) {
      mensajeModalCita.textContent = 'Error de red o servidor.';
      mensajeModalCita.className = 'error';
      modalCita.style.display = 'flex';
    }
  });

  function cerrarModalCita() {
    modalCita.style.display = 'none';
  }
</script>

</body>
</html>
