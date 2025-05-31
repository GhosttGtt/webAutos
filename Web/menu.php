<?php
session_start();
?>

<?php
$currentPage = basename($_SERVER['PHP_SELF']);

if ($currentPage === 'index.php') {
    $menuItems = [
        'Inicio'   => '#Inicio',
        'Autos'    => '#auto',
        'Contacto' => '#Contacto',
        'Cita'     => '#cita',
        'Misión'   => 'mision.php',
        'Visión'   => 'vision.php',
    ];
} else {
    $menuItems = [
        'Inicio'   => 'index.php#Inicio',
        'Autos'    => 'index.php#auto',
        'Contacto' => 'index.php#Contacto',
        'Cita'     => 'index.php#cita',
        'Misión'   => 'mision.php',
        'Visión'   => 'vision.php',
    ];
}
?>

<head>
  <style>
    nav .nav-wrapper ul li a {
      color: rgb(0, 0, 0);
    }
    nav .nav-wrapper ul li a i.material-icons {
      color: rgb(110, 32, 193);
    }
  </style>
</head>

<?php 
$currentPage = basename($_SERVER['PHP_SELF']);
foreach ($menuItems as $name => $link): 
    $activeClass = ($link === $currentPage) ? 'class="active"' : '';
?>
    <li <?php echo $activeClass; ?>><a href="<?php echo $link; ?>"><?php echo $name; ?></a></li>
<?php endforeach; ?>

<?php if (isset($_SESSION['username'])): ?>
    <li>
        <a href="#" style="pointer-events: none; color: gray;">
            Bienvenido: <?php echo htmlspecialchars($_SESSION['username']); ?>
        </a>
    </li>
    <li>
        <a href="logout.php" title="Cerrar sesión">
            <i class="material-icons">logout</i>
        </a>
    </li>
<?php else: ?>
    <li>
        <a class="dropdown-trigger" href="iniciar_sesion.php" data-target="userDropdown">
            <i class="material-icons">account_circle</i>
        </a>
    </li>
    <ul id="userDropdown" class="dropdown-content">
        <li class="logmenu">
            <a href="#loginModal" class="modal-trigger">
                <i class="material-icons">login</i> Iniciar Sesión
            </a>
        </li>
        <li class="logmenu">
            <a href="#registerModal" class="modal-trigger">
                <i class="material-icons">person_add</i> Registrarse
            </a>
        </li>
    </ul>
<?php endif; ?>

<!-- MODAL LOGIN -->
<div id="loginModal" class="modal" style="max-width: 400px; padding: 10px;">
    <div class="modal-content">
        <h5>Iniciar Sesión</h5>
        <?php if (isset($_SESSION['error_login'])): ?>
            <div class="card-panel red lighten-4 red-text text-darken-4" style="margin-top: 10px; padding: 10px;">
                <?php 
                    echo $_SESSION['error_login'];
                    unset($_SESSION['error_login']);
                ?>
            </div>
        <?php endif; ?>
        <form method="post" action="iniciar_sesion.php" id="loginForm" onsubmit="return handleLogin(event)">
            <div class="card-panel red lighten-4 red-text text-darken-4" id="error-message" style="display: none;"></div>
            <div class="input-field">
                <input type="text" id="username" name="username" required>
                <label for="username">Correo</label>
            </div>
            <div class="input-field password-container">
                <input type="password" id="password" name="password" required>
                <label for="password">Contraseña</label>
                <i class="material-icons toggle-password-login">visibility_off</i>
            </div>
            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close btn-flat">Cerrar</a>
    </div>
</div>

<!-- MODAL REGISTRO -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <h4>Registrarse</h4>
        <form action="crear_cuenta.php" method="post">
            <div class="input-field">
                <input type="text" id="username" name="username" required>
                <label for="username">Nombre</label>
            </div>
            <div class="input-field">
                <input type="email" id="email" name="email" required>
                <label for="email">Correo Electrónico</label>
            </div>
            <div class="input-field" style="display: flex; align-items: center;">
                <input type="password" id="passwords" name="password" required>
                <label for="password" style="margin-top: -15px;">Contraseña</label>
                <button toggle="#password" class="material-icons toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility()">visibility</button>
            </div>
            <button type="submit" class="btn">Registrarse</button>
        </form>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close btn-flat">Cerrar</a>
    </div>
</div>

<!-- SCRIPTS -->
<script>
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('passwords');
        const toggleIcon = document.querySelector('.toggle-password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        toggleIcon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
    }

    document.addEventListener('DOMContentLoaded', function() {
        var modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);

        var elems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(elems, {
            constrainWidth: false,
            coverTrigger: false
        });

        const togglePasswordLogin = document.querySelector('.toggle-password-login');
        const passwordInputLogin = document.getElementById('password');
        if (togglePasswordLogin && passwordInputLogin) {
            togglePasswordLogin.addEventListener('click', function() {
                if (passwordInputLogin.type === 'password') {
                    passwordInputLogin.type = 'text';
                    this.textContent = 'visibility';
                } else {
                    passwordInputLogin.type = 'password';
                    this.textContent = 'visibility_off';
                }
            });
        }

        <?php if (isset($_SESSION['error_login'])): ?>
            var loginModal = document.getElementById('loginModal');
            var instance = M.Modal.init(loginModal, {
                dismissible: false
            });
            instance.open();
        <?php endif; ?>
    });

    function validateLoginForm() {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const errorDiv = document.getElementById('error-message');
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(username)) {
            errorDiv.textContent = 'Por favor, ingrese un correo electrónico válido';
            errorDiv.style.display = 'block';
            return false;
        }

        if (password.length < 5) {
            errorDiv.textContent = 'La contraseña debe tener al menos 5 caracteres';
            errorDiv.style.display = 'block';
            return false;
        }

        return true;
    }

    function handleLogin(event) {
        event.preventDefault();
        if (!validateLoginForm()) return false;

        const formData = new FormData(document.getElementById('loginForm'));
        fetch('iniciar_sesion.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.php';
            } else {
                const errorDiv = document.getElementById('error-message');
                errorDiv.textContent = data.message;
                errorDiv.style.display = 'block';
                var loginModal = document.getElementById('loginModal');
                var instance = M.Modal.getInstance(loginModal);
                if (instance) instance.open();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });

        return false;
    }
</script>