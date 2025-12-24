<?php
session_start();

// Inclure les fichiers nécessaires
require_once 'includes/config.php';
require_once 'includes/check_auth.php';
require_once 'includes/fonctions_bibliotheque.php';
require_once 'includes/fonctions_historique.php';

// Vérifier si l'utilisateur est connecté et récupérer ses données
if (!isset($_SESSION['user_id'])) {
  header('Location: connexion.php');
  exit;
}

// Récupérer les informations complètes de l'utilisateur
$utilisateur = obtenirUtilisateurConnecte();
$user_id = $utilisateur['id_utilisateur'];

// Fonction pour récupérer les vraies statistiques
function getStatistiquesUtilisateur($user_id)
{
  global $pdo;

  $stats = [
    'conversations' => 0,
    'cours_favoris' => 0,
    'cours_suivis' => 0,
    'progression' => 0,
    'dernieres_activites' => []
  ];

  try {
    // 1. Nombre de conversations
    $sql = "SELECT COUNT(*) as total FROM conversation WHERE id_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $stats['conversations'] = $stmt->fetch()['total'];

    // 2. Nombre de cours favoris (basé sur la table favoris)
    $sql = "SELECT COUNT(*) as total FROM favoris 
            WHERE id_utilisateur = ? AND type_favori = 'matiere'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $stats['cours_favoris'] = $stmt->fetch()['total'];

    // 3. Nombre de cours suivis (matières avec progression)
    $sql = "SELECT COUNT(DISTINCT m.id_matiere) as total 
            FROM matiere m
            INNER JOIN chapitre c ON m.id_matiere = c.id_matiere
            WHERE EXISTS (
                SELECT 1 FROM vue_chapitre vc 
                WHERE vc.id_chapitre = c.id_chapitre 
                AND vc.id_utilisateur = ?
            )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $stats['cours_suivis'] = $stmt->fetch()['total'] ?? 0;

    // 4. Progression réelle basée sur les chapitres consultés
    $sql = "SELECT 
                COUNT(DISTINCT vc.id_chapitre) as chapitres_vus,
                COUNT(DISTINCT c.id_chapitre) as total_chapitres
            FROM matiere m
            INNER JOIN chapitre c ON m.id_matiere = c.id_matiere
            LEFT JOIN vue_chapitre vc ON c.id_chapitre = vc.id_chapitre 
                AND vc.id_utilisateur = ?
            WHERE m.id_utilisateur IS NULL OR m.id_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id]);
    $result = $stmt->fetch();

    if ($result && $result['total_chapitres'] > 0) {
      $stats['progression'] = round(($result['chapitres_vus'] / $result['total_chapitres']) * 100);
    } else {
      $stats['progression'] = 0;
    }

    // 5. Dernières activités
    $stats['dernieres_activites'] = getDernieresActivites($user_id);

  } catch (PDOException $e) {
    error_log("Erreur récupération statistiques: " . $e->getMessage());
  }

  return $stats;
}

// Fonction pour récupérer les dernières activités (optimisée)
function getDernieresActivites($user_id)
{
  global $pdo;

  $activites = [];

  try {
    // Requête unique pour toutes les activités
    $sql = "(SELECT 'chat' as type, 
                    CONCAT('Discussion: ', LEFT(COALESCE(m.message_contenu, 'Nouvelle conversation'), 30), '...') as description,
                    c.date_debut as date_activite,
                    'fas fa-comment' as icon,
                    c.date_debut as order_date
            FROM conversation c
            LEFT JOIN message m ON c.id_conversation = m.id_conversation
            WHERE c.id_utilisateur = ?
            ORDER BY c.date_debut DESC
            LIMIT 2)
            
            UNION ALL
            
            (SELECT 'cours' as type,
                    CONCAT('Cours: ', m.nom_matiere) as description,
                    MAX(vc.date_vue) as date_activite,
                    'fas fa-book' as icon,
                    MAX(vc.date_vue) as order_date
            FROM vue_chapitre vc
            JOIN chapitre c ON vc.id_chapitre = c.id_chapitre
            JOIN matiere m ON c.id_matiere = m.id_matiere
            WHERE vc.id_utilisateur = ?
            GROUP BY m.id_matiere
            ORDER BY MAX(vc.date_vue) DESC
            LIMIT 2)
            
            UNION ALL
            
            (SELECT 'favori' as type,
                    CONCAT('Favori: ', m.nom_matiere) as description,
                    f.date_favoris as date_activite,
                    'fas fa-star' as icon,
                    f.date_favoris as order_date
            FROM favoris f
            JOIN matiere m ON f.id_cible = m.id_matiere
            WHERE f.id_utilisateur = ? AND f.type_favori = 'matiere'
            ORDER BY f.date_favoris DESC
            LIMIT 2)
            
            ORDER BY order_date DESC
            LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id, $user_id]);

    $allActivites = $stmt->fetchAll();

    // Formater les dates
    foreach ($allActivites as &$activite) {
      $activite['date'] = date('d/m/Y H:i', strtotime($activite['date_activite']));
    }

    return $allActivites;

  } catch (PDOException $e) {
    error_log("Erreur récupération activités: " . $e->getMessage());
    return [];
  }
}

// Récupérer les cours favoris
function getCoursFavorisDetails($user_id)
{
  global $pdo;

  try {
    $sql = "SELECT m.*, n.nom_level, n.code_level, 
                    (SELECT COUNT(*) FROM chapitre WHERE id_matiere = m.id_matiere) as nb_chapitres,
                    (SELECT COUNT(*) FROM ressource r 
                     JOIN chapitre c ON r.id_chapitre = c.id_chapitre 
                     WHERE c.id_matiere = m.id_matiere) as nb_ressources,
                    f.date_favoris as date_ajout
            FROM favoris f
            JOIN matiere m ON f.id_cible = m.id_matiere
            JOIN niveau n ON m.niveau_id = n.id_niveau
            WHERE f.id_utilisateur = ? 
            AND f.type_favori = 'matiere'
            ORDER BY f.date_favoris DESC
            LIMIT 3";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);

    return $stmt->fetchAll();
  } catch (PDOException $e) {
    error_log("Erreur récupération favoris: " . $e->getMessage());
    return [];
  }
}

// Récupérer les données
$stats = getStatistiquesUtilisateur($user_id);
$cours_favoris = getCoursFavorisDetails($user_id);

// Statistiques supplémentaires pour la section d'accueil
$stats_accueil = [
  'chapitres_termines' => 0,
  'conversations_jour' => 0,
  'temps_total' => "0h 30m",
  'objectifs_atteints' => 3
];
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

  <title>tdsi.ai - Tableau de Bord</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/fontawesome.css">
  <link rel="stylesheet" href="./assets/css/animate.css">
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/owl.css">
  <link rel="stylesheet" href="assets/css/animate.css">
  <link rel="stylesheet" href="assets/css/animations.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css">
  <link rel="stylesheet" href="CSS/index_connecte.css">

</head>

<body>

  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky header-bleu">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <!-- ***** Logo Start ***** -->
            <a href="index_connecte.php" class="logo">
              <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
            </a>
            <!-- ***** Logo End ***** -->
            <!-- ***** Menu Start ***** -->
            <ul class="nav">
              <li class="scroll-to-section"><a href="index_connecte.php" class="active"><i class="fa fa-home"></i>
                  Accueil</a></li>
              <li class="scroll-to-section"><a href="chatbot.php"><i class="fas fa-comment"></i> Chatbot</a></li>
              <li class="scroll-to-section"><a href="Bibliotheque.php"><i class="fas fa-book-open"></i> Bibliothèque</a>
              </li>
              <li class="scroll-to-section"><a href="mes_cours.php"><i class="fas fa-star"></i> Mes Cours</a></li>
              <li class="scroll-to-section"><a href="historique.php"><i class="fas fa-history"></i> Historique</a></li>

              <!-- ***** LIEN ADMIN ***** -->
              <?php if ($utilisateur['role'] === 'admin'): ?>
                <li class="scroll-to-section"><a href="admin.php"><i class="fas fa-cog"></i> Administration</a></li>
              <?php endif; ?>

              <li class="dropdown">
                <a href="#" class="dropdown-toggle user-menu">
                  <div class="user-avatar">
                    <img
                      src="https://ui-avatars.com/api/?name=<?php echo urlencode($utilisateur['prenom'] . '+' . $utilisateur['nom']); ?>&background=ffffff&color=133ebe&size=32"
                      alt="avatar">
                  </div>
                  <span class="user-name"><?php echo htmlspecialchars($utilisateur['prenom']); ?></span>
                  <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                  <li class="dropdown-header">
                    <div class="user-info">
                      <strong><?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?></strong>
                      <span><?php echo htmlspecialchars($utilisateur['email']); ?></span>
                      <small>Niveau :
                        <?php echo htmlspecialchars($utilisateur['nom_level'] ?? $utilisateur['code_level']); ?></small>
                    </div>
                  </li>
                  <li class="divider"></li>
                  <li><a href="#" onclick="commencerNouvelleConversation()"><i class="fas fa-plus"></i> Nouveau chat</a>
                  </li>
                  <li><a href="#" onclick="ouvrirParametres()"><i class="fas fa-cog"></i>
                      Paramètres</a></li>
                  <li class="divider"></li>
                  <li><a href="includes/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i>
                      Déconnexion</a></li>
                </ul>
              </li>
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
              <h2 class="main-title">Bonjour <span
                  class="highlight"><?php echo htmlspecialchars($utilisateur['prenom']); ?></span>, bienvenue sur votre
                <span class="animated-text">tableau de bord</span> TDSI.ai
              </h2>
              <div class="div-dec animated-line"></div>
              <p class="fade-in-text">Niveau :
                <strong><?php echo htmlspecialchars($utilisateur['nom_level'] ?? $utilisateur['code_level']); ?></strong>
                | Dernière connexion : <strong><?php echo date('d/m/Y'); ?></strong>
              </p>
              <p class="fade-in-text">tdsi.ai révolutionne l'apprentissage en Transmission de Données et Sécurité de
                l'Information avec une IA spécialisée, des ressources pédagogiques avancées et un accompagnement
                personnalisé pour votre réussite académique.</p>

              <div class="buttons">
                <div class="green-button pulse-animation">
                  <a href="chatbot.php">
                    <i class="fas fa-robot"></i>
                    Discuter avec l'IA
                  </a>
                </div>
                <div class="orange-button glow-animation">
                  <a href="Bibliotheque.php">
                    <i class="fas fa-book"></i>
                    Explorer la Bibliothèque
                  </a>
                </div>
              </div>
            </div>
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
  </div>
  <!-- ***** Main Banner Area End ***** -->

  <!-- Section Dashboard - Vos Outils d'Apprentissage -->
  <section class="services" id="dashboard">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="section-heading">
            <h6>Vos Outils d'Apprentissage</h6>
            <h4>Explorez Nos Fonctionnalités</h4>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-4">
          <div class="dashboard-card card-chatbot">
            <i class="fas fa-robot"></i>
            <h4>Assistant IA</h4>
            <p>Discutez avec notre IA spécialisée TDSI. Vous avez déjà <?php echo $stats['conversations']; ?>
              conversation<?php echo $stats['conversations'] > 1 ? 's' : ''; ?>.</p>
            <a href="chatbot.php" class="btn-tool mt-3">Commencer</a>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="dashboard-card card-bibliotheque">
            <i class="fas fa-book"></i>
            <h4>Bibliothèque Complète</h4>
            <p>Accédez à tous les cours et ressources. <?php echo $stats['cours_suivis']; ?> cours suivis.</p>
            <a href="Bibliotheque.php" class="btn-tool mt-3">Explorer</a>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="dashboard-card card-favoris">
            <i class="fas fa-star"></i>
            <h4>Mes Favoris</h4>
            <p>Retrouvez vos <?php echo $stats['cours_favoris']; ?> cours préférés en un clic.</p>
            <a href="mes_cours.php" class="btn-tool mt-3">Voir les favoris</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section Cours Favoris -->
  <?php if (!empty($cours_favoris)): ?>
    <section class="favorite-courses" id="favorites">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="section-heading">
              <h6>Vos Cours Favoris</h6>
              <h4>Retrouvez Vos Préférés</h4>
            </div>
          </div>
        </div>

        <div class="row">
          <?php foreach ($cours_favoris as $cours): ?>
            <div class="col-lg-4">
              <div class="course-item">
                <div class="course-icon">
                  <?php
                  $icon = getIconForMatiere($cours['nom_matiere']);
                  echo "<i class='$icon'></i>";
                  ?>
                </div>
                <div class="course-info">
                  <h5><?php echo htmlspecialchars($cours['nom_matiere']); ?></h5>
                  <div class="course-meta">
                    <span><i class="fas fa-layer-group"></i> <?php echo $cours['nb_chapitres'] ?? 0; ?> chapitres</span>
                    <span><i class="fas fa-file"></i> <?php echo $cours['nb_ressources'] ?? 0; ?> ressources</span>
                  </div>
                  <div class="course-footer">
                    <span class="badge badge-primary"><?php echo htmlspecialchars($cours['nom_level']); ?></span>
                    <a href="cours_detail.php?id=<?php echo $cours['id_matiere']; ?>" class="btn-view">Voir</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($stats['cours_favoris'] > 3): ?>
          <div class="row mt-4">
            <div class="col-lg-12 text-center">
              <a href="mes_cours.php" class="btn btn-outline-primary">Voir tous mes favoris
                (<?php echo $stats['cours_favoris']; ?>)</a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>


  <!-- Section Progression améliorée -->
  <section class="services" id="progression">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="section-heading">
            <h6>Votre Progression</h6>
            <h4>Suivez Votre Avancement</h4>
          </div>
        </div>
      </div>

      <div class="row justify-content-center">
        <!-- Cette colonne vide va pousser le contenu au centre -->
        <div class="col-lg-1"></div>

        <div class="col-lg-3">
          <div class="progress-card">
            <div class="progress-header">
              <i class="fas fa-chart-line"></i>
              <h5>Niveau Actuel</h5>
            </div>
            <div class="progress-value">
              <?php echo htmlspecialchars($utilisateur['nom_level'] ?? $utilisateur['code_level']); ?>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar" style="width: 100%;"></div>
            </div>
            <p>Niveau de l'année en cours</p>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="progress-card">
            <div class="progress-header">
              <i class="fas fa-star"></i>
              <h5>Engagement</h5>
            </div>
            <div class="progress-value"><?php echo $stats['cours_favoris']; ?> cours</div>
            <div class="progress-bar-container">
              <div class="progress-bar" style="width: <?php echo min($stats['cours_favoris'] * 20, 100); ?>%"></div>
            </div>
            <p>Basé sur vos cours favoris</p>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="progress-card">
            <div class="progress-header">
              <i class="fas fa-comments"></i>
              <h5>Interaction IA</h5>
            </div>
            <div class="progress-value"><?php echo $stats['conversations']; ?> conv</div>
            <div class="progress-bar-container">
              <div class="progress-bar" style="width: <?php echo min($stats['conversations'] * 20, 100); ?>%"></div>
            </div>
            <p>Nombre de conversations</p>
          </div>
        </div>

        <!-- Cette colonne vide va pousser le contenu au centre -->
        <div class="col-lg-1"></div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <div class="user-actions">
            <div class="green-button">
              <a href="chatbot.php?nouvelle_conversation=1">
                <i class="fas fa-plus-circle"></i>
                Nouvelle Conversation
              </a>
            </div>
            <div class="orange-button">
              <a href="Bibliotheque.php">
                <i class="fas fa-graduation-cap"></i>
                Continuer l'Apprentissage
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section Créateur (corrigée) -->
  <section class="testimonials" id="testimonials">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
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
          <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br>Développé par Ibrahima Khalilou llah Sylla -
            Licence 2 TDSI</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/swiper.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/script.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
      // Animation des statistiques utilisateur
      function animateCounter(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            current = target;
            clearInterval(timer);
          }
          element.textContent = Math.floor(current) + (element.id.includes('progression') ? '%' : '');
        }, 30);
      }

      // Animer les statistiques utilisateur (conversations, cours, favoris, progression)
      const statsElements = {
        'stats-conversations': <?php echo $stats['conversations']; ?>,
        'stats-cours': <?php echo $stats['cours_suivis']; ?>,
        'stats-favoris': <?php echo $stats['cours_favoris']; ?>,
        'stats-progression': <?php echo $stats['progression']; ?>
      };

      for (const [id, target] of Object.entries(statsElements)) {
        const element = document.getElementById(id);
        if (element) {
          animateCounter(element, target);
        }
      }

      // Animation des statistiques fixes (92%, 1900, 500)
      function animateStats() {
        const statNumbers = document.querySelectorAll('.stat-number[data-count]');
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

      // Appeler l'animation des statistiques fixes
      animateStats();

      // Navigation fluide
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
              history.pushState(null, null, href);
              document.querySelectorAll('.nav li a').forEach(a => a.classList.remove('active'));
              this.classList.add('active');
            }
          }
        });
      });

      // Initialiser le carousel des témoignages
      if ($('.owl-testimonials').length) {
        $('.owl-testimonials').owlCarousel({
          loop: true,
          nav: false,
          dots: true,
          items: 1,
          margin: 30,
          autoplay: true,
          smartSpeed: 700,
          autoplayTimeout: 6000,
          responsive: {
            0: {
              items: 1,
              margin: 0
            },
            768: {
              items: 1
            },
            992: {
              items: 1
            }
          }
        });
      }

      // Fonction pour nouvelle conversation
      window.commencerNouvelleConversation = function () {
        if (confirm('Voulez-vous commencer une nouvelle conversation ?')) {
          window.location.href = 'chatbot.php?nouvelle_conversation=1';
        }
      };

      // Fonction pour ouvrir les paramètres
      window.ouvrirParametres = function () {
        alert('Page paramètres à venir !');
      };

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
              if (a.getAttribute('href') === `index_connecte.php#${id}`) {
                a.classList.add('active');
              }
            });
          }
        });
      }

      // Initialiser la détection de section
      window.addEventListener('scroll', updateActiveSection);
      updateActiveSection();
    });
  </script>
</body>

</html>