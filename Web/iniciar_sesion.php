<?php
session_start();

if (isset($_POST['btningresar'])) {
    $correo = trim($_POST['txtcorreo']);
    $password = trim($_POST['txtpassword']);

    if (empty($correo) || empty($password)) {
        $_SESSION['error'] = 'Por favor, completa todos los campos.';
header('Location: login.php'); // o el mismo archivo
exit();

    } else {
        $endpoint = "https://www.alexcg.de/autozone/api/login_client.php";

        $data = json_encode([
            "email" => $correo,
            "password" => $password
        ]);

        $options = [
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => $data,
                'timeout' => 10
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($endpoint, false, $context);

        if ($result === FALSE) {
            echo "<script>alert('Error al conectarse al servidor.');</script>";
        } else {
            $response = json_decode($result, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<h4>Error al decodificar JSON:</h4>";
                echo "<pre>" . htmlspecialchars($result) . "</pre>";
                exit;
            }

            if ($response && isset($response['success']) && $response['success'] === true) {
    if (isset($response['user']['name'])) {
        $_SESSION['username'] = $response['user']['name']; // ✅ CORRECTO
        header("Location: index.php");
        exit();
    } else {
        echo "<h4>Respuesta JSON incompleta:</h4>";
        echo "<pre>" . print_r($response, true) . "</pre>";
        exit();
    }
            } else {
                $mensaje = isset($response['message']) ? $response['message'] : "Error de autenticación.";
                echo "<script>alert('$mensaje'); window.location.href = 'iniciar_sesion.php';</script>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Autozone</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      margin: 0;
      background: rgb(255, 252, 252);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-image: url('img/auto_fondo.jpg');
      background-size: cover;
      background-position: center;
    }

    .login-container {
      background: #ffffff;
      border-radius: 18px;
      padding: 30px 24px;
      box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.2);
      max-width: 360px;
      width: 90%;
      text-align: center;
    }

    .logo-button-style {
      background-color: rgb(170, 96, 244);
      border-radius: 20px;
      padding: 10px 20px;
      display: inline-block;
      margin-bottom: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .logo-button-style img {
      width: 120px;
      display: block;
    }

    .login-container h2 {
      color: #7b1fa2;
      margin-bottom: 20px;
      font-size: 20px;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 14px;
    }

    .checkbox-label {
      display: flex;
      align-items: center;
      font-size: 13px;
      color: #555;
      margin-bottom: 14px;
      justify-content: left;
    }

    .checkbox-label input {
      margin-right: 8px;
    }

    .btn-login {
      width: 100%;
      padding: 10px;
      border: none;
      background-color: #8000ff;
      color: #fff;
      font-weight: bold;
      font-size: 15px;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-login:hover {
      background-color: #5d00c1;
    }

    .link {
      display: block;
      margin-top: 12px;
      font-size: 13px;
      color: #8000ff;
      text-decoration: none;
    }

    .link:hover {
      text-decoration: underline;
    }

    .btn-home {
      margin-top: 20px;
      background-color: #8000ff;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 10px;
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .btn-home:hover {
      background-color: #5e0cd4;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <!-- Logo -->
    <div class="logo-button-style">
      <img src="img/LogoAuto-Blanco.png" alt="Logo Autozone">
    </div>

    <h2>Iniciar sesión</h2>

    <form method="POST" action="iniciar_sesion.php">
      <input type="text" name="txtcorreo" placeholder="Correo electrónico" required>
      <input type="password" id="txtpassword" name="txtpassword" placeholder="Contraseña" required>

      <label class="checkbox-label">
        <input type="checkbox" onclick="verpassword()"> Mostrar contraseña
      </label>

      <input type="submit" class="btn-login" value="Ingresar" name="btningresar">

      <a href="crear_cuenta.php" class="link">¿No tienes cuenta? Registrarme</a>
      <a href="recuperar.php" class="link">¿Has olvidado tu contraseña?</a>
      <a href="index.php" class="btn-home">Regresar al inicio</a>
    </form>
  </div>

  <script>
    function verpassword() {
      const pass = document.getElementById("txtpassword");
      pass.type = pass.type === "password" ? "text" : "password";
    }
  </script>

</body>
</html>