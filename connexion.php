<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

  <title>Connexion</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/owl.css">
  <link rel="stylesheet" href="assets/css/animate.css">
  <link rel="stylesheet" href="assets/css/animations.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">

</head>
<style>
  :root {
    --couleur-success: #ffc527;
    --couleur-principale: #ff9f1c;
    --couleur-secondaire: #ff7a00;
    --header-blue: #0b5ed7;
    /* bleu bootstrap-like */
    --header-blue-dark: #094bb5;
    --couleur-principale1: #133ebe;
    --couleur-secondaire1: #2e59d9;
  }

  /* ====== Styles scoped ONLY to the calculator section ====== */
  .calculator {
    /* container scope (no visual changes here) */
  }

  .calculator .register-card {
    background: linear-gradient(59deg, #05174a, #0c38bb);
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    padding: 40px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.69);
    color: #fff;
  }

  .calculator .card-header:first-child {
    border-radius: 35px;
  }

  .calculator .card-header {
    padding: 16px 0rem;
    margin-bottom: 0;
    background-color: #cfd2d91f;
    border-bottom: 1px solid rgba(0, 0, 0, .125);
    color: #fff;
  }

  .calculator .card-header h6 {
    color: var(--couleur-success);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 13px;
    margin-bottom: 6px;
  }

  .calculator .card-header h4 {
    color: #fff;
    font-size: 22px;
    margin: 0 0 6px;
    font-weight: 700;
  }

  .calculator .card-header p {
    color: rgba(255, 255, 255, 0.85);
    margin: 0 0 12px;
    font-size: 14px;
  }

  /* Form controls - INSIDE the dark card only */
  .calculator .register-form .form-group {
    margin-bottom: 1rem;
  }

  .calculator .register-form .form-label {
    color: #fff;
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
  }

  .calculator .input-with-icon {
    position: relative;
  }

  .calculator .input-with-icon i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--couleur-success);
    z-index: 2;
    font-size: 16px;
  }

  .calculator .register-form input,
  .calculator .register-form select {
    width: 100%;
    height: 48px;
    padding: 0 14px 0 42px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: #fff;
    outline: none;
    transition: all .18s ease;
  }

  .calculator .register-form input::placeholder {
    color: rgba(255, 255, 255, 0.6);
  }

  .calculator .register-form input:focus {
    background: rgba(255, 255, 255, 0.06);
    border-color: var(--couleur-success);
    box-shadow: 0 6px 18px rgba(246, 194, 62, 0.08);
  }

  .calculator .register-btn {
    width: 100%;
    height: 52px;
    border-radius: 8px;
    background: linear-gradient(135deg, #fac021ff, var(--couleur-success));
    border: none;
    color: #ffffffff;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .calculator .register-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 159, 28, 0.3);
  }

  .calculator .login-link {
    text-align: center;
    margin-top: 12px;
    color: rgba(255, 255, 255, 0.8);
  }

  .calculator .login-link a {
    color: var(--couleur-success);
    font-weight: 600;
    text-decoration: none;
  }

  .calculator .login-link a:hover {
    text-decoration: underline;
  }

  /* Notifications */
  .notification {
    position: fixed;
    top: 100px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: white;
    z-index: 10000;
    max-width: 400px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    justify-content: space-between;
    animation: slideIn 0.3s ease-out;
  }

  .notification-success {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    border-left: 4px solid #2E7D32;
  }

  .notification-error {
    background: linear-gradient(135deg, #f44336, #d32f2f);
    border-left: 4px solid #b71c1c;
  }

  .notification-warning {
    background: linear-gradient(135deg, #ff9800, #f57c00);
    border-left: 4px solid #e65100;
  }

  .notification-info {
    background: linear-gradient(135deg, #2196F3, #1976D2);
    border-left: 4px solid #0D47A1;
  }

  .close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    margin-left: 10px;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .register-redirect-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
    margin-left: 10px;
  }

  .register-redirect-btn:hover {
    background: rgba(255, 255, 255, 0.3);
  }

  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }

    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  /* Responsive — only for elements inside .calculator */
  @media (max-width:991px) {
    .calculator .register-card {
      padding: 28px;
      border-radius: 16px;
    }
  }

  @media (max-width:576px) {
    .calculator .register-card {
      padding: 18px;
    }
  }
</style>

<body class="page-connexion">


  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <!-- ***** Logo Start ***** -->
            <a href="index.php" class="logo">
              <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
            </a>
            <!-- ***** Logo End ***** -->
            <!-- ***** Menu Start ***** -->
            <ul class="nav">
              <li class="scroll-to-section"><a href="index.php#top">Accueil</a></li>
              <li class="scroll-to-section"><a href="index.php#services">Services</a></li>
              <li class="scroll-to-section"><a href="index.php#about">À Propos</a></li>
              <li class="scroll-to-section"><a href="index.php#testimonials">Créateur</a></li>
              <li class="scroll-to-section"><a href="index.php#register">Inscription</a></li>
              <li class="scroll-to-section1"><a href="connexion.php" class="active">Connexion</a></li>
            </ul>
            <!-- ***** Menu End ***** -->
          </nav>
        </div>
      </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->

  <div class="page-heading">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="header-text">
            <h2>Connexion</h2>
            <div class="div-dec"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Main : Formulaire de connexion (droite) + petite carte d'accroche (gauche) -->
  <section class="calculator" style="padding:100px 0;background: transparent">
    <div class="container">
      <div class="row align-items-center">
        <!-- Colonne gauche : Contenu texte (70%) -->
        <div class="col-lg-8">
          <div class="banner-content left-aligned">
            <div class="header-text">
              <h2 class="main-title">Bienvenue dans la <span class="highlight">Communauté TDSI</span></h2>
              <div class="div-dec animated-line"></div>
              <p class="fade-in-text">Connecte-toi pour accéder à ton tableau de bord, tes ressources et ton assistant
                IA personnalisé.</p>

              <!-- Avantages -->
              <div class="benefits-list mt-4">
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Accès aux cours et ressources TDSI</span>
                </div>
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Historique de conversations avec l'IA</span>
                </div>
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Assistant IA 24/7 spécialisé en TDSI</span>
                </div>
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Bibliothèque de ressources exclusive</span>
                </div>
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Communauté étudiante active</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- droite : formulaire -->
        <div class="col-lg-6">
          <div class="register-card">
            <div class="card-header">
              <h6>Accès Membres</h6>
              <h4>Connectez-vous</h4>
              <p>Entrez vos identifiants pour continuer</p>
            </div>

            <form id="login-form" action="includes/login.php" method="POST" class="register-form" novalidate>
              <div class="row">
                <div class="col-12 form-group">
                  <label for="login_email" class="form-label">Email</label>
                  <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="login_email" name="email" placeholder="votre@email.com" required
                      value="<?php echo isset($_SESSION['old_login_data']['email']) ? htmlspecialchars($_SESSION['old_login_data']['email']) : ''; ?>">
                  </div>
                </div>

                <div class="col-12 form-group">
                  <label for="login_password" class="form-label">Mot de passe</label>
                  <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="login_password" name="password" placeholder="••••••••" required>
                  </div>
                </div>

                <div class="col-12 form-group" style="display:flex; align-items:center; justify-content:space-between;">
                  <div style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" id="remember_me" name="remember_me" style="width:16px; height:16px;">
                    <label for="remember_me" style="color:rgba(255,255,255,0.85); margin:0; font-size:13px;">Se souvenir
                      de moi</label>
                  </div>
                  <div>
                    <a href="mot_de_passe_oublie.php"
                      style="color:var(--couleur-success); font-weight:600; text-decoration:none; font-size:13px;">Mot
                      de passe oublié ?</a>
                  </div>
                </div>

                <div class="col-12">
                  <button type="submit" class="register-btn">
                    <span>Se connecter</span>
                    <i class="fas fa-arrow-right"></i>
                  </button>
                </div>

                <div class="col-12">
                  <div class="login-link">
                    <p>Pas encore de compte ? <a href="index.php#register">S'inscrire</a></p>
                  </div>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ***** Footer Start ***** -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br>Développé par Ibrahima Khalilou llah Sylla -
            Licence 2 TDSI</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>

  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/swiper.js"></script>
  <script src="assets/js/custom.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Afficher les messages PHP depuis la session
      showSessionMessages();

      // Gestion du formulaire de connexion
      const loginForm = document.getElementById('login-form');
      if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
          const email = document.getElementById('login_email').value;
          const password = document.getElementById('login_password').value;

          // Validation email
          if (!isValidEmail(email)) {
            e.preventDefault();
            showNotification('Veuillez entrer une adresse email valide.', 'error');
            return false;
          }

          // Validation mot de passe
          if (password.length < 1) {
            e.preventDefault();
            showNotification('Veuillez entrer votre mot de passe.', 'error');
            return false;
          }
        });
      }
    });

    // Fonction pour afficher les messages de session PHP
    function showSessionMessages() {
      <?php if (isset($_SESSION['login_errors'])): ?>
        const errors = <?php echo json_encode($_SESSION['login_errors']); ?>;
        const isEmailNotFound = <?php echo isset($_SESSION['email_not_found']) ? 'true' : 'false'; ?>;
        const isPasswordError = <?php echo isset($_SESSION['password_error']) ? 'true' : 'false'; ?>;

        // Afficher chaque erreur
        errors.forEach(error => {
          showNotification(error, 'error');
        });

        // Si c'est une erreur d'email non trouvé, ajouter un bouton d'inscription
        if (isEmailNotFound) {
          const email = "<?php echo isset($_SESSION['old_login_data']['email']) ? $_SESSION['old_login_data']['email'] : ''; ?>";
          showEmailNotFoundNotification(email);
        }

        // Si c'est une erreur de mot de passe, suggérer la réinitialisation
        if (isPasswordError) {
          showPasswordErrorNotification();
        }

        // Nettoyer la session
        <?php
        unset($_SESSION['login_errors']);
        unset($_SESSION['old_login_data']);
        unset($_SESSION['email_not_found']);
        unset($_SESSION['password_error']);
        ?>
      <?php endif; ?>
    }

    // Fonction spéciale pour l'erreur d'email non trouvé
    function showEmailNotFoundNotification(email) {
      const notification = document.createElement('div');
      notification.className = 'notification notification-warning';
      notification.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
          <div style="flex: 1;">
            <span>Aucun compte trouvé avec cet email.</span>
          </div>
          <div style="display: flex; align-items: center;">
            <button onclick="redirectToRegister()" class="register-redirect-btn">
              S'inscrire
            </button>
            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="close-btn">&times;</button>
          </div>
        </div>
      `;

      document.body.appendChild(notification);

      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 10000);
    }

    // Fonction spéciale pour l'erreur de mot de passe
    function showPasswordErrorNotification() {
      const notification = document.createElement('div');
      notification.className = 'notification notification-error';
      notification.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
          <div style="flex: 1;">
            <span>Mot de passe incorrect. Voulez-vous réinitialiser votre mot de passe ?</span>
          </div>
          <div style="display: flex; align-items: center;">
            <button onclick="redirectToForgotPassword()" class="register-redirect-btn">
              Réinitialiser
            </button>
            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="close-btn">&times;</button>
          </div>
        </div>
      `;

      document.body.appendChild(notification);

      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 10000);
    }

    // Fonction pour rediriger vers la page d'inscription
    function redirectToRegister() {
      window.location.href = 'index.php#register';
    }

    // Fonction pour rediriger vers la page de mot de passe oublié
    function redirectToForgotPassword() {
    window.location.href = 'mot_de_passe_oublie.php';
}

    // Fonctions existantes
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `notification notification-${type}`;
      notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="close-btn">&times;</button>
      `;

      document.body.appendChild(notification);

      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 5000);
    }

    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

    // Effet de focus amélioré pour les champs de formulaire
    const formInputs = document.querySelectorAll('.calculator .register-form input');
    formInputs.forEach(input => {
      input.addEventListener('focus', function () {
        this.parentElement.style.transform = 'translateY(-2px)';
      });

      input.addEventListener('blur', function () {
        this.parentElement.style.transform = 'translateY(0)';
      });
    });
  </script>

</body>

</html>