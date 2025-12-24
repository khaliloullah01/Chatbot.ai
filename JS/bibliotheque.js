// État de l'application
let etatApplication = {
  filtreActuel: 'tous',
  rechercheActuelle: '',
  coursFiltres: []
};

// Initialisation
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
  const btnMenuMobile = document.getElementById('btn-menu-mobile');
  if (btnMenuMobile) {
    btnMenuMobile.addEventListener('click', function() {
      document.getElementById('barre-laterale').classList.toggle('active');
    });
  }
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
    b.classList.remove('active', 'l1', 'l2', 'l3', 'm1', 'm2');
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
    if (coursParNiveau[niveau].cours.length > 0) {
      const section = creerSectionNiveau(niveau);
      container.appendChild(section);
    }
  }
  
  // Stocker les cours filtrés pour référence
  etatApplication.coursFiltres = obtenirTousLesCours();
}

function afficherCoursParNiveau(niveau) {
  const container = document.getElementById('niveaux-container');
  container.innerHTML = '';
  
  if (coursParNiveau[niveau] && coursParNiveau[niveau].cours.length > 0) {
    const section = creerSectionNiveau(niveau);
    container.appendChild(section);
    
    // Stocker les cours filtrés pour référence
    etatApplication.coursFiltres = coursParNiveau[niveau].cours;
  } else {
    container.innerHTML = `
      <div class="text-center py-5">
        <i class="fas fa-book fa-3x mb-3 text-muted"></i>
        <h4 class="text-muted">Aucun cours disponible</h4>
        <p class="text-muted">Aucun cours n'est disponible pour ce niveau pour le moment.</p>
      </div>
    `;
  }
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
        <a href="#" class="btn-action btn-telecharger ${cours.couleur}" onclick="telechargerZipMatiere('${cours.dossier}', '${cours.nom}')">
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

function telechargerZipMatiere(dossier, nomMatiere) {
  // Afficher un indicateur de chargement
  afficherNotification(`📦 Préparation du téléchargement pour ${nomMatiere}...`, 'info');
  
  // Appeler le script PHP pour générer le ZIP
  fetch(`telecharger_zip.php?dossier=${encodeURIComponent(dossier)}&matiere=${encodeURIComponent(nomMatiere)}`)
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
      
      afficherNotification(`✅ Téléchargement de ${nomMatiere} terminé !`, 'success');
    })
    .catch(error => {
      console.error('Erreur:', error);
      afficherNotification(`❌ Erreur lors du téléchargement: ${error.message}`, 'error');
    });
}

// Fonction pour afficher les notifications
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

// Exporter pour utilisation globale
window.reinitialiserRecherche = reinitialiserRecherche;
window.telechargerZipMatiere = telechargerZipMatiere;