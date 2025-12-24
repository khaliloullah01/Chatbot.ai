<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description"
    content="tdsi.ai - Plateforme d'apprentissage en Transmission de Données et Sécurité de l'Information">
  <meta name="author" content="Ibrahima Khalilou llah Sylla">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

  <title>tdsi.ai - Plateforme d'Apprentissage</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Dans la section <head> -->
  <link href="./vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/fontawesome.css">
  <!-- <link rel="stylesheet" href="./assets/css/templatemo-574-mexant.css"> -->
  <!-- <link rel="stylesheet" href="./assets/css/owl.css"> -->
  <link rel="stylesheet" href="./assets/css/animate.css">
  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <!-- <link rel="stylesheet" href="assets/css/templatemo-574-mexant.css"> -->
  <link rel="stylesheet" href="assets/css/owl.css">
  <link rel="stylesheet" href="assets/css/animate.css">
  <link rel="stylesheet" href="assets/css/animations.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">

  <style>
    /* Styles personnalisés pour le formulaire d'inscription */
    .register-form input,
    .register-form select {
      background-color: rgba(255, 255, 255, 0.1) !important;
      border: 1px solid rgba(255, 255, 255, 0.3) !important;
      border-radius: 5px !important;
      width: 100% !important;
      height: 50px !important;
      color: #fff !important;
      font-size: 14px !important;
      margin-bottom: 20px !important;
      outline: none !important;
      padding: 0px 40px !important;
      transition: all 0.3s ease !important;
    }

    .register-form input::placeholder {
      color: rgba(255, 255, 255, 0.7) !important;
    }

    .register-form input:focus,
    .register-form select:focus {
      background-color: rgba(255, 255, 255, 0.2) !important;
      border-color: var(--couleur-success) !important;
      box-shadow: 0 0 10px rgba(246, 194, 62, 0.3) !important;
    }

    .register-form label {
      color: #fff !important;
      font-size: 14px !important;
      margin-bottom: 8px !important;
      display: block !important;
      font-weight: 500 !important;
    }

    .register-form .form-check {
      margin: 20px 0 !important;
    }

    .register-form .form-check-input {
      margin-right: 10px !important;
    }

    .register-form .form-check-label {
      color: #fff !important;
      font-size: 14px !important;
    }

    .register-form .orange-button {
      width: 100% !important;
      margin-top: 10px !important;
    }

    /* Amélioration des témoignages */
    .testimonial-avatar {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      color: white;
      font-size: 32px;
      font-weight: bold;
    }

    /* Styles pour les notifications */
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

    .login-redirect-btn {
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

    .login-redirect-btn:hover {
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
  </style>

</head>

<body>

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
              <li class="scroll-to-section"><a href="index.php#top" class="active">Accueil</a></li>
              <li class="scroll-to-section"><a href="index.php#services">Services</a></li>
              <li class="scroll-to-section"><a href="index.php#about">À Propos</a></li>
              <li class="scroll-to-section"><a href="index.php#testimonials">Créateur</a></li>
              <li class="scroll-to-section"><a href="index.php#register">Inscription</a></li>
              <li class="scroll-to-section1"><a href="connexion.php">Connexion</a></li>
            </ul>
            <!-- ***** Menu End ***** -->
          </nav>
        </div>
      </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->
  <!-- ***** Main Banner Area Start ***** -->
  <div class="main-banner" id="top">
    <div class="container">
      <div class="row align-items-center">
        <!-- Colonne gauche pour le contenu textuel -->
        <div class="col-lg-65">
          <div class="banner-content left-aligned">
            <div class="header-text">
              <h2 class="main-title">Bienvenue sur <span class="highlight">tdsi.ai</span>, votre plateforme
                d'apprentissage <span class="animated-text">intelligente</span> et <span
                  class="animated-text">interactive</span>.</h2>
              <div class="div-dec animated-line"></div>
              <p class="fade-in-text">tdsi.ai révolutionne l'apprentissage en Transmission de Données et Sécurité de
                l'Information avec une IA spécialisée, des ressources pédagogiques avancées et un accompagnement
                personnalisé pour votre réussite académique.</p>
              <div class="buttons">
                <div class="green-button pulse-animation">
                  <a href="#services">
                    <i class="fas fa-rocket"></i>
                    Découvrir les Services
                  </a>
                </div>
                <div class="orange-button glow-animation">
                  <a href="#register">
                    <i class="fas fa-user-plus"></i>
                    Inscrivez Vous Maintenant
                  </a>
                </div>
              </div>
            </div>

            <!-- Statistiques animées -->
            <div class="animated-stats">
              <div class="stat-item">
                <div class="stat-number" data-count="92">0</div>
                <div class="stat-label">Satisfaction</div>
              </div>
              <div class="stat-item">
                <div class="stat-number" data-count="1900">0</div>
                <div class="stat-label">Questions traitées</div>
              </div>
              <div class="stat-item">
                <div class="stat-number" data-count="500">0</div>
                <div class="stat-label">Étudiants actifs</div>
              </div>
            </div>
          </div>

          <div class="animations-side">
            <!-- Icônes flottantes avec effet 3D -->
            <div class="floating-icons-3d">
              <div class="icon-3d i1" data-tooltip="Intelligence Artificielle">
                <i class="fas fa-brain"></i>
                <div class="icon-aura"></div>
              </div>
              <div class="icon-3d i2" data-tooltip="Sécurité Informatique">
                <i class="fas fa-shield-alt"></i>
                <div class="icon-aura"></div>
              </div>
              <div class="icon-3d i3" data-tooltip="Cryptographie Avancée">
                <i class="fas fa-lock"></i>
                <div class="icon-aura"></div>
              </div>
              <div class="icon-3d i4" data-tooltip="Transmission de Données">
                <i class="fas fa-broadcast-tower"></i>
                <div class="icon-aura"></div>
              </div>
              <div class="icon-3d i5" data-tooltip="Algorithmes">
                <i class="fas fa-code"></i>
                <div class="icon-aura"></div>
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- Colonne droite pour les animations -->

    </div>
  </div>
  <!-- ***** Main Banner Area End ***** -->
  <section class="services" id="services">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="service-item">
            <i class="fas fa-robot"></i>
            <h4>Chatbot Intelligent</h4>
            <p>Assistant IA disponible 24/7 pour répondre à vos questions sur les cours et concepts TDSI.</p>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="service-item">
            <i class="fas fa-book"></i>
            <h4>Ressources Pédagogiques</h4>
            <p>Accédez à une bibliothèque complète de cours et exercices de tout les niveau.</p>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="service-item">
            <i class="fas fa-star"></i>
            <h4>Mes Favoris</h4>
            <p>Organisez et accédez rapidement à vos cours, exercices et ressources préférés.</p>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="service-item">
            <i class="fas fa-history"></i>
            <h4>Historique des Conversations</h4>
            <p>Retrouvez l'ensemble de vos échanges avec l'IA pour réviser et consolider vos apprentissages.</p>
          </div>
        </div>

      </div>
    </div>
    </div>
  </section>

  <section class="simple-cta">
    <div class="container">
      <div class="row">
        <div class="col-lg-5">
          <h4>Plateforme <em>100% Gratuite</em> pour les <strong>Étudiants de la TDSI</strong></h4>
        </div>
        <div class="col-lg-7">
          <div class="buttons">
            <div class="green-button">
              <a href="#about">Découvrir la Plateforme</a>
            </div>
            <div class="orange-button">
              <a href="#register">Créer un Compte</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="about-us" id="about">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 offset-lg-3">
          <div class="section-heading">
            <h6>Découvrez Notre Plateforme</h6>
            <h4>Explorez tdsi.ai </h4>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="naccs">
            <div class="tabs">
              <div class="row">
                <div class="col-lg-12">
                  <div class="menu">
                    <div class="active gradient-border"><span>Assistant IA</span></div>
                    <div class="gradient-border"><span>Ressources</span></div>
                    <div class="gradient-border"><span>Suivi Personnel</span></div>
                  </div>
                </div>
                <div class="col-lg-12">
                  <ul class="nacc">
                    <li class="active">
                      <div>
                        <div class="main-list">
                          <span class="title">Fonctionnalité</span>
                          <span class="title">Description</span>
                          <span class="title">Avantage</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Assistant Spécialisé</span>
                          <span class="item">Expert en algèbre, cryptographie et cybersécurité</span>
                          <span class="item">Réponses précises et adaptées</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Disponibilité 24h/24</span>
                          <span class="item">Accès permanent à l'assistance</span>
                          <span class="item">Flexibilité totale d'apprentissage</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Historique Intelligent</span>
                          <span class="item">Sauvegarde de tous vos échanges</span>
                          <span class="item">Révision et continuité assurées</span>
                        </div>
                        <div class="list-item last-item">
                          <span class="item item-title">Explications Détaillées</span>
                          <span class="item">Guidage pas-à-pas des concepts complexes</span>
                          <span class="item">Compréhension approfondie</span>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div>
                        <div class="main-list">
                          <span class="title">Type de Ressource</span>
                          <span class="title">Contenu</span>
                          <span class="title">Niveaux</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Cours Complets</span>
                          <span class="item">Algèbre, cryptographie, cybersécurité</span>
                          <span class="item">Licence 1 à Master</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Exercices Pratiques</span>
                          <span class="item">Problèmes et cas concrets</span>
                          <span class="item">Tous niveaux</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Bibliothèque Algorithmique</span>
                          <span class="item">Méthodes et techniques avancées</span>
                          <span class="item">Référence expert</span>
                        </div>
                        <div class="list-item last-item">
                          <span class="item item-title">Favoris Personnels</span>
                          <span class="item">Cours et ressources sauvegardés</span>
                          <span class="item">Accès rapide</span>
                        </div>
                    </li>
                    <li>
                      <div>
                        <div class="main-list">
                          <span class="title">Fonctionnalité</span>
                          <span class="title">Description</span>
                          <span class="title">Bénéfice</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Mon Programme</span>
                          <span class="item">Suivi personnalisé de progression</span>
                          <span class="item">Apprentissage structuré</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Analyses de Performance</span>
                          <span class="item">Statistiques détaillées d'avancement</span>
                          <span class="item">Points forts/faibles identifiés</span>
                        </div>
                        <div class="list-item">
                          <span class="item item-title">Objectifs Personnalisés</span>
                          <span class="item">Plan d'apprentissage adapté</span>
                          <span class="item">Motivation maintenue</span>
                        </div>
                        <div class="list-item last-item">
                          <span class="item item-title">Recommandations IA</span>
                          <span class="item">Suggestions de contenu intelligent</span>
                          <span class="item">Progression optimisée</span>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="right-content">
            <h4>Votre Assistant IA pour la Réussite à la TDSI</h4>
            <p>tdsi.ai révolutionne l'apprentissage des sciences fondamentales en combinant intelligence artificielle et
              expertise pédagogique spécialisée en algèbre, cryptographie et cybersécurité.</p>
            <p>Notre plateforme s'adapte intelligemment à votre niveau et style d'apprentissage, vous offrant un
              accompagnement sur-mesure tout au long de votre parcours universitaire en Transmission de Données et
              Sécurité de l'Information.</p>
            <div class="green-button">
              <a href="#register">Commencer Maintenant</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="main-banner calculator" id="register">
    <div class="container">
      <div class="row align-items-center">
        <!-- Colonne gauche : Contenu texte (70%) -->
        <div class="col-lg-8">
          <div class="banner-content left-aligned">
            <div class="header-text">
              <h2 class="main-title">Rejoignez la <span class="highlight">Communauté TDSI</span></h2>
              <div class="div-dec animated-line"></div>
              <p class="fade-in-text">Créez votre compte pour accéder à l'assistant IA spécialisé TDSI, toutes les
                ressources pédagogiques avancées et un suivi personnalisé de votre progression.</p>

              <!-- Avantages -->
              <div class="benefits-list mt-4">
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Assistant IA spécialisé TDSI</span>
                </div>
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Accès à toutes les ressources pédagogiques</span>
                </div>
                <div class="benefit-item">
                  <i class="fas fa-check-circle"></i>
                  <span>Historique de conversations avec l'IA</span>
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
        <!-- Colonne droite : Formulaire (30%) -->
        <div class="col-lg-45">
          <div class="register-card">
            <div class="card-header">
              <h6>Commencez votre voyage</h6>
              <h4>Créez Votre Compte</h4>
              <p>Rejoignez des centaines d'étudiants TDSI</p>
            </div>

            <form id="register-form" action="includes/register.php" method="POST" class="register-form">
              <div class="row">
                <!-- Prénom et Nom -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <div class="input-with-icon">
                      <i class="fas fa-user"></i>
                      <input type="text" name="prenom" id="prenom" placeholder="Votre prénom" autocomplete="given-name"
                        required
                        value="<?php echo isset($_SESSION['old_register_data']['prenom']) ? htmlspecialchars($_SESSION['old_register_data']['prenom']) : ''; ?>">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="nom" class="form-label">Nom *</label>
                    <div class="input-with-icon">
                      <i class="fas fa-user"></i>
                      <input type="text" name="nom" id="nom" placeholder="Votre nom" autocomplete="family-name" required
                        value="<?php echo isset($_SESSION['old_register_data']['nom']) ? htmlspecialchars($_SESSION['old_register_data']['nom']) : ''; ?>">
                    </div>
                  </div>
                </div>

                <!-- Email -->
                <div class="col-12">
                  <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <div class="input-with-icon">
                      <i class="fas fa-envelope"></i>
                      <input type="email" name="email" id="email" placeholder="votre@email.com" required
                        value="<?php echo isset($_SESSION['old_register_data']['email']) ? htmlspecialchars($_SESSION['old_register_data']['email']) : ''; ?>">
                    </div>
                  </div>
                </div>

                <!-- Mot de passe et Confirmation -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                    <div class="input-with-icon">
                      <i class="fas fa-lock"></i>
                      <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="••••••••" required>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="confirmation_mot_de_passe" class="form-label">Confirmation *</label>
                    <div class="input-with-icon">
                      <i class="fas fa-lock"></i>
                      <input type="password" name="confirmation_mot_de_passe" id="confirmation_mot_de_passe"
                        placeholder="••••••••" required>
                    </div>
                  </div>
                </div>

                <!-- Niveau d'étude -->
                <div class="col-12">
                  <div class="form-group">
                    <label for="niveau_etude" class="form-label">Niveau d'étude *</label>
                    <div class="input-with-icon">
                      <i class="fas fa-graduation-cap"></i>
                      <select name="niveau_etude" id="niveau_etude" required>
                        <option value="">Sélectionnez votre niveau</option>
                        <option value="L1" <?php echo (isset($_SESSION['old_register_data']['niveau_etude']) && $_SESSION['old_register_data']['niveau_etude'] == 'L1') ? 'selected' : ''; ?>>Licence 1
                        </option>
                        <option value="L2" <?php echo (isset($_SESSION['old_register_data']['niveau_etude']) && $_SESSION['old_register_data']['niveau_etude'] == 'L2') ? 'selected' : ''; ?>>Licence 2
                        </option>
                        <option value="L3" <?php echo (isset($_SESSION['old_register_data']['niveau_etude']) && $_SESSION['old_register_data']['niveau_etude'] == 'L3') ? 'selected' : ''; ?>>Licence 3
                        </option>
                        <option value="M1" <?php echo (isset($_SESSION['old_register_data']['niveau_etude']) && $_SESSION['old_register_data']['niveau_etude'] == 'M1') ? 'selected' : ''; ?>>Master 1
                        </option>
                        <option value="M2" <?php echo (isset($_SESSION['old_register_data']['niveau_etude']) && $_SESSION['old_register_data']['niveau_etude'] == 'M2') ? 'selected' : ''; ?>>Master 2
                        </option>
                        <option value="Doctorat" <?php echo (isset($_SESSION['old_register_data']['niveau_etude']) && $_SESSION['old_register_data']['niveau_etude'] == 'Doctorat') ? 'selected' : ''; ?>>
                          Doctorat
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Bouton d'inscription -->
                <div class="col-12">
                  <button type="submit" class="register-btn">
                    <span>Créer mon compte gratuit</span>
                    <i class="fas fa-arrow-right"></i>
                  </button>
                </div>

                <!-- Lien de connexion -->
                <div class="col-12">
                  <div class="login-link">
                    <p>Déjà membre ? <a href="connexion.php">Connectez-vous ici</a></p>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
  </section>

  <section class="testimonials" id="testimonials">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 offset-lg-3">
          <div class="section-heading">
            <h6>Le Créateur</h6>
            <h4>Présentation du Développeur</h4>
          </div>
        </div>
        <div class="col-lg-10 offset-lg-1">
          <div class="owl-testimonials owl-carousel" style="position: relative; z-index: 5;">
            <div class="item">

              <i class="fa fa-quote-left"></i>
              <p>Ce chatbot a été développé dans le cadre de mon projet de fin de licence 2 à l'École de Transmission de
                Données et Sécurité de l'Information. Mon objectif était de créer un outil pédagogique innovant qui
                puisse aider les étudiants à mieux comprendre les concepts complexes liés à la sécurité informatique et
                aux transmissions de données.</p>
              <h4>Ibrahima Khalilou llah Sylla</h4>
              <span>Étudiant en Licence 2 - TDSI</span>
            </div>
            <div class="item">

              <i class="fa fa-quote-left"></i>
              <p>Ce projet vise à faciliter l'apprentissage des principes de sécurité informatique et offrir un support
                interactif pour les étudiants. Il démontre également l'application pratique des connaissances acquises
                et contribue à enrichir l'écosystème éducatif de notre école avec une approche technologique innovante.
              </p>
              <h4>Objectifs du Projet</h4>
              <span>Innovation Pédagogique & Technologique</span>
            </div>
            <div class="item">

              <i class="fa fa-quote-left"></i>
              <p>"En tant qu'étudiant passionné par les technologies de l'information et la cybersécurité, j'ai voulu
                créer un outil qui rendrait l'apprentissage plus accessible et interactif. Ce chatbot représente la
                synthèse des connaissances acquises et mon engagement à contribuer à notre communauté éducative."</p>
              <h4>Motivation Personnelle</h4>
              <span>Passion & Engagement Communautaire</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br>
  </section>

  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br>Développé par Ibrahima Khalilou llah Sylla - Licence 2 TDSI</p>
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

  <script src="./vendor/jquery/jquery.min.js"></script>
  <script src="./vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/isotope.min.js"></script>
  <!-- <script src="./assets/js/owl-carousel.js"></script> -->
  <script src="./assets/js/tabs.js"></script>
  <script src="./assets/js/swiper.js"></script>
  <script src="./assets/js/custom.js"></script>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Navigation fluide vers les ancres
      document.querySelectorAll('.scroll-to-section a').forEach(link => {
        link.addEventListener('click', function (e) {
          const href = this.getAttribute('href');

          if (href.includes('#')) {
            e.preventDefault();
            const targetId = href.split('#')[1];
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
              window.scrollTo({
                top: targetElement.offsetTop - 80,
                behavior: 'smooth'
              });

              // Mettre à jour l'URL
              history.pushState(null, null, href);

              // Mettre à jour l'état actif du menu
              document.querySelectorAll('.nav li a').forEach(a => a.classList.remove('active'));
              this.classList.add('active');
            }
          }
        });
      });

      // Détection de la section active au scroll
      function updateActiveSection() {
        const sections = document.querySelectorAll('section[id]');
        const scrollPos = window.scrollY + 100;

        sections.forEach(section => {
          const top = section.offsetTop;
          const bottom = top + section.offsetHeight;

          if (scrollPos >= top && scrollPos < bottom) {
            const id = section.getAttribute('id');
            document.querySelectorAll('.nav li a').forEach(a => {
              a.classList.remove('active');
              if (a.getAttribute('href') === `index.php#${id}`) {
                a.classList.add('active');
              }
            });
          }
        });
      }

      window.addEventListener('scroll', updateActiveSection);
      updateActiveSection(); // Initial call
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const urlParams = new URLSearchParams(window.location.search);

      if (urlParams.has('success')) {
        showNotification('Inscription réussie ! Vous pouvez maintenant vous connecter.', 'success');
      }

      if (urlParams.has('error')) {
        showNotification('Une erreur est survenue lors de l\'inscription. Veuillez réessayer.', 'error');
      }

      // Afficher les messages PHP depuis la session
      showSessionMessages();

      // Gestion du formulaire d'inscription
      const registerForm = document.getElementById('register-form');
      if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
          const email = document.getElementById('email').value;
          const motDePasse = document.getElementById('mot_de_passe').value;
          const confirmation = document.getElementById('confirmation_mot_de_passe').value;

          // Validation email
          if (!isValidEmail(email)) {
            e.preventDefault();
            showNotification('Veuillez entrer une adresse email valide.', 'error');
            return false;
          }

          if (motDePasse !== confirmation) {
            e.preventDefault();
            showNotification('Les mots de passe ne correspondent pas.', 'error');
            return false;
          }

          if (motDePasse.length < 6) {
            e.preventDefault();
            showNotification('Le mot de passe doit contenir au moins 6 caractères.', 'error');
            return false;
          }
        });
      }
    });

    // Fonction pour afficher les messages de session PHP
    function showSessionMessages() {
      <?php if (isset($_SESSION['register_errors'])): ?>
        const errors = <?php echo json_encode($_SESSION['register_errors']); ?>;
        const isEmailExists = <?php echo isset($_SESSION['email_exists_error']) ? 'true' : 'false'; ?>;

        // Afficher chaque erreur
        errors.forEach(error => {
          showNotification(error, 'error');
        });

        // Si c'est une erreur d'email existant, ajouter un bouton de connexion
        if (isEmailExists) {
          const email = "<?php echo isset($_SESSION['old_register_data']['email']) ? $_SESSION['old_register_data']['email'] : ''; ?>";
          showEmailExistsNotification(email);
        }

        // Nettoyer la session
        <?php
        unset($_SESSION['register_errors']);
        unset($_SESSION['old_register_data']);
        unset($_SESSION['email_exists_error']);
        ?>
      <?php endif; ?>
    }

    // Fonction spéciale pour l'erreur d'email existant
    function showEmailExistsNotification(email) {
      const notification = document.createElement('div');
      notification.className = 'notification notification-warning';
      notification.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
          <div style="flex: 1;">
            <span>Un compte existe déjà avec cet email (${email}).</span>
          </div>
          <div style="display: flex; align-items: center;">
            <button onclick="redirectToLogin()" class="login-redirect-btn">
              Se connecter
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

    // Fonction pour rediriger vers la page de connexion
    function redirectToLogin() {
      window.location.href = 'connexion.php';
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
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      console.log('DOM chargé - Initialisation du script...');

      // ===== FONCTIONS D'AIDE =====
      function checkFileExists(url) {
        return new Promise((resolve) => {
          fetch(url, { method: 'HEAD' })
            .then(response => resolve(response.ok))
            .catch(() => resolve(false));
        });
      }

      function downloadMissingFile(filename, content, type = 'text/javascript') {
        const blob = new Blob([content], { type });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        console.log(`Fichier ${filename} téléchargé`);
      }

      // ===== CORRECTION DES CHEMINS DE RESSOURCES =====
      async function fixResourcePaths() {
        console.log('Correction des chemins de ressources...');

        // Configuration des chemins attendus
        const resourcesConfig = {
          basePaths: ['./', '../', '../../', '/'],
          resources: {
            'bootstrap.css': [
              'vendor/bootstrap/css/bootstrap.min.css',
              'assets/vendor/bootstrap/css/bootstrap.min.css',
              'bootstrap/css/bootstrap.min.css'
            ],
            'bootstrap.js': [
              'vendor/bootstrap/js/bootstrap.bundle.min.js',
              'assets/vendor/bootstrap/js/bootstrap.bundle.min.js',
              'bootstrap/js/bootstrap.bundle.min.js'
            ],
            'fontawesome': [
              'vendor/fontawesome/css/all.min.css',
              'assets/css/fontawesome.css',
              'fontawesome/css/all.min.css'
            ],
            'template.css': [
              'assets/css/templatemo-574-mexant.css',
              'templatemo-574-mexant.css',
              'css/templatemo-574-mexant.css'
            ],
            'logo.png': [
              'assets/images/tdsi-ai-logo.png',
              'images/tdsi-ai-logo.png',
              'tdsi-ai-logo.png'
            ],
            'header-bg.png': [
              'assets/images/header-bg.png',
              'images/header-bg.png',
              'header-bg.png'
            ]
          }
        };

        // Vérifier et corriger chaque type de ressource
        for (const [resourceType, paths] of Object.entries(resourcesConfig.resources)) {
          let found = false;

          for (const basePath of resourcesConfig.basePaths) {
            if (found) break;

            for (const path of paths) {
              const fullPath = basePath + path;

              if (await checkFileExists(fullPath)) {
                console.log(`✓ ${resourceType} trouvé à: ${fullPath}`);
                updateResourceElements(resourceType, fullPath);
                found = true;
                break;
              }
            }
          }

          if (!found) {
            console.log(`✗ ${resourceType} non trouvé`);
            handleMissingResource(resourceType);
          }
        }
      }

      function updateResourceElements(resourceType, correctPath) {
        const selectors = {
          'bootstrap.css': 'link[href*="bootstrap"][rel="stylesheet"]',
          'bootstrap.js': 'script[src*="bootstrap.bundle.min.js"]',
          'fontawesome': 'link[href*="fontawesome"]',
          'template.css': 'link[href*="templatemo-574-mexant.css"]',
          'logo.png': 'img[src*="tdsi-ai-logo.png"]',
          'header-bg.png': '.header-bg, .main-banner'
        };

        const selector = selectors[resourceType];
        if (!selector) return;

        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
          const attr = element.tagName === 'LINK' ? 'href' :
            element.tagName === 'SCRIPT' ? 'src' :
              element.tagName === 'IMG' ? 'src' : null;

          if (attr && element.getAttribute(attr) !== correctPath) {
            console.log(`Mise à jour ${resourceType}: ${element.getAttribute(attr)} -> ${correctPath}`);
            element.setAttribute(attr, correctPath);
          }
        });
      }

      function handleMissingResource(resourceType) {
        switch (resourceType) {
          case 'bootstrap.css':
            // Charger Bootstrap CSS depuis CDN
            const bootstrapCSS = document.createElement('link');
            bootstrapCSS.rel = 'stylesheet';
            bootstrapCSS.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css';
            bootstrapCSS.integrity = 'sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM';
            bootstrapCSS.crossOrigin = 'anonymous';
            document.head.appendChild(bootstrapCSS);
            console.log('Bootstrap CSS chargé depuis CDN');
            break;

          case 'bootstrap.js':
            // Charger Bootstrap JS depuis CDN
            const bootstrapJS = document.createElement('script');
            bootstrapJS.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
            bootstrapJS.integrity = 'sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz';
            bootstrapJS.crossOrigin = 'anonymous';
            document.body.appendChild(bootstrapJS);
            console.log('Bootstrap JS chargé depuis CDN');
            break;

          case 'fontawesome':
            // Charger FontAwesome depuis CDN
            const fontAwesome = document.createElement('link');
            fontAwesome.rel = 'stylesheet';
            fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
            fontAwesome.integrity = 'sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==';
            fontAwesome.crossOrigin = 'anonymous';
            document.head.appendChild(fontAwesome);
            console.log('FontAwesome chargé depuis CDN');
            break;

          case 'header-bg.png':
            // Créer un fond de secours avec CSS
            const style = document.createElement('style');
            style.textContent = `
              .main-banner::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(135deg, #0a1a3a 0%, #133ebe 50%, #1e4fd6 100%);
                z-index: -1;
              }
              .header-area::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(29deg, #0d1e55, #133ebe);
                z-index: -1;
              }
            `;
            document.head.appendChild(style);
            console.log('Fond de secours créé pour header-bg.png');
            break;
        }
      }

      // ===== INITIALISATION DES COMPOSANTS BOOTSTRAP =====
      function initBootstrapComponents() {
        // Vérifier si Bootstrap est chargé
        if (typeof bootstrap === 'undefined') {
          console.log('Bootstrap non chargé, réessayer dans 1 seconde...');
          setTimeout(initBootstrapComponents, 1000);
          return;
        }

        console.log('Bootstrap chargé, initialisation des composants...');

        // Initialiser les tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        if (tooltips.length > 0) {
          tooltips.forEach(el => {
            new bootstrap.Tooltip(el);
          });
          console.log(`${tooltips.length} tooltips initialisés`);
        }

        // Initialiser les popovers
        const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
        if (popovers.length > 0) {
          popovers.forEach(el => {
            new bootstrap.Popover(el);
          });
          console.log(`${popovers.length} popovers initialisés`);
        }

        // Initialiser les dropdowns
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        if (dropdowns.length > 0) {
          dropdowns.forEach(el => {
            new bootstrap.Dropdown(el);
          });
          console.log(`${dropdowns.length} dropdowns initialisés`);
        }

        // Initialiser les modals
        const modals = document.querySelectorAll('.modal');
        if (modals.length > 0) {
          modals.forEach(el => {
            const modal = new bootstrap.Modal(el);
            // Gérer les boutons d'ouverture
            const trigger = document.querySelector(`[data-bs-target="#${el.id}"]`);
            if (trigger) {
              trigger.addEventListener('click', () => modal.show());
            }
          });
          console.log(`${modals.length} modals initialisés`);
        }

        // Initialiser les carousels
        const carousels = document.querySelectorAll('.carousel');
        if (carousels.length > 0) {
          carousels.forEach(el => {
            new bootstrap.Carousel(el);
          });
          console.log(`${carousels.length} carousels initialisés`);
        }
      }

      // ===== CORRECTION DES ERREURS ISOTOPE =====
      function fixIsotopeErrors() {
        console.log('Correction des erreurs Isotope...');

        // Vérifier si jQuery est chargé
        if (typeof jQuery === 'undefined') {
          console.log('jQuery non chargé, chargement depuis CDN...');
          loadjQuery().then(() => {
            loadIsotope();
          });
        } else {
          loadIsotope();
        }
      }

      function loadjQuery() {
        return new Promise((resolve) => {
          if (typeof jQuery !== 'undefined') {
            resolve();
            return;
          }

          const script = document.createElement('script');
          script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
          script.integrity = 'sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=';
          script.crossOrigin = 'anonymous';

          script.onload = function () {
            console.log('jQuery chargé depuis CDN');
            resolve();
          };

          script.onerror = function () {
            console.log('Erreur lors du chargement de jQuery');
            resolve();
          };

          document.head.appendChild(script);
        });
      }

      function loadIsotope() {
        if (typeof jQuery.fn.isotope !== 'undefined') {
          console.log('Isotope déjà chargé');
          return;
        }

        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js';
        script.integrity = 'sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdnqbrM1hmVdGzyeCa1DgM3X5Q4A/Is9xA1IkbUeDd7755dNNI/PzSf2Pew==';
        script.crossOrigin = 'anonymous';

        script.onload = function () {
          console.log('Isotope chargé depuis CDN');
          fixNavigationLinks();
        };

        script.onerror = function () {
          console.log('Erreur lors du chargement de Isotope');
        };

        document.head.appendChild(script);
      }

      function fixNavigationLinks() {
        // Corriger tous les liens de navigation problématiques
        const navLinks = document.querySelectorAll('a[href*="../index.php"], a[href*="#"]');

        navLinks.forEach(link => {
          const originalHref = link.getAttribute('href');

          // Corriger les liens avec ../index.php
          if (originalHref && originalHref.includes('../index.php')) {
            const newHref = originalHref.replace('../index.php', 'index.php');
            link.setAttribute('href', newHref);
            console.log(`Correction lien: ${originalHref} -> ${newHref}`);
          }

          // Ajouter le comportement de défilement lisse
          link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href && href.includes('#')) {
              const targetId = href.split('#')[1];
              const targetElement = document.getElementById(targetId);

              if (targetElement) {
                e.preventDefault();
                window.scrollTo({
                  top: targetElement.offsetTop - 100,
                  behavior: 'smooth'
                });

                // Mettre à jour l'URL sans recharger la page
                history.pushState(null, null, href);
              }
            }
          });
        });
      }

      // ===== ANIMATION DES STATISTIQUES =====
      function animateStats() {
        const statNumbers = document.querySelectorAll('.stat-number');
        if (statNumbers.length === 0) return;

        console.log('Animation des statistiques démarrée');

        statNumbers.forEach(stat => {
          const target = parseInt(stat.getAttribute('data-count'));
          const suffix = target >= 500 ? '+' : '%';
          const duration = 2000;
          const step = target / (duration / 16);
          let current = 0;

          const updateNumber = () => {
            current += step;
            if (current >= target) {
              current = target;
              clearInterval(timer);
            }
            stat.textContent = Math.floor(current) + suffix;
          };

          const timer = setInterval(updateNumber, 16);
        });
      }

      // ===== PARTICULES CANVAS =====
      function initParticles() {
        const canvas = document.getElementById('particleCanvas');
        if (!canvas) {
          console.log('Canvas non trouvé, création...');
          createParticleCanvas();
          return;
        }

        if (!canvas.getContext) {
          console.log('Canvas non supporté');
          return;
        }

        try {
          const ctx = canvas.getContext('2d');
          canvas.width = canvas.offsetWidth || window.innerWidth;
          canvas.height = canvas.offsetHeight || 300;

          const particles = [];
          const particleCount = Math.min(50, Math.floor(window.innerWidth / 20));

          class Particle {
            constructor() {
              this.x = Math.random() * canvas.width;
              this.y = Math.random() * canvas.height;
              this.size = Math.random() * 2 + 0.5;
              this.speedX = Math.random() * 1 - 0.5;
              this.speedY = Math.random() * 1 - 0.5;
              this.color = Math.random() > 0.5 ? '#ffc527' : '#2e59d9';
              this.opacity = Math.random() * 0.3 + 0.1;
            }

            update() {
              this.x += this.speedX;
              this.y += this.speedY;

              if (this.x > canvas.width) this.x = 0;
              if (this.x < 0) this.x = canvas.width;
              if (this.y > canvas.height) this.y = 0;
              if (this.y < 0) this.y = canvas.height;
            }

            draw() {
              ctx.beginPath();
              ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
              ctx.fillStyle = this.color;
              ctx.globalAlpha = this.opacity;
              ctx.fill();
              ctx.closePath();
            }
          }

          function initParticleArray() {
            particles.length = 0;
            for (let i = 0; i < particleCount; i++) {
              particles.push(new Particle());
            }
          }

          let animationId = null;

          function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < particles.length; i++) {
              for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 100) {
                  ctx.beginPath();
                  ctx.strokeStyle = 'rgba(255, 197, 39, 0.1)';
                  ctx.lineWidth = 0.5;
                  ctx.moveTo(particles[i].x, particles[i].y);
                  ctx.lineTo(particles[j].x, particles[j].y);
                  ctx.stroke();
                  ctx.closePath();
                }
              }

              particles[i].update();
              particles[i].draw();
            }

            animationId = requestAnimationFrame(animate);
          }

          initParticleArray();
          animate();

          // Gestion du redimensionnement
          window.addEventListener('resize', () => {
            canvas.width = canvas.offsetWidth || window.innerWidth;
            canvas.height = canvas.offsetHeight || 300;
            initParticleArray();
          });

          console.log('Particules canvas initialisées');

        } catch (error) {
          console.log('Erreur canvas:', error);
        }
      }

      function createParticleCanvas() {
        const particleSystem = document.querySelector('.particle-system');
        if (!particleSystem) return;

        const canvas = document.createElement('canvas');
        canvas.id = 'particleCanvas';
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        canvas.style.display = 'block';
        canvas.style.position = 'absolute';
        canvas.style.top = '0';
        canvas.style.left = '0';
        canvas.style.zIndex = '1';

        particleSystem.appendChild(canvas);

        // Redémarrer l'initialisation
        setTimeout(initParticles, 100);
      }

      // ===== ÉTAPES D'INITIALISATION =====
      async function initializeApp() {
        console.log('=== DÉMARRAGE DE L\'INITIALISATION ===');

        // 1. Corriger les chemins de ressources
        await fixResourcePaths();

        // 2. Initialiser les statistiques
        animateStats();

        // 3. Initialiser les particules
        setTimeout(initParticles, 500);

        // 4. Corriger les erreurs Isotope
        setTimeout(fixIsotopeErrors, 1000);

        // 5. Initialiser Bootstrap
        setTimeout(initBootstrapComponents, 1500);

        // 6. Configuration finale
        setTimeout(() => {
          console.log('=== INITIALISATION TERMINÉE ===');
          console.log('Résumé:');
          console.log(`- Statistiques: ${document.querySelectorAll('.stat-number').length}`);
          console.log(`- Icônes 3D: ${document.querySelectorAll('.icon-3d').length}`);
          console.log(`- Nœuds réseau: ${document.querySelectorAll('.network-node').length}`);
          console.log(`- Canvas: ${document.getElementById('particleCanvas') ? 'OK' : 'Non trouvé'}`);
          console.log(`- Bootstrap: ${typeof bootstrap !== 'undefined' ? 'OK' : 'Non chargé'}`);
          console.log(`- jQuery: ${typeof jQuery !== 'undefined' ? 'OK' : 'Non chargé'}`);
        }, 2000);
      }

      // ===== DÉMARRER L'APPLICATION =====
      initializeApp();

      // ===== GESTION DES ERREURS GLOBALES =====
      window.addEventListener('error', function (e) {
        console.warn('Erreur globale:', e.message);
        e.preventDefault();
        return false;
      }, false);

    });
  </script>
</body>

</html>