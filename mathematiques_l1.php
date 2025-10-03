<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mathématiques L1 - tdsi.ai</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="CSS/style.css">
  <link rel="stylesheet" href="CSS/cours_details.css">
</head>
<body>

  <!-- Bouton menu mobile -->
  <button class="bouton-menu" id="btn-menu-mobile">
    <i class="fas fa-bars"></i>
  </button>

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
      <div class="breadcrumb">
        <a href="Bibliotheque.php"><i class="fas fa-arrow-left"></i> Retour à la bibliothèque</a>
      </div>
      <h1><i class="fas fa-calculator me-2"></i>Mathématiques Fondamentales - Licence 1</h1>
    </div>
    
    <div class="matieres-container">
      <!-- Section Analyse -->
      <div class="matiere-section">
        <h2 class="matiere-titre"><i class="fas fa-chart-line me-2"></i>Analyse</h2>
        <div class="cours-list">
          <div class="cours-item">
            <div class="cours-icon">
              <i class="fas fa-file-pdf"></i>
            </div>
            <div class="cours-info">
              <h3>Limites et Continuité</h3>
              <p>Notions fondamentales de limites et continuité des fonctions</p>
            </div>
            <div class="cours-actions">
              <a href="#" class="btn-action btn-voir"><i class="fas fa-eye"></i> Voir</a>
              <a href="#" class="btn-action btn-telecharger"><i class="fas fa-download"></i></a>
            </div>
          </div>
          
          <div class="cours-item">
            <div class="cours-icon">
              <i class="fas fa-file-pdf"></i>
            </div>
            <div class="cours-info">
              <h3>Nombres réels et Nombres complexes</h3>
              <p>Propriétés des nombres réels et introduction aux nombres complexes</p>
            </div>
            <div class="cours-actions">
              <a href="#" class="btn-action btn-voir"><i class="fas fa-eye"></i> Voir</a>
              <a href="#" class="btn-action btn-telecharger"><i class="fas fa-download"></i></a>
            </div>
          </div>
          
          <div class="cours-item">
            <div class="cours-icon">
              <i class="fas fa-file-pdf"></i>
            </div>
            <div class="cours-info">
              <h3>Notions de Topologie dans &real;, &complexes; et &reals;<sup>n</sup></h3>
              <p>Concepts de base de topologie dans différents espaces</p>
            </div>
            <div class="cours-actions">
              <a href="#" class="btn-action btn-voir"><i class="fas fa-eye"></i> Voir</a>
              <a href="#" class="btn-action btn-telecharger"><i class="fas fa-download"></i></a>
            </div>
          </div>
          
          <div class="cours-item">
            <div class="cours-icon">
              <i class="fas fa-file-pdf"></i>
            </div>
            <div class="cours-info">
                <h3>Suites à valeurs dans &real;, &complexes; et &reals;<sup>n</sup></h3>
              <p>Étude des suites dans différents espaces vectoriels</p>
            </div>
            <div class="cours-actions">
              <a href="#" class="btn-action btn-voir"><i class="fas fa-eye"></i> Voir</a>
              <a href="#" class="btn-action btn-telecharger"><i class="fas fa-download"></i></a>
            </div>
          </div>
          
          <div class="cours-item">
            <div class="cours-icon">
              <i class="fas fa-file-pdf"></i>
            </div>
            <div class="cours-info">
              <h3>Développements limités</h3>
              <p>Approximations locales de fonctions par des polynômes</p>
            </div>
            <div class="cours-actions">
              <a href="#" class="btn-action btn-voir"><i class="fas fa-eye"></i> Voir</a>
              <a href="#" class="btn-action btn-telecharger"><i class="fas fa-download"></i></a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Section Algèbre -->
      <div class="matiere-section">
        <h2 class="matiere-titre"><i class="fas fa-square-root-alt me-2"></i>Algèbre</h2>
        <div class="cours-list">
          <div class="cours-item">
            <div class="cours-icon">
              <i class="fas fa-file-pdf"></i>
            </div>
            <div class="cours-info">
              <h3>Espaces vectoriels</h3>
              <p>Définition et propriétés des espaces vectoriels</p>
            </div>
            <div class="cours-actions">
              <a href="#" class="btn-action btn-voir"><i class="fas fa-eye"></i> Voir</a>
              <a href="#" class="btn-action btn-telecharger"><i class="fas fa-download"></i></a>
            </div>
          </div>
          
          <div class="cours-item">
            <div class="cours-icon">
              <i class="fas fa-file-pdf"></i>
            </div>
            <div class="cours-info">
              <h3>Matrices</h3>
              <p>Opérations sur les matrices et applications</p>
            </div>
            <div class="cours-actions">
              <a href="#" class="btn-action btn-voir"><i class="fas fa-eye"></i> Voir</a>
              <a href="#" class="btn-action btn-telecharger"><i class="fas fa-download"></i></a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script src="JS/cours_details.js"></script>
</body>
</html>