<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDxyJi0YpxzfQMDskUaYHLTVcIx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuRj2gYAlas1myPmzMkhGGxRmH4Ia9kj/jOFxnYdoYhsKGHTpBOVgWl5rQOKcxlz9KYkrjCOI/QYyZtsK8qwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="form-section">
            <h2>Iniciar Sesión</h2>
            <form action="#" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="showPassword">
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
            <img src="assets/img/logo.svg" alt="Logo de la Empresa" class="img-fluid">
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>