<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoZone</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/materialize.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">  
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  </head>

  <body>
    <div class="item" id="Inicio">
</div>
<nav id="menuFlotante" class="white oculto" style="position: fixed; top: 0; width: 100%; z-index: 1000;">
  <div class="container">
    <div class="nav-wrapper">
      <a class="brand-logo"><img src="img/LogoAuto.png" alt="Logo"></a>
      <a href="#" data-target="mobile-menu" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      <ul class="right hide-on-med-and-down">
        <?php include 'menu.php'; ?>
      </ul>
    </div>
  </div>
</nav>

    <div class="container" style="margin-top: 40px;">
      <div class="row">
        <div class="col s50 m3">
          <div>
            <br>
            <br>
            <h4 class="black-text" style="font-weight: bold;">Tu Ruta,</h4>
            <br><br><br><br>
            <h6 class="grey-text" style="font-weight: bold;">
              Es hora de cumplir tu sueño, ven a hacerlo realidad
            </h6>
          </div>
        </div>

    
</div>

           

          <!--El pie de la página -->

                        </div>
                      </div>                     
                      <div style='margin-top: 200px;'> 
                  <footer id="color-footer" class="page-footer">
                    <div class="container">
                      <div class="row"> 
                        <div class="col l6 s12">
                          <a href="index.html" class="brand-logo">
                            <img src="img/LogoAuto-Blanco.png" alt="Logo" style="width: 250px;">
                          </a>
                        </div>

                        <div class="col l4 offset-l2 s12">
                          <li>                             
                              <i class="material-icons">call</i>
                              <p class="grey-text text-lighten-4">12-21 Zona 1, Guatemala +502 2244 - 1155</p>
                            </li>
                      </div>
                      </div>
                    </div>
                    <div class="footer-copyright">  
                      <div class="container">
                        © 2025 Copyright AUTOZONE               
                      </div>
                    </div>
                  </footer>
    
    

    <div id="modal1" class="modal bottom-sheet">
      <div class="modal-content">
        <h4>Modal Header</h4>
        <p>A bunch of text</p>
      </div>
      <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Agree</a>
      </div>
    </div>
    
    <script>
       document.addEventListener('DOMContentLoaded', function () {
  const menu = document.getElementById('menuFlotante');

  function mostrarMenuAlHacerScroll() {
    if (window.scrollY > 0) {
      menu.classList.remove('oculto');
    } else {
      menu.classList.remove('oculto'); // también visible al estar en la parte superior
    }
  }

  // Mostrar siempre al cargar
  mostrarMenuAlHacerScroll();

  // Mostrar también al hacer scroll
  window.addEventListener('scroll', mostrarMenuAlHacerScroll);
});

</script>


  </body>

</html>
