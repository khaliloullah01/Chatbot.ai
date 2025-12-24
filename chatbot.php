<?php
require_once 'includes/check_auth.php';
require_once 'includes/fonctions_historique.php';

// Rediriger si non connecté
redirigerSiNonConnecte();

// Récupérer les infos de l'utilisateur connecté avec vérification
$utilisateur = obtenirUtilisateurConnecte();
$user_id = obtenirUtilisateurId();

// Vérifier que l'utilisateur est bien récupéré
if (!$utilisateur || !$user_id) {
  header('Location: connexion.php');
  exit;
}

// Gérer la demande de nouvelle conversation
if (isset($_GET['nouvelle_conversation']) && $_GET['nouvelle_conversation'] == 1) {
  unset($_SESSION['id_conversation_courante']);
  header('Location: chatbot.php');
  exit;
}

// Gérer le chargement d'une conversation existante
if (isset($_GET['charger_conversation']) && is_numeric($_GET['charger_conversation'])) {
  $id_conversation = $_GET['charger_conversation'];

  // Vérifier que l'utilisateur possède cette conversation
  $conversation = getConversation($id_conversation, $user_id);
  if ($conversation) {
    $_SESSION['id_conversation_courante'] = $id_conversation;
    header('Location: chatbot.php');
    exit;
  }
}

// Gérer la conversation courante
if (!isset($_SESSION['id_conversation_courante'])) {
  $_SESSION['id_conversation_courante'] = creerConversation($user_id);
}

$id_conversation_courante = $_SESSION['id_conversation_courante'];

// Charger les messages existants si ce n'est pas une nouvelle conversation
if (conversationAVecMessages($id_conversation_courante)) {
  $messagesExistants = getMessagesConversation($id_conversation_courante);
} else {
  $messagesExistants = [];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>tdsi.ai - Chatbot</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="CSS/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github-dark.min.css">

  <!-- Ajout de MathJax pour le rendu des formules mathématiques -->
  <!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script> -->
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <!-- Ajout de marked.js pour le traitement du Markdown -->
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <!-- Ajout de highlight.js pour la coloration syntaxique -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/javascript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/java.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/cpp.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/sql.min.js"></script>

  <script>
    window.MathJax = {
      tex: {
        inlineMath: [['\\(', '\\)']],
        displayMath: [['\\[', '\\]']],
        tags: 'ams'
      },
      svg: {
        fontCache: 'global'
      }
    };

    // Initialisation de highlight.js
    document.addEventListener('DOMContentLoaded', function () {
      hljs.highlightAll();
    });
  </script>
</head>

<body>

  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky header-bleu">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <nav class="main-nav">
            <!-- ***** Logo Start ***** -->
            <a href="chatbot.php" class="logo">
              <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
            </a>
            <!-- ***** Logo End ***** -->
            <!-- ***** Menu Start ***** -->
            <ul class="nav">
              <li class="scroll-to-section"><a href="index_connecte.php"><i class="fa fa-home"></i> Acceuil</a></li>
              <li class="scroll-to-section"><a href="chatbot.php" class="active"><i class="fas fa-comment"></i>
                  Chatbot</a></li>
              <li class="scroll-to-section"><a href="Bibliotheque.php"><i class="fas fa-book-open"></i> Bibliothèque</a>
              </li>
              <li class="scroll-to-section"><a href="mes_cours.php"><i class="fas fa-star"></i> Mes Cours</a></li>
              <li class="scroll-to-section"><a href="historique.php"><i class="fas fa-history"></i> Historique</a></li>


              <!-- ***** LIEN ADMIN - AJOUTEZ ICI ***** -->
              <?php if (estAdministrateur()): ?>
                <li class="scroll-to-section"><a href="admin.php"><i class="fas fa-cog"></i> Administration</a></li>
              <?php endif; ?>
              <!-- ***** FIN DU LIEN ADMIN ***** -->

              <li class="dropdown">
                <a href="#" class="dropdown-toggle user-menu">
                  <div class="user-avatar">
                    <img
                      src="https://ui-avatars.com/api/?name=<?php echo urlencode($utilisateur['prenom'] . '+' . $utilisateur['nom']); ?>&background=ffffff&color=133ebe&size=32"
                      alt="avatar">
                  </div>
                  <span class="user-name"><?php echo htmlspecialchars($utilisateur['prenom'] ?? ''); ?></span>
                  <i class="fas fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                  <li class="dropdown-header">
                    <div class="user-info">
                      <strong><?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?></strong>
                      <span><?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></span>
                      <small>Niveau :
                        <?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'Non défini')); ?></small>
                    </div>
                  </li>
                  <li class="divider"></li>
                  <li><a href="#" onclick="commencerNouvelleConversation()"><i class="fas fa-plus"></i> Nouveau chat</a>
                  </li>
                  <li><a href="#" onclick="ouvrirParametres()"><i class="fas fa-cog"></i> Paramètres</a></li>
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

  <!-- Données utilisateur cachées pour JavaScript -->
  <div id="user-data" data-prenom="<?php echo htmlspecialchars($utilisateur['prenom'] ?? ''); ?>"
    data-nom="<?php echo htmlspecialchars($utilisateur['nom'] ?? ''); ?>"
    data-niveau="<?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? '')); ?>"
    data-email="<?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?>" style="display: none;">
  </div>

  <!-- Contenu principal -->
  <div class="contenu-principal-chatbot">

    <!-- En-tête du chatbot -->
    <div class="en-tete-chatbot">
      <div class="info-utilisateur-chatbot">
        <!-- Les boutons sont dans le dropdown -->
      </div>
    </div>

    <!-- Messages de chat -->
    <div class="conteneur-chat" id="boite-chat">
      <?php if (!empty($messagesExistants)): ?>
        <?php foreach ($messagesExistants as $msg): ?>
          <div class="message <?php echo $msg['auteur_id'] ? 'utilisateur' : 'bot'; ?>">
            <div class="contenu-message">
              <?php echo nl2br(htmlspecialchars($msg['message_contenu'])); ?>
            </div>
            <div class="horodatage">
              <?php echo $msg['date_formatee']; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      <!-- Les nouveaux messages seront ajoutés ici dynamiquement -->
    </div>

    <!-- Suggestions de questions -->
    <div class="suggestions-questions" id="suggestions-questions">
      <div class="suggestions-header">
        <span>Questions suggérées pour vous</span>
        <button class="btn-refresh-suggestions" onclick="rafraichirSuggestions()" title="Nouvelles suggestions">
          <i class="fas fa-sync-alt"></i>
        </button>
      </div>
      <div class="suggestions-list" id="suggestions-list">
        <?php
        // Charger les suggestions initiales
        require_once 'includes/questions_suggestions.php';
        $suggestions = getQuestionsSuggestions($utilisateur['nom_level'] ?? 'debutant');
        foreach ($suggestions as $suggestion):
          ?>
          <div class="suggestion-item" onclick="utiliserSuggestion('<?php echo addslashes($suggestion); ?>', this)">
            <?php echo htmlspecialchars($suggestion); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Zone de saisie -->
    <div class="conteneur-saisie">
      <div class="boite-saisie">
        <textarea id="saisie-utilisateur" placeholder="Tapez votre question ici..." rows="1"
          oninput="autoResize(this)"></textarea>
        <div class="boutons-saisie">
          <button id="btn-envoyer" title="Envoyer le message" aria-label="Envoyer le message">
            <i class="fas fa-paper-plane"></i>
          </button>
          <button id="btn-arreter" title="Arrêter la réponse" aria-label="Arrêter la réponse" style="display: none;">
            <i class="fas fa-stop"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Boîte de dialogue de confirmation personnalisée -->
  <div id="confirmation-modal" class="modal-custom">
    <div class="modal-content-custom">
      <div class="modal-header-custom">
        <h5>Nouvelle conversation</h5>
        <span class="close-modal">&times;</span>
      </div>
      <div class="modal-body-custom">
        <p>Voulez-vous vraiment commencer une nouvelle conversation ?</p>
      </div>
      <div class="modal-footer-custom">
        <button id="confirm-cancel" class="btn-modal btn-cancel">Annuler</button>
        <button id="confirm-ok" class="btn-modal btn-confirm">Oui</button>
      </div>
    </div>
  </div>

  <!-- Modal Paramètres -->
  <div id="modal-parametres" class="modal-custom">
    <div class="modal-content-custom modal-large">
      <div class="modal-header-custom">
        <h5><i class="fas fa-cogs"></i> Paramètres</h5>
        <span class="close-modal" onclick="fermerParametres()">&times;</span>
      </div>
      <div class="modal-body-custom">
        <div class="parametre-groupe">
          <h6>Informations du compte</h6>
          <div class="info-compte">
            <p><strong>Nom:</strong>
              <?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></p>
            <p><strong>Niveau:</strong>
              <?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'Non défini')); ?>
            </p>
            <p><strong>Rôle:</strong> <?php echo htmlspecialchars($utilisateur['role'] ?? ''); ?></p>
          </div>
        </div>

        <div class="parametre-groupe">
          <h6>Préférences de l'interface</h6>
          <div class="parametre-option">
            <label class="switch">
              <input type="checkbox" id="auto-scroll" checked>
              <span class="slider round"></span>
            </label>
            <div class="parametre-info">
              <strong>Défilement automatique</strong>
              <span>Descendre automatiquement vers les nouveaux messages</span>
            </div>
          </div>

          <div class="parametre-option">
            <label class="switch">
              <input type="checkbox" id="sons" checked>
              <span class="slider round"></span>
            </label>
            <div class="parametre-info">
              <strong>Sons de notification</strong>
              <span>Jouer un son à la fin de chaque réponse</span>
            </div>
          </div>
        </div>

        <div class="parametre-groupe">
          <h6>Personnalisation</h6>
          <div class="parametre-option">
            <label for="vitesse-frappe" class="parametre-label">
              <strong>Vitesse d'affichage</strong>
              <span>Contrôle la rapidité de l'affichage progressif</span>
            </label>
            <select id="vitesse-frappe" class="parametre-select">
              <option value="rapide">Rapide</option>
              <option value="normal" selected>Normal</option>
              <option value="lent">Lent</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer-custom">
        <button class="btn-modal btn-secondary" onclick="reinitialiserParametres()">Réinitialiser</button>
        <button class="btn-modal btn-confirm" onclick="fermerParametres()">Fermer</button>
      </div>
    </div>
  </div>


  <footer>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br> Développé par Ibrahima Khalilou llah
            Sylla - Licence 2 TDSI </p>
        </div>
      </div>
    </div>
  </footer>


  <script src="JS/script.js"></script>
  <script>
    // Variables globales pour les préférences utilisateur
    // const preferencesUtilisateur = {
    //   prenom: "<?php echo $utilisateur['prenom'] ?? ''; ?>",
    //   nom: "<?php echo $utilisateur['nom'] ?? ''; ?>",
    //   niveau: "<?php echo $utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? ''); ?>",
    //   email: "<?php echo $utilisateur['email'] ?? ''; ?>",
    //   autoScroll: true,
    //   sons: true
    // };

    function ouvrirParametres() {
      document.getElementById('modal-parametres').style.display = 'flex';
    }

    function fermerParametres() {
      document.getElementById('modal-parametres').style.display = 'none';
    }

    // Fonction pour nouvelle conversation depuis le dropdown
    function commencerNouvelleConversation() {
      // Fermer le dropdown
      const dropdowns = document.querySelectorAll('.dropdown-menu');
      dropdowns.forEach(dropdown => {
        dropdown.style.display = 'none';
      });

      // Afficher la modal de confirmation
      const modal = document.getElementById('confirmation-modal');
      modal.style.display = 'flex';

      // Gestionnaire pour le bouton OK
      document.getElementById('confirm-ok').onclick = function () {
        window.location.href = 'chatbot.php?nouvelle_conversation=1';
      };

      // Gestionnaire pour le bouton Annuler
      document.getElementById('confirm-cancel').onclick = function () {
        modal.style.display = 'none';
      };
    }

    // Gestion des clics en dehors des modals pour fermer
    document.getElementById('confirmation-modal').addEventListener('click', function (e) {
      if (e.target === this) {
        this.style.display = 'none';
      }
    });

    document.getElementById('modal-parametres').addEventListener('click', function (e) {
      if (e.target === this) {
        fermerParametres();
      }
    });

    // Gestionnaire pour la croix de fermeture de la modal de confirmation
    document.querySelector('#confirmation-modal .close-modal').addEventListener('click', function () {
      document.getElementById('confirmation-modal').style.display = 'none';
    });
  </script>
</body>

</html>