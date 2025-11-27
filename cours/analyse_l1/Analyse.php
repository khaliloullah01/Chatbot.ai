<?php
$titreMatiere = "Analyse";
$nomFichier = "analyse";
$iconeMatiere = "fas fa-chart-line";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $titreMatiere; ?> - tdsi.ai</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../CSS/style.css">
  <link rel="stylesheet" href="../../CSS/cours_details.css">
  <link rel="stylesheet" href="CSS/analyse.css">
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
          <li><a href="../../index.php"><i class="fas fa-comment"></i> tdsi chatbot</a></li>
          <li><a href="#"><i class="fas fa-search"></i> Historique</a></li>
          <li><a href="../../Programme.php"><i class="fas fa-laptop-code"></i> Mon programme</a></li>
          <li><a href="../../Bibliotheque.php"class="active"><i class="fas fa-book-open"></i> Bibliothèque</a></li>
          <li><a href="../../Cours.php"><i class="fas fa-book"></i> Mes cours</a></li>
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
    <div class="breadcrumb">
      <a href="../../Bibliotheque.php">
        <i class="fas fa-arrow-left"></i> Retour à la bibliothèque
      </a>
    </div>

    <div class="matiere-header">
      <h1 class="matiere-titre">
        <i class="<?php echo $iconeMatiere; ?>"></i>
        <?php echo $titreMatiere; ?>
      </h1>
    </div>
    
    <div class="cours-container" id="cours-container">
      <!-- Les sections Cours et TP seront chargées dynamiquement -->
    </div>

  </div>

  <script src="../../JS/bibliotheque.js"></script>
  <script src="JS/analyse.js"></script>
</body>
</html>