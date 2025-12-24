<?php
require_once 'includes/check_auth.php';
require_once 'includes/fonctions_bibliotheque.php';

redirigerSiNonConnecte();
$utilisateur = obtenirUtilisateurConnecte();
$user_id = obtenirUtilisateurId();

// Récupérer les cours depuis la base de données
$coursParNiveau = getCoursParNiveau(); // <-- Passez l'ID utilisateur ici

// DEBUG: Afficher la structure des données pour vérification
echo "<!-- DEBUG: Structure des données -->";
echo "<!-- " . print_r($coursParNiveau, true) . " -->";

// Convertir en JSON pour JavaScript
$coursJson = json_encode($coursParNiveau, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

// Vérifier s'il y a une erreur JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Erreur JSON: " . json_last_error_msg());
    $coursJson = '{}';
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tdsi.ai - Bibliothèque de cours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/bibliotheque.css">
</head>

<body>
    <!-- ... reste du code inchangé ... -->

    <body>
        <!-- Header (identique) -->
        <!-- ***** Header Area Start ***** -->
        <header class="header-area header-sticky header-bleu">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="main-nav">
                            <!-- ***** Logo Start ***** -->
                            <a href="chatbot.php" class="logo">
                                <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
                            </a>
                            <!-- ***** Logo End ***** -->
                            <!-- ***** Menu Start ***** -->
                            <ul class="nav">
                                <li class="scroll-to-section"><a href="index_connecte.php"><i class="fa fa-home"></i>
                                        Acceuil</a>
                                </li>
                                <li class="scroll-to-section"><a href="chatbot.php"><i class="fas fa-comment"></i>
                                        Chatbot</a></li>
                                <li class="scroll-to-section"><a href="Bibliotheque.php" class="active"><i
                                            class="fas fa-book-open"></i> Bibliothèque</a>
                                </li>
                                <li class="scroll-to-section"><a href="mes_cours.php"><i class="fas fa-star"></i> Mes
                                        Cours</a></li>
                                <li class="scroll-to-section"><a href="historique.php"><i class="fas fa-history"></i>
                                        Historique</a></li>


                                <!-- ***** LIEN ADMIN - AJOUTEZ ICI ***** -->
                                <?php if (estAdministrateur()): ?>
                                    <li class="scroll-to-section"><a href="admin.php"><i class="fas fa-cog"></i>
                                            Administration</a></li>
                                <?php endif; ?>
                                <!-- ***** FIN DU LIEN ADMIN ***** -->

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle user-menu">
                                        <div class="user-avatar">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($utilisateur['prenom'] . '+' . $utilisateur['nom']); ?>&background=ffffff&color=133ebe&size=32"
                                                alt="avatar">
                                        </div>
                                        <span
                                            class="user-name"><?php echo htmlspecialchars($utilisateur['prenom'] ?? ''); ?></span>
                                        <i class="fas fa-chevron-down"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-header">
                                            <div class="user-info">
                                                <strong><?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?></strong>
                                                <span><?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></span>
                                                <small>Niveau :
                                                    <?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'Non défini')); ?></small>
                                            </div>
                                        </li>
                                        <li class="divider"></li>
                                        <li><a href="#" onclick="commencerNouvelleConversation()"><i
                                                    class="fas fa-plus"></i> Nouveau chat</a>
                                        </li>
                                        <li><a href="#" onclick="ouvrirParametres()"><i class="fas fa-cog"></i>
                                                Paramètres</a></li>
                                        <li class="divider"></li>
                                        <li><a href="includes/logout.php" class="logout-btn"><i
                                                    class="fas fa-sign-out-alt"></i>
                                                Déconnexion</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <!-- ***** Menu End ***** -->
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu principal -->
        <div class="contenu-bibliotheque">
            <div class="container-bibliotheque">
                <!-- En-tête -->
                <div class="en-tete-bibliotheque">
                    <div class="titre-bibliotheque">
                        <i class="fas fa-book-open"></i>
                        <h1>Bibliothèque de cours</h1>
                    </div>

                    <div class="search-box">
                        <input type="text" id="search-input" placeholder="Rechercher un cours...">
                        <i class="fas fa-search"></i>
                    </div>
                    <!-- Dans la section en-tete-bibliotheque, remplacez le bouton existant -->
                    <div class="actions-bibliotheque">
                        <button class="btn-favoris" onclick="window.location.href='mes_cours.php'">
                            <i class="fas fa-star"></i>
                            <span>Voir mes favoris</span>
                            <?php if ($coursFavoris = getCoursFavoris($user_id)): ?>
                                <span class="badge-count"><?php echo count($coursFavoris); ?></span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="filtres">
                    <button class="btn-filtre active" data-niveau="tous">Tous les niveaux</button>
                    <button class="btn-filtre l1" data-niveau="l1">Licence 1</button>
                    <button class="btn-filtre l2" data-niveau="l2">Licence 2</button>
                    <button class="btn-filtre l3" data-niveau="l3">Licence 3</button>
                    <button class="btn-filtre m1" data-niveau="m1">Master 1</button>
                    <button class="btn-filtre m2" data-niveau="m2">Master 2</button>
                </div>

                <!-- Conteneur des cours -->
                <div class="conteneur-cours">
                    <div id="niveaux-container">
                        <!-- Le contenu sera chargé par JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Paramètres -->
        <div id="modal-parametres" class="modal-custom">
            <div class="modal-content-custom modal-large">
                <div class="modal-header-custom">
                    <h5><i class="fas fa-cogs"></i> Paramètres</h5>
                    <span class="close-modal" onclick="fermerParametres()">&times;</span>
                </div>
                <div class="modal-body-custom">
                    <div class="parametre-groupe">
                        <h6>Informations du compte</h6>
                        <div class="info-compte">
                            <p><strong>Nom:</strong>
                                <?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?>
                            </p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></p>
                            <p><strong>Niveau:</strong>
                                <?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'Non défini')); ?>
                            </p>
                            <p><strong>Rôle:</strong> <?php echo htmlspecialchars($utilisateur['role'] ?? ''); ?></p>
                        </div>
                    </div>

                    <div class="parametre-groupe">
                        <h6>Préférences de l'interface</h6>
                        <div class="parametre-option">
                            <label class="switch">
                                <input type="checkbox" id="auto-scroll" checked>
                                <span class="slider round"></span>
                            </label>
                            <div class="parametre-info">
                                <strong>Défilement automatique</strong>
                                <span>Descendre automatiquement vers les nouveaux messages</span>
                            </div>
                        </div>

                        <div class="parametre-option">
                            <label class="switch">
                                <input type="checkbox" id="sons" checked>
                                <span class="slider round"></span>
                            </label>
                            <div class="parametre-info">
                                <strong>Sons de notification</strong>
                                <span>Jouer un son à la fin de chaque réponse</span>
                            </div>
                        </div>
                    </div>

                    <div class="parametre-groupe">
                        <h6>Personnalisation</h6>
                        <div class="parametre-option">
                            <label for="vitesse-frappe" class="parametre-label">
                                <strong>Vitesse d'affichage</strong>
                                <span>Contrôle la rapidité de l'affichage progressif</span>
                            </label>
                            <select id="vitesse-frappe" class="parametre-select">
                                <option value="rapide">Rapide</option>
                                <option value="normal" selected>Normal</option>
                                <option value="lent">Lent</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <button class="btn-modal btn-secondary" onclick="reinitialiserParametres()">Réinitialiser</button>
                    <button class="btn-modal btn-confirm" onclick="fermerParametres()">Fermer</button>
                </div>
            </div>
        </div>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br> Développé par Ibrahima Khalilou
                            llah
                            Sylla - Licence 2 TDSI </p>
                    </div>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Données des cours depuis la base de données PHP
            const coursParNiveau = <?php echo $coursJson; ?>;

            console.log('Données chargées depuis la base:', coursParNiveau);

            // État de l'application
            let etatApplication = {
                filtreActuel: 'tous',
                rechercheActuelle: '',
                coursFiltres: [],
                matiereSelectionnee: null,
                vueActuelle: 'liste' // 'liste' ou 'detail'
            };

            // Initialisation
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Initialisation de la bibliothèque...');
                initialiserApplication();
            });

            function initialiserApplication() {
                afficherVueListe();
                configurerFiltres();
                configurerRecherche();
            }

            // Fonction pour afficher 3 colonnes
            function afficherVueListe() {
                etatApplication.vueActuelle = 'liste';
                const container = document.getElementById('niveaux-container');

                // Vérifier si des données existent
                if (!coursParNiveau || Object.keys(coursParNiveau).length === 0) {
                    container.innerHTML = `
                <div class="etat-vide">
                    <div class="icone-vide">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Aucun cours disponible</h3>
                    <p>Les cours seront bientôt disponibles dans votre bibliothèque.</p>
                </div>
            `;
                    return;
                }

                let html = '';

                // Parcourir tous les niveaux
                for (const niveau in coursParNiveau) {
                    const dataNiveau = coursParNiveau[niveau];

                    if (dataNiveau.cours && dataNiveau.cours.length > 0) {
                        html += `
                    <div class="niveau-section" data-niveau="${niveau}">
                        <div class="en-tete-niveau">
                            <h2 class="titre-niveau">${dataNiveau.titre}</h2>
                            <span class="badge-nombre">${dataNiveau.cours.length} cours</span>
                        </div>
                        <div class="grille-cours grille-3-colonnes">
                            ${genererCartesCours(dataNiveau.cours)}
                        </div>
                    </div>
                `;
                    }
                }

                container.innerHTML = html;
                etatApplication.coursFiltres = obtenirTousLesCours();
            }

            // Fonction avec affichage séparé TP/Leçons
            function genererCartesCours(coursArray) {
                if (!Array.isArray(coursArray)) {
                    return '<div class="erreur-donnees">Erreur: données invalides</div>';
                }

                return coursArray.map(cours => {
                    // Échapper les caractères spéciaux pour éviter les erreurs JavaScript
                    const nomEchappe = cours.nom.replace(/'/g, "\\'").replace(/"/g, '\\"');
                    const descriptionEchappee = (cours.description || '').replace(/'/g, "\\'").replace(/"/g, '\\"');

                    // Déterminer si c'est un favori
                    const estFavori = cours.estFavori || false;
                    const iconFavori = estFavori ? 'fas fa-star' : 'far fa-star';
                    const classeFavori = estFavori ? 'favori-actif' : '';

                    // Déterminer si c'est un cours personnel
                    const estPersonnel = cours.personnel || false;
                    const badgePersonnel = estPersonnel ? '<span class="badge-perso"><i class="fas fa-user"></i> Personnel</span>' : '';

                    return `
                <div class="carte-cours ${cours.couleur}" 
                     data-nom="${cours.nom.toLowerCase()}" 
                     data-description="${descriptionEchappee.toLowerCase()}"
                     data-id="${cours.id}">
                    <div class="en-tete-carte ${cours.couleur}">
                        <i class="${cours.icon}"></i>
                    </div>
                    <div class="corps-carte">
                        <div class="header-carte">
                            <h3 class="${cours.couleur}">${cours.nom}</h3>
                            <button class="btn-favori ${classeFavori}" onclick="toggleFavori(${cours.id}, this, event)">
                                <i class="${iconFavori}"></i>
                            </button>
                        </div>
                        ${badgePersonnel}
                        <p class="description-cours">${cours.description || 'Pas de description disponible'}</p>
                        
                        <!-- Section statistiques séparées TP/Leçons -->
                        <div class="stats-separees">
                            <div class="stat-categorie">
                                <i class="fas fa-file"></i>
                                <div class="stat-info">
                                    <strong>Ressources</strong>
                                    <span>${cours.fichiers} fichier(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="actions-cours">
                        <button class="btn-action btn-voir ${cours.couleur}" 
                                onclick="afficherDetailsMatiere(${cours.id}, '${nomEchappe}', '${cours.couleur}')">
                            <i class="fas fa-eye"></i>
                            Voir le cours
                        </button>
                        <button class="btn-action btn-telecharger ${cours.couleur}" 
                                onclick="telechargerZipMatiere(${cours.id}, '${nomEchappe}')">
                            <i class="fas fa-download"></i>
                            Télécharger
                        </button>
                    </div>
                </div>
            `;
                }).join('');
            }

            // CORRECTION : Fonction pour afficher les détails d'une matière
            function afficherDetailsMatiere(idMatiere, nomMatiere, niveau) {
                console.log('Afficher détails matière:', idMatiere, nomMatiere, niveau);

                etatApplication.matiereSelectionnee = { id: idMatiere, nom: nomMatiere, niveau: niveau };
                etatApplication.vueActuelle = 'detail';

                // Afficher un indicateur de chargement
                const container = document.getElementById('niveaux-container');
                container.innerHTML = `
            <div class="detail-matiere">
                <div class="header-detail">
                    <button class="btn-retour" onclick="retourListe()">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la liste
                    </button>
                    <div class="titre-detail">
                        <h1>${nomMatiere}</h1>
                        <span class="badge-niveau ${niveau}">${obtenirNomNiveau(niveau)}</span>
                    </div>
                </div>
                
                <div class="contenu-detail">
                    <div class="chargement-contenu">
                        <div class="spinner-chargement">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <h3>Chargement du contenu...</h3>
                        <p>Récupération des chapitres et ressources</p>
                    </div>
                </div>
            </div>
        `;

                // Charger le contenu de la matière
                chargerContenuMatiere(idMatiere);
            }

            // Fonction pour obtenir le nom du niveau
            function obtenirNomNiveau(codeNiveau) {
                const niveaux = {
                    'l1': 'Licence 1',
                    'l2': 'Licence 2',
                    'l3': 'Licence 3',
                    'm1': 'Master 1',
                    'm2': 'Master 2',
                    'l1-perso': 'Licence 1 (Personnel)',
                    'l2-perso': 'Licence 2 (Personnel)',
                    'l3-perso': 'Licence 3 (Personnel)',
                    'm1-perso': 'Master 1 (Personnel)',
                    'm2-perso': 'Master 2 (Personnel)'
                };
                return niveaux[codeNiveau] || codeNiveau;
            }

            // Fonction pour charger le contenu avec séparation TP/Leçons
            function chargerContenuMatiere(idMatiere) {
                console.log('Chargement contenu pour matière:', idMatiere);

                fetch(`includes/get_contenu_matiere.php?id_matiere=${idMatiere}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur de chargement: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Données reçues:', data);
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        afficherContenuMatiere(data);
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        afficherErreurChargement();
                    });
            }

            // Fonction pour afficher le contenu séparé
            function afficherContenuMatiere(contenu) {
                const contenuDetail = document.querySelector('.contenu-detail');

                let html = `
            <div class="resume-matiere">
                <h4>${contenu.matiere || 'Matière'}</h4>
                <div class="stats-matiere">
                    <div class="stat">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>${contenu.lecons?.length || 0} leçons</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-laptop-code"></i>
                        <span>${contenu.tp?.length || 0} TP</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-file"></i>
                        <span>${contenu.totalFichiers || 0} ressources</span>
                    </div>
                </div>
            </div>
        `;

                // Section des leçons
                if (contenu.lecons && contenu.lecons.length > 0) {
                    html += `
                <div class="chapitres-container">
                    <h3><i class="fas fa-chalkboard-teacher"></i> Leçons (${contenu.lecons.length})</h3>
                    <div class="liste-chapitres">
                        ${contenu.lecons.map((lecon, index) => {
                        const descriptionSafe = (lecon.description || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        return `
                                <div class="carte-chapitre">
                                    <div class="header-chapitre">
                                        <h4>Leçon ${lecon.ordre || index + 1}: ${lecon.titre || 'Sans titre'}</h4>
                                        <span class="badge-ressources">${lecon.ressources?.length || 0} ressources</span>
                                    </div>
                                    ${lecon.description ? `<p class="description-chapitre">${lecon.description}</p>` : ''}
                                    ${genererRessourcesHTML(lecon.ressources)}
                                </div>
                            `;
                    }).join('')}
                    </div>
                </div>
            `;
                } else {
                    html += `
                <div class="chapitres-container">
                    <h3><i class="fas fa-chalkboard-teacher"></i> Leçons</h3>
                    <p class="aucune-ressource">Aucune leçon disponible pour cette matière.</p>
                </div>
            `;
                }

                // Section des TP
                if (contenu.tp && contenu.tp.length > 0) {
                    html += `
                <div class="tp-container">
                    <h3><i class="fas fa-laptop-code"></i> Travaux Pratiques (${contenu.tp.length})</h3>
                    <div class="liste-tp">
                        ${contenu.tp.map((tp, index) => {
                        const descriptionSafe = (tp.description || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        return `
                                <div class="carte-tp">
                                    <div class="header-tp">
                                        <h4>TP ${tp.ordre || index + 1}: ${tp.titre || 'Sans titre'}</h4>
                                        <span class="badge-tp"><i class="fas fa-flask"></i> TP</span>
                                    </div>
                                    ${tp.description ? `<p class="description-tp">${tp.description}</p>` : ''}
                                    ${genererRessourcesHTML(tp.ressources)}
                                </div>
                            `;
                    }).join('')}
                    </div>
                </div>
            `;
                } else {
                    html += `
                <div class="tp-container">
                    <h3><i class="fas fa-laptop-code"></i> Travaux Pratiques</h3>
                    <p class="aucune-ressource">Aucun TP disponible pour cette matière.</p>
                </div>
            `;
                }

                contenuDetail.innerHTML = html;
            }

            function genererRessourcesHTML(ressources) {
                if (!ressources || ressources.length === 0) {
                    return '<p class="aucune-ressource">Aucune ressource disponible</p>';
                }

                return `
            <div class="ressources-chapitre">
                <h5>Ressources disponibles :</h5>
                <div class="liste-ressources">
                    ${ressources.map(ressource => {
                    const nomSafe = (ressource.nom || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    return `
                            <div class="ressource-item">
                                <i class="${obtenirIconeRessource(ressource.type)}"></i>
                                <span class="nom-ressource">${nomSafe}</span>
                                <div class="actions-ressource">
                                    <button class="btn-voir-ressource" onclick="voirRessource(${ressource.id})" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="${ressource.chemin || '#'}" class="btn-telecharger-ressource" download title="Télécharger">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                }).join('')}
                </div>
            </div>
        `;
            }

            // Fonction pour obtenir l'icône selon le type de ressource
            function obtenirIconeRessource(type) {
                const icones = {
                    'pdf': 'fas fa-file-pdf',
                    'doc': 'fas fa-file-word',
                    'docx': 'fas fa-file-word',
                    'ppt': 'fas fa-file-powerpoint',
                    'pptx': 'fas fa-file-powerpoint',
                    'xls': 'fas fa-file-excel',
                    'xlsx': 'fas fa-file-excel',
                    'zip': 'fas fa-file-archive',
                    'video': 'fas fa-file-video',
                    'audio': 'fas fa-file-audio',
                    'image': 'fas fa-file-image',
                    'code': 'fas fa-file-code',
                    'texte': 'fas fa-file-alt'
                };
                return icones[type] || 'fas fa-file';
            }

            function afficherErreurChargement() {
                const contenuDetail = document.querySelector('.contenu-detail');
                contenuDetail.innerHTML = `
            <div class="etat-erreur">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Erreur de chargement</h3>
                <p>Impossible de charger le contenu de la matière</p>
                <button class="btn-secondaire" onclick="chargerContenuMatiere(${etatApplication.matiereSelectionnee.id})">
                    <i class="fas fa-redo"></i>
                    Réessayer
                </button>
            </div>
        `;
            }

            // Fonction pour retourner à la liste
            function retourListe() {
                etatApplication.vueActuelle = 'liste';
                afficherVueListe();
            }

            // Fonctions pour les ressources
            function voirRessource(idRessource) {
                // Ouvrir la ressource dans une nouvelle fenêtre ou modal
                window.open(`includes/voir_ressource.php?id=${idRessource}`, '_blank');
            }

            // Fonction pour gérer les favoris avec event
            function toggleFavori(idMatiere, boutonElement, event) {
                if (event) {
                    event.stopPropagation();
                    event.preventDefault();
                }

                fetch('includes/gestion_favoris.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_matiere: idMatiere })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const icon = boutonElement.querySelector('i');

                            if (data.action === 'ajoute') {
                                boutonElement.classList.add('favori-actif');
                                icon.className = 'fas fa-star';
                                afficherNotification('⭐ ' + data.message, 'success');
                            } else {
                                boutonElement.classList.remove('favori-actif');
                                icon.className = 'far fa-star';
                                afficherNotification('👌 ' + data.message, 'info');
                            }
                        } else {
                            afficherNotification('❌ ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        afficherNotification('❌ Erreur lors de la mise à jour', 'error');
                    });
            }

            // Les autres fonctions utilitaires
            function obtenirTousLesCours() {
                let tousLesCours = [];
                for (const niveau in coursParNiveau) {
                    if (coursParNiveau[niveau].cours) {
                        tousLesCours = tousLesCours.concat(coursParNiveau[niveau].cours);
                    }
                }
                return tousLesCours;
            }

            function configurerFiltres() {
                document.querySelectorAll('.btn-filtre').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.querySelectorAll('.btn-filtre').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        const niveau = this.dataset.niveau;
                        appliquerFiltre(niveau);
                    });
                });
            }

            function appliquerFiltre(niveau) {
                etatApplication.filtreActuel = niveau;
                filtrerEtAfficherCours();
            }

            function configurerRecherche() {
                const searchInput = document.getElementById('search-input');
                searchInput.addEventListener('input', function () {
                    etatApplication.rechercheActuelle = this.value.toLowerCase();
                    filtrerEtAfficherCours();
                });
            }

            function filtrerEtAfficherCours() {
                if (etatApplication.vueActuelle === 'detail') return;

                let coursFiltres = obtenirTousLesCours();

                if (etatApplication.filtreActuel !== 'tous') {
                    coursFiltres = coursFiltres.filter(cours =>
                        cours.couleur === etatApplication.filtreActuel
                    );
                }

                if (etatApplication.rechercheActuelle) {
                    coursFiltres = coursFiltres.filter(cours =>
                        cours.nom.toLowerCase().includes(etatApplication.rechercheActuelle) ||
                        (cours.description && cours.description.toLowerCase().includes(etatApplication.rechercheActuelle))
                    );
                }

                afficherCoursFiltres(coursFiltres);
            }

            function afficherCoursFiltres(coursFiltres) {
                const container = document.getElementById('niveaux-container');

                if (coursFiltres.length === 0) {
                    container.innerHTML = `
                <div class="etat-vide">
                    <div class="icone-vide">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Aucun cours trouvé</h3>
                    <p>Aucun cours ne correspond à vos critères de recherche.</p>
                    <button class="btn-secondaire" onclick="afficherVueListe()">
                        <i class="fas fa-undo"></i>
                        Afficher tous les cours
                    </button>
                </div>
            `;
                    return;
                }

                const coursParNiveauFiltres = {};
                coursFiltres.forEach(cours => {
                    if (!coursParNiveauFiltres[cours.couleur]) {
                        coursParNiveauFiltres[cours.couleur] = [];
                    }
                    coursParNiveauFiltres[cours.couleur].push(cours);
                });

                let html = '';
                for (const niveau in coursParNiveauFiltres) {
                    const dataNiveau = coursParNiveau[niveau];
                    html += `
                <div class="niveau-section" data-niveau="${niveau}">
                    <div class="en-tete-niveau">
                        <h2 class="titre-niveau">${dataNiveau.titre}</h2>
                        <span class="badge-nombre">${coursParNiveauFiltres[niveau].length} cours</span>
                    </div>
                    <div class="grille-cours grille-3-colonnes">
                        ${genererCartesCours(coursParNiveauFiltres[niveau])}
                    </div>
                </div>
            `;
                }

                container.innerHTML = html;
            }

            function telechargerZipMatiere(idMatiere, nomMatiere) {
                afficherNotification(`📦 Préparation du téléchargement pour ${nomMatiere}...`, 'info');

                // Implémentation du téléchargement ZIP
                const link = document.createElement('a');
                link.href = `includes/telecharger_zip_matiere.php?id_matiere=${idMatiere}`;
                link.download = `${genererNomFichier(nomMatiere)}.zip`;
                link.click();
            }

            function genererNomFichier(nomMatiere) {
                return nomMatiere
                    .toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-z0-9]/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_|_$/g, '');
            }

            function afficherNotification(message, type = 'info') {
                // Implémentation simple des notifications toast
                console.log('Notification:', type, message);
                // Vous pouvez ajouter ici une vraie notification toast si nécessaire
            }

            // Export global des fonctions
            window.afficherVueListe = afficherVueListe;
            window.afficherDetailsMatiere = afficherDetailsMatiere;
            window.retourListe = retourListe;
            window.telechargerZipMatiere = telechargerZipMatiere;
            window.toggleFavori = toggleFavori;
            window.voirRessource = voirRessource;

            // Fonctions pour le modal paramètres
            function ouvrirParametres() {
                document.getElementById('modal-parametres').style.display = 'flex';
            }

            function fermerParametres() {
                document.getElementById('modal-parametres').style.display = 'none';
            }

            function reinitialiserParametres() {
                // Réinitialiser les paramètres
                document.getElementById('auto-scroll').checked = true;
                document.getElementById('sons').checked = true;
                document.getElementById('vitesse-frappe').value = 'normal';
                afficherNotification('Paramètres réinitialisés', 'success');
            }

            function commencerNouvelleConversation() {
                window.location.href = 'chatbot.php?nouvelle_conversation=1';
            }
            
        </script>
    </body>

</html>