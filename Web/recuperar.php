<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Recuperar contraseña - Autozone</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: #f5f5f5;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .recovery-container {
      background: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .logo-background {
      background-color: rgb(161, 73, 249);
      padding: 12px;
      border-radius: 12px;
      margin-bottom: 20px;
    }

    .logo-background img {
      width: 100px;
      height: auto;
      display: block;
    }

    .recovery-container h2 {
      color: #8000ff;
      margin-bottom: 20px;
      text-align: center;
    }

    form {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    input[type="email"] {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      text-align: center;
    }

    .btn-send {
      width: 60%;
      padding: 8px;
      font-size: 14px;
      background-color: #8000ff;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-send:hover {
      background-color: #6200c0;
    }

    .link-back {
      display: block;
      margin-top: 15px;
      font-size: 13px;
      color: #8000ff;
      text-decoration: none;
      text-align: center;
    }

    .link-back:hover {
      text-decoration: underline;
    }

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
      font-family: 'Segoe UI', sans-serif;
    }

    .success {
      color: green;
      font-weight: 600;
      font-size: 1.1rem;
    }

    .error {
      color: red;
      font-weight: 600;
      font-size: 1.1rem;
    }

    .close-btn {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #8000ff;
      border: none;
      border-radius: 4px;
      color: white;
      font-weight: 600;
      cursor: pointer;
    }

    .close-btn:hover {
      background-color: #6200c0;
    }
  </style>
</head>
<body>

  <div class="recovery-container">
    <div class="logo-background">
      <img src="img/LogoAuto-Blanco.png" alt="Logo Autozone">
    </div>

    <h2>Recuperar contraseña</h2>

    <form method="POST" autocomplete="off">
      <input type="email" name="email" list="email-list" placeholder="Correo electrónico" required autocomplete="email" />
      <datalist id="email-list">
        <option value="ejemplo1@gmail.com">
        <option value="usuario@hotmail.com">
        <option value="cliente@empresa.com">
        <option value="nombre@yahoo.com">
        <!-- Puedes agregar más opciones -->
      </datalist>

      <button type="submit" class="btn-send">Enviar código</button>
    </form>

    <a class="link-back" href="iniciar_sesion.php">Volver al login</a>
  </div>

  <!-- Modal -->
  <div id="myModal">
    <div class="modal-content">
      <p id="modalText"></p>
      <button id="modalCloseBtn" class="close-btn">Cerrar</button>
    </div>
  </div>

<?php
  $modalMessage = "";
  $modalType = ""; 

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $email = trim($_POST["email"]);
      if (!empty($email)) {
          $endpoint = "https://alexcg.de/autozone/api/password_reset_client.php";
          $data = json_encode(["email" => $email]);

          $options = [
              'http' => [
                  'header'  => "Content-Type: application/json\r\n",
                  'method'  => 'POST',
                  'content' => $data,
                  'timeout' => 10
              ]
          ];

          $context  = stream_context_create($options);
          $result = @file_get_contents($endpoint, false, $context);

          if ($result === FALSE) {
              $modalMessage = "No se pudo contactar al servidor. Intenta más tarde.";
              $modalType = "error";
          } else {
              $response = json_decode($result, true);
              if (isset($response['success']) && $response['success'] === true) {
                  $modalMessage = "Revisa tu correo para continuar con la recuperación.";
                  $modalType = "success";
              } else {
                  $mensaje = isset($response['message']) ? $response['message'] : "Ocurrió un error.";
                  $modalMessage = $mensaje;
                  $modalType = "error";
              }
          }
      } else {
          $modalMessage = "Ingresa un correo válido.";
          $modalType = "error";
      }
  }
?>

<script>
  const modal = document.getElementById('myModal');
  const modalText = document.getElementById('modalText');
  const modalCloseBtn = document.getElementById('modalCloseBtn');

  modalCloseBtn.addEventListener('click', () => {
    modal.classList.remove('show');
  });

  <?php if (!empty($modalMessage)): ?>
    modalText.textContent = <?php echo json_encode($modalMessage); ?>;
    modalText.className = <?php echo json_encode($modalType); ?>;
    modal.classList.add('show');
  <?php endif; ?>
</script>

</body>
</html>