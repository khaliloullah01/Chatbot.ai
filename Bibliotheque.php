<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bibliothèque - tdsi.ai</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="CSS/style.css">
  <link rel="stylesheet" href="CSS/bibliotheque.css">
</head>
<body>

  <!-- Bouton menu principal -->
  <button class="bouton-menu-principal" id="btn-menu-principal">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Icone de l'application (visible quand barre masquée) -->
  <div class="icone-appli" id="icone-appli">
    <i class="fas fa-graduation-cap"></i>
    <span>tdsi.ai</span>
  </div>

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

       <h4><i class="fas fa-th"></i> Mes outils</h4>
      <ul>
          <li><a href="index.php"><i class="fas fa-comment"></i> tdsi chatbot</a></li>
          <li><a href="#"><i class="fas fa-search"></i> Historique</a></li>
          <li><a href="Programme.php"><i class="fas fa-laptop-code"></i> Mon programme</a></li>
          <li><a href="Bibliotheque.php" class="active"><i class="fas fa-book-open"></i> Bibliothèque</a></li>
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
    <div class="en-tete-page">
      <h1><i class="fas fa-book-open me-2"></i>Bibliothèque de cours</h1>
      <div class="search-box">
        <input type="text" id="search-input" placeholder="Rechercher un cours...">
        <i class="fas fa-search"></i>
      </div>
    </div>
    
    <div class="filtres">
      <button class="btn-filtre active" data-niveau="tous">Tous les niveaux</button>
      <button class="btn-filtre licence1" data-niveau="licence1">Licence 1</button>
      <button class="btn-filtre licence2" data-niveau="licence2">Licence 2</button>
      <button class="btn-filtre licence3" data-niveau="licence3">Licence 3</button>
      <button class="btn-filtre master1" data-niveau="master1">Master 1</button>
      <button class="btn-filtre master2" data-niveau="master2">Master 2</button>
    </div>
    
    <div class="niveaux-container" id="niveaux-container">
      <!-- Les contenus seront chargés dynamiquement par mon js -->
    </div>
  </div>

  <script src="JS/bibliotheque.js"></script>
  <script>
    // Toggle barre latérale
    document.getElementById('btn-menu-principal').addEventListener('click', function() {
      const barreLaterale = document.getElementById('barre-laterale');
      const iconeAppli = document.getElementById('icone-appli');
      const icon = this.querySelector('i');
      
      barreLaterale.classList.toggle('masquee');
      
      if (barreLaterale.classList.contains('masquee')) {
        icon.className = 'fas fa-bars';
        iconeAppli.style.display = 'flex';
      } else {
        icon.className = 'fas fa-times';
        iconeAppli.style.display = 'none';
      }
    });
  </script>
</body>
</html>