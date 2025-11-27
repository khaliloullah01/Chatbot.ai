<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mes cours - tdsi.ai</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/cours.css">
  <link rel="stylesheet" href="CSS/style.css">
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
        <li><a href="Bibliotheque.php"><i class="fas fa-book-open"></i> Bibliothèque</a></li>
        <li><a href="Cours.php" class="active"><i class="fas fa-book"></i> Mes cours</a></li>
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
      <h1><i class="fas fa-book me-2"></i>Mes cours</h1>
      <div>
        <button class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Nouveau cours
        </button>
      </div>
    </div>
    
    <div class="filtres">
      <button class="btn-filtre active">Tous</button>
      <button class="btn-filtre">En cours</button>
      <button class="btn-filtre">Terminés</button>
      <button class="btn-filtre">Mathématiques</button>
      <button class="btn-filtre">Informatique</button>
    </div>
    
    <div class="grille-cours">
      <!-- Cours 1 -->
      <div class="carte-cours">
        <div class="en-tete-carte">
          <i class="fas fa-calculator"></i>
        </div>
        <div class="corps-carte">
          <h3>Algèbre Linéaire</h3>
          <p class="description-cours">Concepts fondamentaux des espaces vectoriels, applications linéaires et matrices.</p>
          <div class="details-cours">
            <span>12 leçons</span>
            <span class="badge-statut badge-progress">En cours</span>
          </div>
          <div class="actions-cours">
            <a href="#" class="btn-action btn-continuer">Continuer</a>
            <a href="#" class="btn-action btn-details">Détails</a>
          </div>
        </div>
      </div>
      
      <!-- Cours 2 -->
      <div class="carte-cours">
        <div class="en-tete-carte">
          <i class="fas fa-code"></i>
        </div>
        <div class="corps-carte">
          <h3>Algorithmique Avancée</h3>
          <p class="description-cours">Structures de données complexes et analyse d'algorithmes.</p>
          <div class="details-cours">
            <span>18 leçons</span>
            <span class="badge-statut badge-complete">Terminé</span>
          </div>
          <div class="actions-cours">
            <a href="#" class="btn-action btn-continuer">Revoir</a>
            <a href="#" class="btn-action btn-details">Détails</a>
          </div>
        </div>
      </div>
      
      <!-- Cours 3 -->
      <div class="carte-cours">
        <div class="en-tete-carte">
          <i class="fas fa-chart-bar"></i>
        </div>
        <div class="corps-carte">
          <h3>Probabilités et Statistiques</h3>
          <p class="description-cours">Théorie des probabilités et méthodes statistiques pour l'analyse de données.</p>
          <div class="details-cours">
            <span>15 leçons</span>
            <span class="badge-statut badge-not-started">Non commencé</span>
          </div>
          <div class="actions-cours">
            <a href="#" class="btn-action btn-continuer">Commencer</a>
            <a href="#" class="btn-action btn-details">Détails</a>
          </div>
        </div>
      </div>
      
      <!-- Cours 4 -->
      <div class="carte-cours">
        <div class="en-tete-carte">
          <i class="fas fa-database"></i>
        </div>
        <div class="corps-carte">
          <h3>Bases de Données</h3>
          <p class="description-cours">Conception, implémentation et gestion des systèmes de bases de données.</p>
          <div class="details-cours">
            <span>10 leçons</span>
            <span class="badge-statut badge-progress">En cours</span>
          </div>
          <div class="actions-cours">
            <a href="#" class="btn-action btn-continuer">Continuer</a>
            <a href="#" class="btn-action btn-details">Détails</a>
          </div>
        </div>
      </div>
      
      <!-- Cours 5 -->
      <div class="carte-cours">
        <div class="en-tete-carte">
          <i class="fas fa-brain"></i>
        </div>
        <div class="corps-carte">
          <h3>Intelligence Artificielle</h3>
          <p class="description-cours">Fondements de l'IA, apprentissage automatique et réseaux neuronaux.</p>
          <div class="details-cours">
            <span>20 leçons</span>
            <span class="badge-statut badge-not-started">Non commencé</span>
          </div>
          <div class="actions-cours">
            <a href="#" class="btn-action btn-continuer">Commencer</a>
            <a href="#" class="btn-action btn-details">Détails</a>
          </div>
        </div>
      </div>
      
      <!-- Cours 6 -->
      <div class="carte-cours">
        <div class="en-tete-carte">
          <i class="fas fa-network-wired"></i>
        </div>
        <div class="corps-carte">
          <h3>Réseaux et Sécurité</h3>
          <p class="description-cours">Architecture des réseaux, protocoles et principes de cybersécurité.</p>
          <div class="details-cours">
            <span>14 leçons</span>
            <span class="badge-statut badge-complete">Terminé</span>
          </div>
          <div class="actions-cours">
            <a href="#" class="btn-action btn-continuer">Revoir</a>
            <a href="#" class="btn-action btn-details">Détails</a>
          </div>
        </div>
      </div>
    </div>
  </div>

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