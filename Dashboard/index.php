<?php
session_start();

function loginUser($username, $password)
{
    $url = 'https://alexcg.de/autozone/api/login.php';
    $credentials = [
        'username' => $username,
        'password' => $password
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);

    if (isset($data['success']) && $data['success'] === true && isset($data['user'])) {
        $_SESSION['user'] = $data['user'];
        $_SESSION['token'] = $data['token'] ?? null;
        return true;
    } else {
        return false;
    }
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (loginUser($username, $password)) {
        header('Location: homePage.php?page=products');
        exit();
    } else {
        $error_message = 'Usuario o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/login.css" />
</head>

<body>
    <div class="login-container">
        <div class="form-section">
            <h2>Iniciar Sesión</h2>
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form action="index.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="username" name="username" required />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required />
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="showPassword" />
                        <label class="form-check-label" for="showPassword">Mostrar contraseña</label>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Acceder</button>
                </div>
                <div class="mt-3 text-center">
                    <a href="register.php">¿No tienes cuenta?</a>
                </div>
            </form>
        </div>
        <div class="logo-section">
            <img src="assets/img/logo.svg" alt="Logo de la Empresa" class="img-fluid" />
        </div>
    </div>

    <script>
        document.getElementById('showPassword').addEventListener('change', function() {
            const pwd = document.getElementById('password');
            pwd.type = this.checked ? 'text' : 'password';
        });
    </script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>