// Données des cours d'Algèbre L1
const coursAlgebre = [
  {
    id: "espaces-vectoriels",
    titre: "Chapitre 5 - Espaces Vectoriels",
    description: "Définition et propriétés des espaces vectoriels, sous-espaces vectoriels",
    fichier: "http://chatbot/cours/algebres_l1/Cours/Chapitre 5 Espaces vectoriels.pdf",
    type: "cours",
    date: "10-10-2025",
    taille: "3.8 MB",
    chapitre: 5
  },
  {
    id: "matrices",
    titre: "Chapitre 6 - Matrices",
    description: "Opérations sur les matrices, déterminants, inversion de matrices",
    fichier: "http://chatbot/cours/algebres_l1/Cours/chap6_Matrices.pdf",
    type: "cours",
    date: "10-10-2025",
    taille: "4.2 MB",
    chapitre: 6
  }
];

// Données des TD d'Algèbre L1
const tdAlgebre = [
  {
    id: "td1-logique",
    titre: "TD 1 - Logique et Raisonnement Mathématique",
    description: "Exercices sur la logique propositionnelle et les méthodes de raisonnement",
    fichier: "http://chatbot/cours/algebres_l1/Cours/TD1-LogiqueetRaisonnementMath%c3%a9matique.pdf",
    type: "td",
    date: "10-10-2025",
    taille: "2.1 MB",
    chapitre: 1
  },
  {
    id: "td2-ensemble-application",
    titre: "TD 2 - Ensembles, Applications et Relations",
    description: "Problèmes sur les ensembles, applications et relations binaires",
    fichier: "http://chatbot/cours/algebres_l1/Cours/TD2_ensemble_application_relations.pdf",
    type: "td",
    date: "10-10-2025",
    taille: "2.4 MB",
    chapitre: 2
  },
  {
    id: "td3-groupes",
    titre: "TD 3 - Groupes",
    description: "Exercices sur les groupes, sous-groupes et morphismes de groupes",
    fichier: "http://chatbot/cours/algebres_l1/Cours/TD3_Groupes.pdf",
    type: "td",
    date: "10-10-2025",
    taille: "2.7 MB",
    chapitre: 3
  },
  {
    id: "td5-espaces-vectoriels",
    titre: "TD 5 - Espaces Vectoriels",
    description: "Travaux dirigés sur les espaces vectoriels et applications linéaires",
    fichier: "http://chatbot/cours/algebres_l1/Cours/TD5_Espace_Vectorielle.pdf",
    type: "td",
    date: "10-10-2025",
    taille: "3.1 MB",
    chapitre: 5
  },
  {
    id: "td6-applications-lineaires",
    titre: "TD 6 - Applications Linéaires",
    description: "Exercices sur les applications linéaires, noyau et image",
    fichier: "http://chatbot/cours/algebres_l1/Cours/TD6_applLineaire-1.pdf",
    type: "td",
    date: "10-10-2025",
    taille: "2.9 MB",
    chapitre: 6
  }
];

// Variables globales
let pdfCourant = '';
let pdfTitreCourant = '';

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
  afficherCoursEtTD();
  configurerMenuMobile();
  ajouterStylesSupplementaires();
});

function afficherCoursEtTD() {
  const container = document.getElementById('cours-container');
  container.innerHTML = '';

  // Section Cours
  const sectionCours = creerSection('Cours', 'cours');
  const coursTries = [...coursAlgebre].sort((a, b) => a.chapitre - b.chapitre);
  coursTries.forEach((cours, index) => {
    const element = creerElementCours(cours, index);
    sectionCours.querySelector('.section-content').appendChild(element);
  });
  container.appendChild(sectionCours);

  // Section TD
  const sectionTD = creerSection('Travaux Dirigés', 'td');
  const tdTries = [...tdAlgebre].sort((a, b) => a.chapitre - b.chapitre);
  tdTries.forEach((td, index) => {
    const element = creerElementCours(td, index);
    sectionTD.querySelector('.section-content').appendChild(element);
  });
  container.appendChild(sectionTD);
}

function creerSection(titre, type) {
  const section = document.createElement('div');
  section.classList.add('matiere-section');
  section.innerHTML = `
    <h2 class="section-titre">
      <i class="fas fa-${getIconeType(type)}"></i>
      ${titre}
    </h2>
    <div class="section-content" data-type="${type}">
      <!-- Les éléments seront ajoutés ici -->
    </div>
  `;
  return section;
}

function creerElementCours(cours, index) {
  const div = document.createElement('div');
  div.classList.add('cours-item');
  div.setAttribute('data-chapitre', cours.chapitre);
  div.setAttribute('data-type', cours.type);
  div.style.animationDelay = `${index * 0.1}s`;
  
  div.innerHTML = `
    <div class="cours-icon">
      <i class="fas fa-${getIconeType(cours.type)}"></i>
    </div>
    <div class="cours-info">
      <h3>${cours.titre}</h3>
      <p>${cours.description}</p>
      <div class="meta-cours">
        <span><i class="far fa-calendar"></i> ${cours.date}</span>
        <span><i class="far fa-file"></i> ${cours.taille}</span>
        <span class="badge-cours ${cours.type}">${getBadgeText(cours.type)} ${cours.chapitre}</span>
      </div>
    </div>
    <div class="cours-actions">
      <button class="btn-action btn-voir" onclick="voirPDF('${cours.fichier}', '${cours.titre}')">
        <i class="far fa-eye"></i> Voir le PDF
      </button>
      <button class="btn-action btn-telecharger" onclick="telechargerPDF('${cours.fichier}', '${cours.titre}')">
        <i class="fas fa-download"></i> Télécharger
      </button>
    </div>
  `;
  
  return div;
}

function getIconeType(type) {
  const icones = {
    'cours': 'book',
    'td': 'pencil-alt',
    'tp': 'flask',
    'examen': 'file-alt'
  };
  return icones[type] || 'file';
}

function getBadgeText(type) {
  const badges = {
    'cours': 'Chapitre',
    'td': 'TD',
    'tp': 'TP',
    'examen': 'Examen'
  };
  return badges[type] || 'Chapitre';
}

function configurerMenuMobile() {
  document.getElementById('btn-menu-mobile').addEventListener('click', function() {
    document.getElementById('barre-laterale').classList.toggle('active');
  });
}

function voirPDF(fichier, titre) {
  // Stocker les informations courantes
  pdfCourant = fichier;
  pdfTitreCourant = titre;
  
  // Afficher le viewer en plein écran
  afficherViewerPleinEcran(titre, fichier);
  
  // Feedback visuel
  afficherNotification(`📖 Ouverture de "${titre}"`, 'info');
}

function afficherViewerPleinEcran(titre, fichier) {
  // Créer le conteneur plein écran
  const pleinEcran = document.createElement('div');
  pleinEcran.id = 'pdf-plein-ecran';
  pleinEcran.innerHTML = `
    <div class="pdf-plein-ecran-header">
      <div class="pdf-plein-ecran-titre">
        <i class="fas fa-file-pdf"></i>
        <h2>${titre}</h2>
      </div>
      <div class="pdf-plein-ecran-controls">
        <button class="btn-pdf-control btn-pdf-telecharger" onclick="telechargerPDF('${fichier}', '${titre}')">
          <i class="fas fa-download"></i> Télécharger
        </button>
        <button class="btn-pdf-control btn-pdf-fermer" onclick="fermerPDFPleinEcran()">
          <i class="fas fa-times"></i> Fermer
        </button>
      </div>
    </div>
    <div class="pdf-plein-ecran-content">
      <iframe src="${fichier}" class="pdf-plein-ecran-iframe"></iframe>
    </div>
  `;
  
  // Ajouter au body
  document.body.appendChild(pleinEcran);
  
  // Empêcher le défilement de la page principale
  document.body.style.overflow = 'hidden';
}

function fermerPDFPleinEcran() {
  const pleinEcran = document.getElementById('pdf-plein-ecran');
  if (pleinEcran) {
    pleinEcran.style.animation = 'fadeOut 0.3s ease';
    setTimeout(() => {
      document.body.removeChild(pleinEcran);
      document.body.style.overflow = '';
    }, 300);
  }
}

function telechargerPDF(fichier, titre) {
  // Créer un lien de téléchargement
  const link = document.createElement('a');
  link.href = fichier;
  link.download = titre + '.pdf';
  link.style.display = 'none';
  
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  
  // Feedback visuel
  afficherNotification(`📥 Téléchargement de "${titre}" démarré`, 'success');
}

function afficherNotification(message, type = 'info') {
  // Supprimer les notifications existantes
  const existingNotifications = document.querySelectorAll('.notification-toast');
  existingNotifications.forEach(notif => notif.remove());
  
  // Créer une notification toast
  const toast = document.createElement('div');
  toast.classList.add('notification-toast');
  
  // Couleurs selon le type
  const colors = {
    'info': '#4e73df',
    'success': '#1cc88a',
    'warning': '#f6c23e',
    'error': '#e74a3b'
  };
  
  const icons = {
    'info': 'ℹ️',
    'success': '✅',
    'warning': '⚠️',
    'error': '❌'
  };
  
  toast.style.background = colors[type] || colors.info;
  toast.innerHTML = `<span class="notification-icon">${icons[type]}</span> ${message}`;
  
  document.body.appendChild(toast);
  
  // Animation d'entrée
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  setTimeout(() => {
    toast.classList.remove('show');
    toast.classList.add('hide');
    setTimeout(() => {
      if (document.body.contains(toast)) {
        document.body.removeChild(toast);
      }
    }, 300);
  }, 3000);
}

function ajouterStylesSupplementaires() {
  const styles = document.createElement('style');
  styles.textContent = `
    /* Styles pour le viewer plein écran */
    #pdf-plein-ecran {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: white;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      animation: fadeIn 0.3s ease;
    }
    
    .pdf-plein-ecran-header {
      background: var(--couleur-principale);
      color: white;
      padding: 15px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      z-index: 10000;
    }
    
    .pdf-plein-ecran-titre {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .pdf-plein-ecran-titre h2 {
      margin: 0;
      font-size: 1.2rem;
      font-weight: 600;
    }
    
    .pdf-plein-ecran-titre i {
      font-size: 1.4rem;
    }
    
    .pdf-plein-ecran-controls {
      display: flex;
      gap: 12px;
    }
    
    .btn-pdf-control {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
    }
    
    .btn-pdf-telecharger {
      background: #1cc88a;
      color: white;
    }
    
    .btn-pdf-telecharger:hover {
      background: #17a673;
      transform: translateY(-2px);
    }
    
    .btn-pdf-fermer {
      background: #e74a3b;
      color: white;
    }
    
    .btn-pdf-fermer:hover {
      background: #d63a2b;
      transform: translateY(-2px);
    }
    
    .pdf-plein-ecran-content {
      flex: 1;
      display: flex;
      background: #f8f9fc;
    }
    
    .pdf-plein-ecran-iframe {
      width: 100%;
      height: 100%;
      border: none;
      background: white;
    }
    
    /* Styles pour les notifications */
    .notification-toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #4e73df;
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 10001;
      transform: translateX(100%);
      opacity: 0;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 10px;
      max-width: 400px;
    }
    
    .notification-toast.show {
      transform: translateX(0);
      opacity: 1;
    }
    
    .notification-toast.hide {
      transform: translateX(100%);
      opacity: 0;
    }
    
    .notification-icon {
      font-size: 1.1rem;
    }
    
    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; }
    }
    
    /* Styles pour la liste des cours */
    .meta-cours {
      display: flex;
      gap: 15px;
      font-size: 0.85rem;
      color: #858796;
      margin-top: 8px;
      flex-wrap: wrap;
    }
    
    .badge-cours {
      background: rgba(78, 115, 223, 0.1);
      color: #4e73df;
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .pdf-plein-ecran-header {
        padding: 12px 15px;
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
      }
      
      .pdf-plein-ecran-controls {
        width: 100%;
        justify-content: space-between;
      }
      
      .btn-pdf-control {
        flex: 1;
        justify-content: center;
        padding: 8px 15px;
        font-size: 0.85rem;
      }
      
      .pdf-plein-ecran-titre h2 {
        font-size: 1rem;
      }
      
      .notification-toast {
        right: 10px;
        left: 10px;
        max-width: none;
      }
    }
  `;
  
  document.head.appendChild(styles);
}

// Gestion de la fermeture avec la touche Échap
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    fermerPDFPleinEcran();
  }
});

// Fonction pour filtrer par chapitre (extension future)
function filtrerParChapitre(chapitre) {
  const tousLesCours = document.querySelectorAll('.cours-item');
  tousLesCours.forEach(cours => {
    if (chapitre === 'tous' || cours.getAttribute('data-chapitre') === chapitre.toString()) {
      cours.style.display = 'flex';
    } else {
      cours.style.display = 'none';
    }
  });
}