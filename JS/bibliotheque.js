// Données des cours par niveau
const coursParNiveau = {
  licence1: {
    titre: "Licence 1",
    cours: [
      {
        id: "algebres",
        nom: "Algèbres",
        description: "Cours d'algèbre fondamentale et avancée",
        icon: "fas fa-square-root-alt",
        fichiers: 12,
        couleur: "licence1",
        dossier: "algebres_l1"
      },
      {
        id: "algorithmes",
        nom: "Algorithmes",
        description: "Fondements de l'algorithmique et structures de données",
        icon: "fas fa-project-diagram",
        fichiers: 10,
        couleur: "licence1",
        dossier: "Algorithmes"
      },
      {
        id: "analyse",
        nom: "Analyse",
        description: "Analyse mathématique et calcul différentiel",
        icon: "fas fa-chart-line",
        fichiers: 5,
        couleur: "licence1",
        dossier: "analyse_l1"
      },
      {
        id: "archi-pc",
        nom: "Architecture des Ordinateurs",
        description: "Architecture et fonctionnement des ordinateurs",
        icon: "fas fa-microchip",
        fichiers: 7,
        couleur: "licence1",
        dossier: "Architecture_Ordinateurs"
      },
      {
        id: "sql",
        nom: "Base De Donnée SQL",
        description: "Introduction aux bases de données et SQL",
        icon: "fas fa-database",
        fichiers: 9,
        couleur: "licence1",
        dossier: "Base_Donnees_SQL"
      },
      {
        id: "crypto",
        nom: "Cryptologie",
        description: "Principes de cryptographie et sécurité",
        icon: "fas fa-lock",
        fichiers: 8,
        couleur: "licence1",
        dossier: "Cryptologie"
      },
      {
        id: "electricite",
        nom: "Électricité",
        description: "Bases de l'électricité et électronique",
        icon: "fas fa-bolt",
        fichiers: 7,
        couleur: "licence1",
        dossier: "Electricite"
      },
      {
        id: "html-css",
        nom: "Développement web",
        description: "Développement web avec HTML et CSS",
        icon: "fas fa-code",
        fichiers: 10,
        couleur: "licence1",
        dossier: "Developpement_Web"
      },
      {
        id: "java",
        nom: "Java",
        description: "Programmation orientée objet avec Java",
        icon: "fas fa-coffee",
        fichiers: 12,
        couleur: "licence1",
        dossier: "Java"
      },
      {
        id: "langage-c",
        nom: "Language C",
        description: "Programmation en langage C pour TDSI",
        icon: "fas fa-copyright",
        fichiers: 11,
        couleur: "licence1",
        dossier: "Langage_C"
      },
      {
        id: "linux",
        nom: "Linux",
        description: "Système d'exploitation Linux et ligne de commande",
        icon: "fas fa-terminal",
        fichiers: 8,
        couleur: "licence1",
        dossier: "Linux"
      },
      {
        id: "reseaux",
        nom: "Réseaux informatiques",
        description: "Fondements des réseaux et protocoles",
        icon: "fas fa-network-wired",
        fichiers: 9,
        couleur: "licence1",
        dossier: "Reseaux_Informatiques"
      },
      {
        id: "systeme",
        nom: "Système D'exploitation",
        description: "Fonctionnement des systèmes d'exploitation",
        icon: "fas fa-desktop",
        fichiers: 8,
        couleur: "licence1",
        dossier: "Systeme_Exploitation"
      },
      {
        id: "analyse-info",
        nom: "Analyse informatique",
        description: "Analyse informatique avec la methode MERISE",
        icon: "fas fa-server",
        fichiers: 7,
        couleur: "licence1",
        dossier: "Analyse_Informatique"
      }
    ]
  },
  licence2: {
    titre: "Licence 2",
    cours: [
      {
        id: "algo-avance",
        nom: "Algorithmique Avancée",
        description: "Structures de données complexes et analyse d'algorithmes",
        icon: "fas fa-project-diagram",
        fichiers: 18,
        couleur: "licence2",
        dossier: "Algorithmique_Avancee"
      },
      {
        id: "bdd-avance",
        nom: "Bases de Données Avancées",
        description: "Conception, implémentation et gestion des systèmes de bases de données",
        icon: "fas fa-database",
        fichiers: 14,
        couleur: "licence2",
        dossier: "Bases_Donnees_Avancees"
      },
      {
        id: "proba-stats",
        nom: "Probabilités et Statistiques",
        description: "Théorie des probabilités et méthodes statistiques pour l'analyse de données",
        icon: "fas fa-chart-bar",
        fichiers: 12,
        couleur: "licence2",
        dossier: "Probabilites_Statistiques"
      },
      {
        id: "reseaux-avance",
        nom: "Réseaux Informatiques Avancés",
        description: "Principes des réseaux, protocoles et architectures réseau",
        icon: "fas fa-network-wired",
        fichiers: 10,
        couleur: "licence2",
        dossier: "Reseaux_Avances"
      },
      {
        id: "web-avance",
        nom: "Développement Web Avancé",
        description: "Création de sites web avec HTML, CSS, JavaScript et technologies associées",
        icon: "fas fa-globe",
        fichiers: 16,
        couleur: "licence2",
        dossier: "Developpement_Web_Avance"
      }
    ]
  },
  licence3: {
    titre: "Licence 3",
    cours: [
      {
        id: "intelligence-artificielle",
        nom: "Intelligence Artificielle",
        description: "Fondements de l'IA, apprentissage automatique et réseaux neuronaux",
        icon: "fas fa-brain",
        fichiers: 20,
        couleur: "licence3",
        dossier: "Intelligence_Artificielle"
      },
      {
        id: "systemes-exploitation",
        nom: "Systèmes d'Exploitation",
        description: "Fonctionnement des systèmes d'exploitation et gestion des processus",
        icon: "fas fa-desktop",
        fichiers: 15,
        couleur: "licence3",
        dossier: "Systemes_Exploitation"
      },
      {
        id: "cryptologie-avance",
        nom: "Cryptologie Avancée",
        description: "Principes de cryptographie et sécurité informatique",
        icon: "fas fa-lock",
        fichiers: 12,
        couleur: "licence3",
        dossier: "Cryptologie_Avancee"
      },
      {
        id: "gestion-projet",
        nom: "Gestion de Projet Informatique",
        description: "Méthodologies de gestion de projet appliquées à l'informatique",
        icon: "fas fa-tasks",
        fichiers: 10,
        couleur: "licence3",
        dossier: "Gestion_Projet"
      }
    ]
  },
  master1: {
    titre: "Master 1",
    cours: [
      {
        id: "big-data",
        nom: "Big Data et Analytics",
        description: "Traitement et analyse de volumes massifs de données",
        icon: "fas fa-chart-line",
        fichiers: 22,
        couleur: "master1",
        dossier: "Big_Data_Analytics"
      },
      {
        id: "cloud-computing",
        nom: "Cloud Computing",
        description: "Architectures cloud, virtualisation et services distants",
        icon: "fas fa-cloud",
        fichiers: 18,
        couleur: "master1",
        dossier: "Cloud_Computing"
      },
      {
        id: "devops",
        nom: "DevOps et Intégration Continue",
        description: "Méthodologies DevOps et automatisation des déploiements",
        icon: "fas fa-cogs",
        fichiers: 15,
        couleur: "master1",
        dossier: "DevOps"
      },
      {
        id: "cybersecurite",
        nom: "Cybersécurité Avancée",
        description: "Techniques avancées de protection des systèmes d'information",
        icon: "fas fa-shield-alt",
        fichiers: 20,
        couleur: "master1",
        dossier: "Cybersecurite_Avancee"
      }
    ]
  },
  master2: {
    titre: "Master 2",
    cours: [
      {
        id: "ia-avance",
        nom: "IA Avancée et Deep Learning",
        description: "Architectures profondes et applications de l'IA moderne",
        icon: "fas fa-robot",
        fichiers: 25,
        couleur: "master2",
        dossier: "IA_Avancee_Deep_Learning"
      },
      {
        id: "blockchain",
        nom: "Blockchain et Technologies Distribuées",
        description: "Principes de la blockchain et applications décentralisées",
        icon: "fas fa-link",
        fichiers: 18,
        couleur: "master2",
        dossier: "Blockchain"
      },
      {
        id: "informatique-quantique",
        nom: "Informatique Quantique",
        description: "Introduction à l'informatique quantique et ses applications",
        icon: "fas fa-atom",
        fichiers: 16,
        couleur: "master2",
        dossier: "Informatique_Quantique"
      },
      {
        id: "management-ti",
        nom: "Management TI et Gouvernance",
        description: "Gestion stratégique des technologies de l'information",
        icon: "fas fa-chart-pie",
        fichiers: 14,
        couleur: "master2",
        dossier: "Management_TI"
      }
    ]
  }
};

// État de l'application
let etatApplication = {
  filtreActuel: 'tous',
  rechercheActuelle: '',
  coursFiltres: []
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
  initialiserApplication();
});

function initialiserApplication() {
  // Charger tous les cours au démarrage
  afficherTousLesCours();
  
  // Configurer les écouteurs d'événements
  configurerFiltres();
  configurerRecherche();
  configurerMenuMobile();
  
  // Mettre à jour l'état
  etatApplication.filtreActuel = 'tous';
}

function configurerFiltres() {
  document.querySelectorAll('.btn-filtre').forEach(btn => {
    btn.addEventListener('click', function() {
      const niveau = this.getAttribute('data-niveau');
      appliquerFiltre(niveau);
    });
  });
}

function configurerRecherche() {
  const searchInput = document.getElementById('search-input');
  searchInput.addEventListener('input', function() {
    const terme = this.value.toLowerCase().trim();
    etatApplication.rechercheActuelle = terme;
    appliquerRecherche(terme);
  });
}

function configurerMenuMobile() {
  document.getElementById('btn-menu-mobile').addEventListener('click', function() {
    document.getElementById('barre-laterale').classList.toggle('active');
  });
}

function appliquerFiltre(niveau) {
  // Mettre à jour l'état
  etatApplication.filtreActuel = niveau;
  
  // Mettre à jour l'interface des filtres
  mettreAJourInterfaceFiltres(niveau);
  
  // Appliquer le filtre
  if (niveau === 'tous') {
    afficherTousLesCours();
  } else {
    afficherCoursParNiveau(niveau);
  }
  
  // Réappliquer la recherche si nécessaire
  if (etatApplication.rechercheActuelle) {
    appliquerRecherche(etatApplication.rechercheActuelle);
  }
}

function appliquerRecherche(terme) {
  const toutesLesCartes = document.querySelectorAll('.carte-cours');
  let aucunResultat = true;
  
  toutesLesCartes.forEach(carte => {
    const nom = carte.getAttribute('data-nom');
    const description = carte.getAttribute('data-description');
    
    if (nom.includes(terme) || description.includes(terme)) {
      carte.style.display = 'block';
      aucunResultat = false;
    } else {
      carte.style.display = 'none';
    }
  });
  
  // Gérer l'affichage des sections et du message d'absence de résultats
  gererAffichageSectionsRecherche(terme, aucunResultat);
}

function mettreAJourInterfaceFiltres(niveau) {
  // Retirer la classe active de tous les boutons
  document.querySelectorAll('.btn-filtre').forEach(b => {
    b.classList.remove('active', 'licence1', 'licence2', 'licence3', 'master1', 'master2');
  });
  
  // Ajouter la classe active au bouton cliqué
  const btnFiltre = document.querySelector(`.btn-filtre[data-niveau="${niveau}"]`);
  if (btnFiltre) {
    if (niveau !== 'tous') {
      btnFiltre.classList.add(niveau, 'active');
    } else {
      btnFiltre.classList.add('active');
    }
  }
}

function gererAffichageSectionsRecherche(terme, aucunResultat) {
  const sections = document.querySelectorAll('.niveau-section');
  let auMoinsUneSectionVisible = false;
  
  sections.forEach(section => {
    const cartesVisibles = section.querySelectorAll('.carte-cours[style="display: block"]');
    if (cartesVisibles.length === 0) {
      section.style.display = 'none';
    } else {
      section.style.display = 'block';
      auMoinsUneSectionVisible = true;
    }
  });
  
  // Afficher ou masquer le message d'absence de résultats
  gererMessageAucunResultat(terme, aucunResultat, auMoinsUneSectionVisible);
}

function gererMessageAucunResultat(terme, aucunResultat, auMoinsUneSectionVisible) {
  const container = document.getElementById('niveaux-container');
  let message = document.getElementById('aucun-resultat');
  
  if (terme && !auMoinsUneSectionVisible) {
    if (!message) {
      message = document.createElement('div');
      message.id = 'aucun-resultat';
      message.classList.add('text-center', 'py-5');
      message.innerHTML = `
        <i class="fas fa-search fa-3x mb-3 text-muted"></i>
        <h4 class="text-muted">Aucun cours trouvé</h4>
        <p class="text-muted">Essayez avec d'autres termes de recherche</p>
        <button class="btn btn-primary mt-3" onclick="reinitialiserRecherche()">
          Réinitialiser la recherche
        </button>
      `;
      container.appendChild(message);
    }
  } else {
    if (message) {
      message.remove();
    }
  }
}

function reinitialiserRecherche() {
  const searchInput = document.getElementById('search-input');
  searchInput.value = '';
  etatApplication.rechercheActuelle = '';
  
  // Réafficher tous les cours selon le filtre actuel
  if (etatApplication.filtreActuel === 'tous') {
    afficherTousLesCours();
  } else {
    afficherCoursParNiveau(etatApplication.filtreActuel);
  }
  
  // Supprimer le message d'absence de résultats
  const message = document.getElementById('aucun-resultat');
  if (message) {
    message.remove();
  }
}

function afficherTousLesCours() {
  const container = document.getElementById('niveaux-container');
  container.innerHTML = '';
  
  for (const niveau in coursParNiveau) {
    const section = creerSectionNiveau(niveau);
    container.appendChild(section);
  }
  
  // Stocker les cours filtrés pour référence
  etatApplication.coursFiltres = obtenirTousLesCours();
}

function afficherCoursParNiveau(niveau) {
  const container = document.getElementById('niveaux-container');
  container.innerHTML = '';
  
  const section = creerSectionNiveau(niveau);
  container.appendChild(section);
  
  // Stocker les cours filtrés pour référence
  etatApplication.coursFiltres = coursParNiveau[niveau].cours;
}

function obtenirTousLesCours() {
  let tousLesCours = [];
  for (const niveau in coursParNiveau) {
    tousLesCours = tousLesCours.concat(coursParNiveau[niveau].cours);
  }
  return tousLesCours;
}

function creerSectionNiveau(niveau) {
  const data = coursParNiveau[niveau];
  const section = document.createElement('div');
  section.classList.add('niveau-section');
  
  const titre = document.createElement('h2');
  titre.classList.add('niveau-titre', niveau);
  titre.innerHTML = `<i class="fas fa-graduation-cap"></i> ${data.titre}`;
  
  const grille = document.createElement('div');
  grille.classList.add('grille-cours');
  
  // Ajouter les cours
  data.cours.forEach(cours => {
    const carte = creerCarteCours(cours);
    grille.appendChild(carte);
  });
  
  section.appendChild(titre);
  section.appendChild(grille);
  
  return section;
}

function creerCarteCours(cours) {
  const carte = document.createElement('div');
  carte.classList.add('carte-cours', cours.couleur);
  carte.setAttribute('data-nom', cours.nom.toLowerCase());
  carte.setAttribute('data-description', cours.description.toLowerCase());
  
  // Générer le chemin vers la page de la matière
  const cheminMatiere = `cours/${cours.dossier}/${genererNomFichier(cours.nom)}.php`;
  
  carte.innerHTML = `
    <div class="en-tete-carte ${cours.couleur}">
      <i class="${cours.icon}"></i>
    </div>
    <div class="corps-carte">
      <h3 class="${cours.couleur}">${cours.nom}</h3>
      <p class="description-cours">${cours.description}</p>
      <div class="details-cours">
        <span>${cours.fichiers} fichiers</span>
      </div>
      <div class="actions-cours">
        <a href="${cheminMatiere}" class="btn-action btn-voir ${cours.couleur}">Voir les cours</a>
        <a href="#" class="btn-action btn-telecharger ${cours.couleur}" onclick="telechargerZipMatiere('${cours.dossier}')">
          <i class="fas fa-download"></i>
        </a>
      </div>
    </div>
  `;
  
  return carte;
}

function genererNomFichier(nomMatiere) {
  return nomMatiere
    .toLowerCase()
    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]/g, '_')
    .replace(/_+/g, '_')
    .replace(/^_|_$/g, '');
}

function telechargerZipMatiere(dossier) {
  // Afficher un indicateur de chargement
  afficherNotification(`📦 Préparation du téléchargement pour ${dossier}...`, 'info');
  
  // Appeler le script PHP pour générer le ZIP
  fetch(`telecharger_zip.php?dossier=${encodeURIComponent(dossier)}`)
    .then(response => {
      if (!response.ok) {
        throw new Error('Erreur lors de la génération du ZIP');
      }
      return response.blob();
    })
    .then(blob => {
      // Créer un lien de téléchargement
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.style.display = 'none';
      a.href = url;
      a.download = `${dossier}_cours_complet.zip`;
      
      document.body.appendChild(a);
      a.click();
      
      // Nettoyer
      window.URL.revokeObjectURL(url);
      document.body.removeChild(a);
      
      afficherNotification(`✅ Téléchargement de ${dossier} terminé !`, 'success');
    })
    .catch(error => {
      console.error('Erreur:', error);
      afficherNotification(`❌ Erreur lors du téléchargement: ${error.message}`, 'error');
    });
}

// Fonction pour afficher les notifications (à ajouter si elle n'existe pas)
function afficherNotification(message, type = 'info') {
  // Créer une notification toast
  const toast = document.createElement('div');
  toast.classList.add('notification-toast', 'bibliotheque-notification');
  
  // Couleurs selon le type
  const colors = {
    'info': '#4e73df',
    'success': '#1cc88a',
    'warning': '#f6c23e',
    'error': '#e74a3b'
  };
  
  const icons = {
    'info': '⏳',
    'success': '✅',
    'warning': '⚠️',
    'error': '❌'
  };
  
  toast.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${colors[type] || colors.info};
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
  `;
  
  toast.innerHTML = `<span class="notification-icon">${icons[type]}</span> ${message}`;
  
  document.body.appendChild(toast);
  
  // Animation d'entrée
  setTimeout(() => {
    toast.style.transform = 'translateX(0)';
    toast.style.opacity = '1';
  }, 10);
  
  // Auto-suppression après 4 secondes
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    toast.style.opacity = '0';
    setTimeout(() => {
      if (document.body.contains(toast)) {
        document.body.removeChild(toast);
      }
    }, 300);
  }, 4000);
}

// Exporter pour utilisation globale (si nécessaire)
window.reinitialiserRecherche = reinitialiserRecherche;
window.telechargerZipMatiere = telechargerZipMatiere;
window.afficherEtatApplication = afficherEtatApplication;