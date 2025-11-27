<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>tdsi.ai</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="CSS/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github-dark.min.css">
  <!-- Ajout de MathJax pour le rendu des formules mathématiques -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
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
    document.addEventListener('DOMContentLoaded', function() {
      hljs.highlightAll();
    });
  </script>
</head>
<body>

  <!-- Barre latérale -->
  <div class="barre-laterale" id="barre-laterale">
    <div>
      <div class="entete-barre-laterale">
        <h3><i class="fas fa-graduation-cap"></i>tdsi.ai</h3>
        <p>Votre assistant pédagogique</p>
      </div>
      
      <button class="btn-nouvelle-conversation" id="btn-nouvelle-conversation">
        <i class="fas fa-plus"></i> Nouveau chat
      </button>
      
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

      <h4><i class="fas fa-th"></i> Mes outils</h4>
      <ul>
          <li><a href="index.php" class="active"><i class="fas fa-comment"></i> tdsi chatbot</a></li>
          <li><a href="#"><i class="fas fa-search"></i> Historique</a></li>
          <li><a href="Programme.php"><i class="fas fa-laptop-code"></i> Mon programme</a></li>
          <li><a href="Bibliotheque.php"><i class="fas fa-book-open"></i> Bibliothèque</a></li>
          <li><a href="Cours.php"><i class="fas fa-book"></i> Mes cours</a></li>
      </ul>
      
      <h4><i class="fas fa-history"></i> Historique des chats</h4> 
      <p style="font-size: 13px; color: gray;">Aucun historique de chat</p>
    </div>
    
    <div class="profil">
      <div class="contenu-profil">
        <img src="https://ui-avatars.com/api/?name=Khalil+Sylla&background=133ebe&color=fff" alt="photo de profil">
        <div class="info-profil">
          <p class="nom-profil">Khalil Sylla</p>
          <p class="email-profil">khaliloullahsylla@gmail.com</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenu principal -->
  <div class="contenu-principal">
    <!-- En-tête avec bouton menu et icone appli -->
    <div class="en-tete">
      <!-- Bouton menu principal -->
      <button class="bouton-menu-principal" id="btn-menu-principal">
        <i class="fas fa-bars"></i>
      </button>
      
      <!-- Icone de l'application (visible quand barre masquée) -->
      <div class="icone-appli" id="icone-appli">
        <i class="fas fa-graduation-cap"></i>
        <span>tdsi.ai</span>
      </div>
      
      <button title="En savoir plus">
        <i class="fas fa-cogs"></i> En savoir plus
      </button>
    </div>

    <!-- Messages de chat -->
    <div class="conteneur-chat" id="boite-chat">
     
    </div>

    <!-- Zone de saisie -->
    <div class="conteneur-saisie">
      <div class="boite-saisie">
        <textarea 
          id="saisie-utilisateur" 
          placeholder="Tapez votre question ici..." 
          rows="1"
          oninput="autoResize(this)"
        ></textarea>
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

  <script src="JS/script.js"></script>
</body>
</html>