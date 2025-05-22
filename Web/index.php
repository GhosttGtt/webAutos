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
       <div class="section" style="height: 50px;"></div>
      <div class="row">
        <div class="col s50 m3">
          <div>
            <h4 class="black-text" style="font-weight: bold;">Tu Ruta,</h4>
            <h4 class="black-text" style="font-weight: bold;">Tu Carro,</h4>
            <h4 class="black-text" style="font-weight: bold;">Tu Camino,</h4>
            <br><br><br><br>
            <h6 class="grey-text" style="font-weight: bold;">
              Es hora de cumplir tu sueño, ven a hacerlo realidad
            </h6>
          </div>
        </div>

        <div class="col s50 m6">
          <div class="center">
            <img src="img/carroRojo.png" alt="Carro Rojo" style="width: 500px; height: auto;">    
            <br><br><br><br><br>
        <div style="display: flex; justify-content: center;">
          <img src="img/modelos.png" alt="Modelos" style="width: 1000px; height: auto;">
        </div>
          </div>
        </div>

        <div class="col s50 m3">
          <div class="right-align">
            <a href="index.html">
              <img src="img/burbujaServicio.png" alt="Burbuja" style="width: 150px; height: auto;">
            </a>
            <div class="right-align">
              <h5 class="black-text" style="font-weight: bold; margin-bottom: 8px;">
                12.5k personas
              </h5>
              <p class="black-text" style="font-size: 1.1rem; line-height: 1;">
                Han utilizado nuestro servicio y recomiendan comprar con nosotros.
              </p>
            </div>
          </div>
        </div>

<div class="section" style="height: 480px;"></div>

</div>

            <!--AUTO-->
            <div class="item" id="auto">
              <div class="form-section">
                       <?php include 'auto.php'; ?>
                </div>
       
          <!--CONTACTO-->

          <div class="item" id="Contacto">  
              <div class="form-section">
                       <?php include 'contacto.php'; ?>
                </div>

          <!--CITA-->

          <div class="item" id="cita">
                       <?php include 'cita.php'; ?>
               </div>

          <!--PIE DE PAGINA -->

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
          const inicio = document.getElementById('Inicio');

          function verificarScroll() {
            const limite = inicio.offsetTop + inicio.offsetHeight;
            const scrollY = window.scrollY;

            if (scrollY < limite) {
              menu.classList.add('oculto');
            } else {
              menu.classList.remove('oculto');
            }
          }

          // Verificamos al cargar
          verificarScroll();

          // Verificamos al hacer scroll
          window.addEventListener('scroll', verificarScroll);
        });
</script>


  </body>

</html>
