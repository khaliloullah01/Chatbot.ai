<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mon programme - tdsi.ai</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/programme.css">
 
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
      <h1><i class="fas fa-laptop-code me-2"></i>Mon programme</h1>
      <div>
        <button class="btn btn-primary">
          <i class="fas fa-plus me-2"></i>Nouvelle tâche
        </button>
      </div>
    </div>
    
    <div class="stats-programme">
      <div class="carte-stat">
        <div class="titre-stat">Tâches complétées</div>
        <div class="valeur-stat">24/36</div>
        <i class="fas fa-check-circle icone-stat"></i>
      </div>
      
      <div class="carte-stat success">
        <div class="titre-stat">Progression globale</div>
        <div class="valeur-stat">67%</div>
        <i class="fas fa-chart-line icone-stat"></i>
      </div>
      
      <div class="carte-stat warning">
        <div class="titre-stat">Tâches en retard</div>
        <div class="valeur-stat">3</div>
        <i class="fas fa-exclamation-triangle icone-stat"></i>
      </div>
      
      <div class="carte-stat info">
        <div class="titre-stat">Prochain examen</div>
        <div class="valeur-stat">7 j</div>
        <i class="fas fa-calendar-day icone-stat"></i>
      </div>
    </div>
    
    <div class="conteneur-calendrier">
      <div class="section-principale">
        <h2 class="titre-section">Tâches à venir</h2>
        
        <ul class="liste-taches">
          <li class="element-tache important">
            <div class="en-tete-tache">
              <h3 class="titre-tache">Révision pour l'examen d'algèbre</h3>
              <span class="badge-tache badge-important">Important</span>
            </div>
            <p>Réviser les chapitres 4 à 6 sur les espaces vectoriels</p>
            <div class="details-tache">
              <div class="date-tache">
                <i class="far fa-calendar"></i>
                <span>Pour demain, 14:00</span>
              </div>
              <div class="actions-tache">
                <button class="btn-action-tache"><i class="far fa-edit"></i></button>
                <button class="btn-action-tache"><i class="far fa-check-circle"></i></button>
              </div>
            </div>
          </li>
          
          <li class="element-tache">
            <div class="en-tete-tache">
              <h3 class="titre-tache">Exercices de programmation</h3>
            </div>
            <p>Compléter les exercices sur les structures de données</p>
            <div class="details-tache">
              <div class="date-tache">
                <i class="far fa-calendar"></i>
                <span>28 Oct, 16:00</span>
              </div>
              <div class="actions-tache">
                <button class="btn-action-tache"><i class="far fa-edit"></i></button>
                <button class="btn-action-tache"><i class="far fa-check-circle"></i></button>
              </div>
            </div>
          </li>
          
          <li class="element-tache urgence">
            <div class="en-tete-tache">
              <h3 class="titre-tache">Projet de base de données</h3>
              <span class="badge-tache badge-urgence">Urgent</span>
            </div>
            <p>Finaliser le modèle conceptuel de données</p>
            <div class="details-tache">
              <div class="date-tache">
                <i class="far fa-calendar"></i>
                <span>30 Oct, 23:59</span>
              </div>
              <div class="actions-tache">
                <button class="btn-action-tache"><i class="far fa-edit"></i></button>
                <button class="btn-action-tache"><i class="far fa-check-circle"></i></button>
              </div>
            </div>
          </li>
          
          <li class="element-tache termine">
            <div class="en-tete-tache">
              <h3 class="titre-tache">Lecture article IA</h3>
              <span class="badge-tache badge-termine">Terminé</span>
            </div>
            <p>Lire l'article sur les réseaux neuronaux convolutionnels</p>
            <div class="details-tache">
              <div class="date-tache">
                <i class="far fa-calendar"></i>
                <span>22 Oct, 10:00</span>
              </div>
              <div class="actions-tache">
                <button class="btn-action-tache"><i class="far fa-edit"></i></button>
                <button class="btn-action-tache"><i class="far fa-check-circle"></i></button>
              </div>
            </div>
          </li>
        </ul>
      </div>
      
      <div class="cote-droit">
        <div class="carte-progression">
          <h2 class="titre-section">Progression du semestre</h2>
          
          <div class="progression-container">
            <div class="info-progression">
              <span>Algèbre Linéaire</span>
              <span>75%</span>
            </div>
            <div class="barre-progression">
              <div class="progression-remplissage" style="width: 75%"></div>
            </div>
          </div>
          
          <div class="progression-container">
            <div class="info-progression">
              <span>Algorithmique</span>
              <span>90%</span>
            </div>
            <div class="barre-progression">
              <div class="progression-remplissage" style="width: 90%"></div>
            </div>
          </div>
          
          <div class="progression-container">
            <div class="info-progression">
              <span>Bases de données</span>
              <span>60%</span>
            </div>
            <div class="barre-progression">
              <div class="progression-remplissage" style="width: 60%"></div>
            </div>
          </div>
          
          <div class="progression-container">
            <div class="info-progression">
              <span>Intelligence Artificielle</span>
              <span>30%</span>
            </div>
            <div class="barre-progression">
              <div class="progression-remplissage" style="width: 30%"></div>
            </div>
          </div>
        </div>
        
        <div class="carte-progression">
          <h2 class="titre-section">Objectifs quotidiens</h2>
          
          <ul class="objectifs-list">
            <li class="objectif-item">
              <div class="objectif-check checked">
                <i class="fas fa-check fa-xs"></i>
              </div>
              <div class="objectif-text checked">2 heures d'étude</div>
            </li>
            
            <li class="objectif-item">
              <div class="objectif-check checked">
                <i class="fas fa-check fa-xs"></i>
              </div>
              <div class="objectif-text checked">Exercices de mathématiques</div>
            </li>
            
            <li class="objectif-item">
              <div class="objectif-check">
              </div>
              <div class="objectif-text">Pratique programmation</div>
            </li>
            
            <li class="objectif-item">
              <div class="objectif-check">
              </div>
              <div class="objectif-text">Révision flashcards</div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

</body>
</html>