<?php
session_start();

$mensaje = "";
$tipo_mensaje = ""; // 'success' o 'error'

if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

if (isset($_POST["btnregistrar"])) {
    $name = $_POST["txtnombre"];
    $lastname = $_POST["txtapellido"];
    $email = $_POST["txtcorreo"];
    $phone = $_POST["txttelefono"];
    $password = $_POST["txtpassword"];

    if (empty($name) || empty($lastname) || empty($email) || empty($phone) || empty($password)) {
        $mensaje = 'Todos los campos son obligatorios';
        $tipo_mensaje = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Correo no válido';
        $tipo_mensaje = 'error';
    } else {
        $data = [
            "name" => $name,
            "lastname" => $lastname,
            "email" => $email,
            "phone" => $phone,
            "password" => $password
        ];

        $jsonData = json_encode($data);
        $ch = curl_init("https://alexcg.de/autozone/api/clients_create.php");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $mensaje = 'Error de conexión: ' . curl_error($ch);
            $tipo_mensaje = 'error';
        } else {
            $responseData = json_decode($response, true);
            if ($http_code == 200 && isset($responseData['success']) && $responseData['success'] === true) {
                $_SESSION['usuario'] = $email;
                $_SESSION['nombre'] = $name;
                $_SESSION['apellido'] = $lastname;

                $mensaje = 'Cuenta creada con éxito. Redirigiendo...';
                $tipo_mensaje = 'success';

                // Redirigir después de 3 segundos con JavaScript
                echo "<script>
                        setTimeout(function() {
                            window.location='index.php';
                        }, 3000);
                      </script>";
            } else {
                $mensaje = $responseData['message'] ?? 'Error desconocido';
                $tipo_mensaje = 'error';
            }
        }
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registrarme</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            background-image: url('img/fondo_auto.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .register-container {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.2);
            padding: 30px 24px;
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
        .register-container h2 {
            color: #7b1fa2;
            margin-bottom: 16px;
            font-size: 20px;
        }
        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
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
        }
        .checkbox-label input {
            margin-right: 8px;
        }
        .btn-register {
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
        .btn-register:hover {
            background-color: #5d00c1;
        }
        .link-login {
            display: block;
            margin-top: 16px;
            font-size: 13px;
            color: #8000ff;
            text-decoration: none;
        }
        .link-login:hover {
            text-decoration: underline;
        }
        .btn-home {
            margin-top: 20px;
            background-color: #8000ff;
            color: #e3e3e3;
            padding: 8px 16px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .btn-home:hover {
            background-color: #ccc;
            color: #333;
        }

        /* Modal estilos */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 24px 30px;
            border-radius: 14px;
            text-align: center;
            max-width: 320px;
            width: 90%;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.3);
        }

        .modal-content button {
            margin-top: 16px;
            padding: 8px 20px;
            border: none;
            background-color: #8000ff;
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .modal-content button:hover {
            background-color: #5d00c1;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="logo-button-style">
        <img src="img/LogoAuto-Blanco.png" alt="Logo" />
    </div>

    <h2>Crear nueva cuenta</h2>

    <form method="post">
        <input type="text" name="txtnombre" placeholder="Nombre completo" required />
        <input type="text" name="txtapellido" placeholder="Apellido completo" required />
        <input type="email" name="txtcorreo" placeholder="Correo electrónico" required />
        <input type="text" name="txttelefono" placeholder="Teléfono" required />
        <input type="password" id="txtpassword" name="txtpassword" placeholder="Contraseña" required />

        <label class="checkbox-label">
            <input type="checkbox" onclick="togglePassword()" /> Mostrar contraseña
        </label>

        <input type="submit" name="btnregistrar" value="Registrarme" class="btn-register" />

        <a href="iniciar_sesion.php" class="link-login">¿Ya tienes cuenta? Iniciar sesión</a>
        <a href="index.php" class="btn-home">Regresar al inicio</a>
    </form>
</div>

<!-- Modal -->
<div id="modalMensaje" class="modal">
    <div class="modal-content">
        <p id="textoModal"></p>
        <button onclick="cerrarModal()">Cerrar</button>
    </div>
</div>

<script>
    function togglePassword() {
        const pwd = document.getElementById("txtpassword");
        pwd.type = (pwd.type === "password") ? "text" : "password";
    }

    function cerrarModal() {
        document.getElementById("modalMensaje").style.display = "none";
    }

    <?php if (!empty($mensaje)): ?>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalMensaje');
        const texto = document.getElementById('textoModal');
        const botonCerrar = document.querySelector('#modalMensaje button');

        texto.textContent = <?= json_encode($mensaje) ?>;
        texto.className = <?= json_encode($tipo_mensaje) ?>;
        modal.style.display = 'flex';

        <?php if ($tipo_mensaje === 'success'): ?>
        botonCerrar.style.display = 'none';
        <?php else: ?>
        botonCerrar.style.display = 'inline-block';
        <?php endif; ?>
    });
    <?php endif; ?>
</script>

</body>
</html>
