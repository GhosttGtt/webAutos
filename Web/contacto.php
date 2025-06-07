<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Autozone - Formularios</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />

  <style>
    #myModal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0; right: 0; bottom: 0;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    #myModal.show {
      display: flex;
    }
    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 6px;
      max-width: 400px;
      width: 90%;
      text-align: center;
    }
    .success {
      color: green;
    }
    .error {
      color: red;
    }
    .close-btn {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #007bff;
      border: none;
      border-radius: 4px;
      color: white;
      font-weight: 600;
      cursor: pointer;
    }
    .close-btn:hover {
      background: #555;
    }
  </style>
</head>
<body>

<div class="formulario-container">
  <div class="image-container">
    <img src="img/car-1.png" alt="Auto" />
  </div>
  <div class="form-container">
    <h2>Contáctanos</h2>
    <p>Estamos aquí para responder tus preguntas</p>
    <form id="contactForm" novalidate>
      <input type="text" name="name" placeholder="Nombre completo" required />
      <input type="tel" name="phone" placeholder="Teléfono" required />
      <input type="email" name="email" placeholder="Correo electrónico" required />
      <input type="text" name="subject" placeholder="Asunto" required />
      <textarea name="message" placeholder="Mensaje" rows="5" required></textarea>
      <button type="submit" id="sendBtn">Enviar mensaje</button>
    </form>
  </div>
</div>

<div id="myModal">
  <div class="modal-content">
    <p id="modalText"></p>
    <button id="modalCloseBtn" onClick="window.location.reload()" class="close-btn">Cerrar</button>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById('contactForm');
    /*const modal = document.getElementById('myModal');
    const modalText = document.getElementById('modalText');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const sendBtn = document.getElementById('sendBtn');*/

    let isSubmitting = false;

    form.onsubmit = null;

    form.addEventListener('submit', async function handleSubmit(e) {
      e.preventDefault();

      if (isSubmitting) {
        console.warn("❌ Envío duplicado bloqueado.");
        return;
      }

      isSubmitting = true;
      sendBtn.disabled = true;

      const name = form.name.value.trim();
      const phone = form.phone.value.trim();
      const email = form.email.value.trim();
      const subject = form.subject.value.trim();
      const message = form.message.value.trim();

      if (!name || !phone || !email || !subject || !message) {
        modalText.textContent = 'Por favor, complete todos los campos.';
        modalText.className = 'error';
        modal.classList.add('show');
        sendBtn.disabled = false;
        isSubmitting = false;
        return;
      }

      const data = { name, phone, email, subject, message, status: "0" };

      try {
        const response = await fetch('https://alexcg.de/autozone/api/message_send.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });

       if (response.ok) {
          modalText.textContent = '¡Mensaje enviado con éxito!';
          modalText.className = 'success';
          form.reset();
        } else {
          modalText.textContent = 'Error al enviar el mensaje.';
          modalText.className = 'error';
        }

       modal.classList.add('show');

       } catch (error) {
        console.error("❌ Error de red:", error);
        modalText.textContent = 'Error de red o servidor.';
        modalText.className = 'error';
        modal.classList.add('show');
      }

      isSubmitting = false;
     /* sendBtn.disabled = false;*/
    });

    modalCloseBtn.addEventListener('click', () => {
      modal.classList.remove('show');
    });
  });
</script>

</body>
</html>
