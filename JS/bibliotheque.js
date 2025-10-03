// Données des cours par niveau
const coursParNiveau = {
  licence1: {
    titre: "Licence 1",
    cours: [
      {
        id: "l1-math1",
        nom: "Mathématiques",
        description: "Introduction aux concepts mathématiques de base : algèbre, analyse, géométrie.",
        icon: "fas fa-calculator",
        fichiers: 15,
        couleur: "licence1"
      },
      {
        id: "l1-prog1",
        nom: "Initiation à la Programmation",
        description: "Découverte des bases de la programmation avec Python et algorithmique.",
        icon: "fas fa-code",
        fichiers: 12,
        couleur: "licence1"
      },
      {
        id: "l1-archi",
        nom: "Architecture des Ordinateurs",
        description: "Fonctionnement des composants d'un ordinateur et principes de base.",
        icon: "fas fa-microchip",
        fichiers: 8,
        couleur: "licence1"
      },
      {
        id: "l1-algebre",
        nom: "Algèbre Linéaire",
        description: "Espaces vectoriels, applications linéaires, matrices et déterminants.",
        icon: "fas fa-square-root-alt",
        fichiers: 10,
        couleur: "licence1"
      },
      {
        id: "l1-elec",
        nom: "Électricité et Électronique",
        description: "Bases de l'électricité, circuits et composants électroniques.",
        icon: "fas fa-bolt",
        fichiers: 9,
        couleur: "licence1"
      },
      {
        id: "l1-bureautique",
        nom: "Bureautique et Outils Numériques",
        description: "Maîtrise des outils bureautiques et environnement numérique.",
        icon: "fas fa-laptop",
        fichiers: 7,
        couleur: "licence1"
      }
    ]
  },
  licence2: {
    titre: "Licence 2",
    cours: [
      {
        id: "l2-algo",
        nom: "Algorithmique Avancée",
        description: "Structures de données complexes et analyse d'algorithmes.",
        icon: "fas fa-project-diagram",
        fichiers: 18,
        couleur: "licence2"
      },
      {
        id: "l2-bdd",
        nom: "Bases de Données",
        description: "Conception, implémentation et gestion des systèmes de bases de données.",
        icon: "fas fa-database",
        fichiers: 14,
        couleur: "licence2"
      },
      {
        id: "l2-proba",
        nom: "Probabilités et Statistiques",
        description: "Théorie des probabilités et méthodes statistiques pour l'analyse de données.",
        icon: "fas fa-chart-bar",
        fichiers: 12,
        couleur: "licence2"
      },
      {
        id: "l2-reseau",
        nom: "Réseaux Informatiques",
        description: "Principes des réseaux, protocoles et architectures réseau.",
        icon: "fas fa-network-wired",
        fichiers: 10,
        couleur: "licence2"
      },
      {
        id: "l2-web",
        nom: "Développement Web",
        description: "Création de sites web avec HTML, CSS, JavaScript et technologies associées.",
        icon: "fas fa-globe",
        fichiers: 16,
        couleur: "licence2"
      }
    ]
  },
  licence3: {
    titre: "Licence 3",
    cours: [
      {
        id: "l3-ia",
        nom: "Intelligence Artificielle",
        description: "Fondements de l'IA, apprentissage automatique et réseaux neuronaux.",
        icon: "fas fa-brain",
        fichiers: 20,
        couleur: "licence3"
      },
      {
        id: "l3-se",
        nom: "Systèmes d'Exploitation",
        description: "Fonctionnement des systèmes d'exploitation et gestion des processus.",
        icon: "fas fa-desktop",
        fichiers: 15,
        couleur: "licence3"
      },
      {
        id: "l3-crypto",
        nom: "Cryptologie",
        description: "Principes de cryptographie et sécurité informatique.",
        icon: "fas fa-lock",
        fichiers: 12,
        couleur: "licence3"
      },
      {
        id: "l3-projet",
        nom: "Gestion de Projet Informatique",
        description: "Méthodologies de gestion de projet appliquées à l'informatique.",
        icon: "fas fa-tasks",
        fichiers: 10,
        couleur: "licence3"
      }
    ]
  },
  master1: {
    titre: "Master 1",
    cours: [
      {
        id: "m1-bigdata",
        nom: "Big Data et Analytics",
        description: "Traitement et analyse de volumes massifs de données.",
        icon: "fas fa-chart-line",
        fichiers: 22,
        couleur: "master1"
      },
      {
        id: "m1-cloud",
        nom: "Cloud Computing",
        description: "Architectures cloud, virtualisation et services distants.",
        icon: "fas fa-cloud",
        fichiers: 18,
        couleur: "master1"
      },
      {
        id: "m1-devops",
        nom: "DevOps et Intégration Continue",
        description: "Méthodologies DevOps et automatisation des déploiements.",
        icon: "fas fa-cogs",
        fichiers: 15,
        couleur: "master1"
      },
      {
        id: "m1-cyber",
        nom: "Cybersécurité Avancée",
        description: "Techniques avancées de protection des systèmes d'information.",
        icon: "fas fa-shield-alt",
        fichiers: 20,
        couleur: "master1"
      }
    ]
  },
  master2: {
    titre: "Master 2",
    cours: [
      {
        id: "m2-ia-avance",
        nom: "IA Avancée et Deep Learning",
        description: "Architectures profondes et applications de l'IA moderne.",
        icon: "fas fa-robot",
        fichiers: 25,
        couleur: "master2"
      },
      {
        id: "m2-blockchain",
        nom: "Blockchain et Technologies Distribuées",
        description: "Principes de la blockchain et applications décentralisées.",
        icon: "fas fa-link",
        fichiers: 18,
        couleur: "master2"
      },
      {
        id: "m2-quantum",
        nom: "Informatique Quantique",
        description: "Introduction à l'informatique quantique et ses applications.",
        icon: "fas fa-atom",
        fichiers: 16,
        couleur: "master2"
      },
      {
        id: "m2-management",
        nom: "Management TI et Gouvernance",
        description: "Gestion stratégique des technologies de l'information.",
        icon: "fas fa-chart-pie",
        fichiers: 14,
        couleur: "master2"
      }
    ]
  }
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
  // Charger tous les cours au démarrage
  afficherTousLesCours();
  
  // Gestion des filtres
  document.querySelectorAll('.btn-filtre').forEach(btn => {
    btn.addEventListener('click', function() {
      // Retirer la classe active de tous les boutons
      document.querySelectorAll('.btn-filtre').forEach(b => b.classList.remove('active'));
      
      // Ajouter la classe active au bouton cliqué
      this.classList.add('active');
      
      // Mettre à jour la couleur du filtre actif
      const niveau = this.getAttribute('data-niveau');
      mettreAJourCouleurFiltre(niveau);
      
      // Afficher les cours correspondants
      if (niveau === 'tous') {
        afficherTousLesCours();
      } else {
        afficherCoursParNiveau(niveau);
      }
    });
  });
  
  // Gestion de la recherche
  const searchInput = document.getElementById('search-input');
  searchInput.addEventListener('input', function() {
    filtrerCours(this.value.toLowerCase());
  });
  
  // Toggle menu mobile
  document.getElementById('btn-menu-mobile').addEventListener('click', function() {
    document.getElementById('barre-laterale').classList.toggle('active');
  });
});

// Mettre à jour la couleur du filtre actif
function mettreAJourCouleurFiltre(niveau) {
  document.querySelectorAll('.btn-filtre').forEach(btn => {
    btn.classList.remove('licence1', 'licence2', 'licence3', 'master1', 'master2', 'active');
    
    if (niveau !== 'tous') {
      const btnFiltre = document.querySelector(`.btn-filtre[data-niveau="${niveau}"]`);
      if (btnFiltre) {
        btnFiltre.classList.add(niveau, 'active');
      }
    } else {
      document.querySelector('.btn-filtre[data-niveau="tous"]').classList.add('active');
    }
  });
}

// Afficher tous les cours
function afficherTousLesCours() {
  const container = document.getElementById('niveaux-container');
  container.innerHTML = '';
  
  for (const niveau in coursParNiveau) {
    const section = creerSectionNiveau(niveau);
    container.appendChild(section);
  }
}

// Afficher les cours d'un niveau spécifique
function afficherCoursParNiveau(niveau) {
  const container = document.getElementById('niveaux-container');
  container.innerHTML = '';
  
  const section = creerSectionNiveau(niveau);
  container.appendChild(section);
}

// Créer une section pour un niveau
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

// Créer une carte de cours
function creerCarteCours(cours) {
  const carte = document.createElement('div');
  carte.classList.add('carte-cours', cours.couleur);
  carte.setAttribute('data-nom', cours.nom.toLowerCase());
  carte.setAttribute('data-description', cours.description.toLowerCase());
  
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
      ${cours.id === "l1-math1" 
        ? `<a href="mathematiques_l1.php" class="btn-action btn-voir ${cours.couleur}">Voir les cours</a>`
        : `<a href="#" class="btn-action btn-voir ${cours.couleur}">Voir les cours</a>`
      }
      <a href="#" class="btn-action btn-telecharger ${cours.couleur}"><i class="fas fa-download"></i></a>
    </div>
  </div>
`;
  return carte;
}

// Filtrer les cours selon la recherche
function filtrerCours(terme) {
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
  
  // Afficher un message si aucun résultat
  const sections = document.querySelectorAll('.niveau-section');
  sections.forEach(section => {
    const cartesVisibles = section.querySelectorAll('.carte-cours[style="display: block"]');
    if (cartesVisibles.length === 0) {
      section.style.display = 'none';
    } else {
      section.style.display = 'block';
    }
  });
  
  if (aucunResultat) {
    const container = document.getElementById('niveaux-container');
    if (!document.getElementById('aucun-resultat')) {
      const message = document.createElement('div');
      message.id = 'aucun-resultat';
      message.classList.add('text-center', 'py-5');
      message.innerHTML = `
        <i class="fas fa-search fa-3x mb-3 text-muted"></i>
        <h4 class="text-muted">Aucun cours trouvé</h4>
        <p class="text-muted">Essayez avec d'autres termes de recherche</p>
      `;
      container.appendChild(message);
    }
  } else {
    const message = document.getElementById('aucun-resultat');
    if (message) {
      message.remove();
    }
  }
}