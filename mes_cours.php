<?php
// mes_cours.php
require_once 'includes/check_auth.php';
require_once 'includes/fonctions_bibliotheque.php';

redirigerSiNonConnecte();
$utilisateur = obtenirUtilisateurConnecte();
$user_id = obtenirUtilisateurId();

// Récupérer les cours favoris
$coursFavoris = getCoursFavoris($user_id);

// Structurer les cours favoris comme dans bibliotheque.php
$coursParNiveau = [];
foreach ($coursFavoris as $cours) {
    $niveauKey = strtolower($cours['code_level']);

    // Ajouter le suffixe -perso si c'est un cours personnel
    if (!empty($cours['id_utilisateur'])) {
        $niveauKey .= '-perso';
    }

    if (!isset($coursParNiveau[$niveauKey])) {
        // Créer la structure du niveau
        $coursParNiveau[$niveauKey] = [
            'titre' => obtenirNomNiveau($niveauKey),
            'cours' => []
        ];
    }

    // Ajouter les propriétés nécessaires pour le template
    $cours['couleur'] = $niveauKey;
    $cours['icon'] = getIconForMatiere($cours['nom_matiere']) ?? 'fas fa-book';
    $cours['estFavori'] = true;
    $cours['personnel'] = !empty($cours['id_utilisateur']);

    // Compter les fichiers
    require_once 'includes/config.php';
    $fichiers = 0;
    try {
        $sqlFichiers = "SELECT COUNT(*) as nb_fichiers 
                       FROM ressource r
                       JOIN chapitre c ON r.id_chapitre = c.id_chapitre
                       WHERE c.id_matiere = ? 
                       AND (r.id_utilisateur IS NULL OR r.id_utilisateur = ?)
                       AND (c.id_utilisateur IS NULL OR c.id_utilisateur = ?)";
        $stmtFichiers = $pdo->prepare($sqlFichiers);
        $stmtFichiers->execute([$cours['id_matiere'], $user_id, $user_id]);
        $resultFichiers = $stmtFichiers->fetch();
        $fichiers = $resultFichiers['nb_fichiers'] ?? 0;
    } catch (Exception $e) {
        error_log("Erreur comptage fichiers: " . $e->getMessage());
    }

    $cours['fichiers'] = $fichiers;

    $coursParNiveau[$niveauKey]['cours'][] = $cours;
}

// Fonction pour obtenir le nom du niveau
function obtenirNomNiveau($codeNiveau)
{
    $niveauSansPerso = str_replace('-perso', '', $codeNiveau);
    $niveaux = [
        'l1' => 'Licence 1',
        'l2' => 'Licence 2',
        'l3' => 'Licence 3',
        'm1' => 'Master 1',
        'm2' => 'Master 2'
    ];

    $nomNiveau = $niveaux[$niveauSansPerso] ?? $niveauSansPerso;
    if (strpos($codeNiveau, '-perso') !== false) {
        $nomNiveau .= ' (Personnel)';
    }

    return $nomNiveau;
}

// Convertir en JSON pour JavaScript
$coursJson = json_encode($coursParNiveau, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
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
    <title>tdsi.ai - Mes Cours Favoris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/bibliotheque.css">
    <style>
        /* Styles spécifiques pour la page mes_cours */
        .page-mes-cours {
            margin-top: 80px;
            padding: 20px 0;
            min-height: calc(100vh - 80px);
        }

        /* Badge pour le bouton "Voir mes favoris" */
        .badge-count {
            background: #ffc107;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 5px;
        }

        /* Style pour l'en-tête spécifique - OPTION 1 */
        .header-personnalise {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            background: linear-gradient(135deg, #133ebe, #0d2e8a);
            color: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header-personnalise::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
        }

        .titre-et-bouton {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .btn-retour-header {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            cursor: pointer;
            backdrop-filter: blur(5px);
            flex-shrink: 0;
        }

        .btn-retour-header:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateX(-5px);
        }

        .titre-personnalise {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .titre-personnalise i {
            font-size: 2.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .titre-personnalise h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }

        .titre-personnalise p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .info-header {
            text-align: right;
            flex-shrink: 0;
        }

        /* Compteur de cours - version améliorée */
        .compteur-cours {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .sous-titre {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            margin: 0;
            font-weight: 500;
        }

        /* Bouton explorer pour le message vide */
        .btn-explorer {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #133ebe, #0d2e8a);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: inherit;
            margin-top: 10px;
        }

        .btn-explorer:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(19, 62, 190, 0.3);
            color: white;
        }

        /* Message lorsque pas de favoris */
        .message-vide {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-top: 20px;
        }

        .message-vide i {
            font-size: 4rem;
            color: #e3e6f0;
            margin-bottom: 25px;
            display: block;
        }

        .message-vide h3 {
            color: #495057;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .message-vide p {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.6;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .titre-personnalise h1 {
                font-size: 1.8rem;
            }

            .titre-personnalise p {
                font-size: 1rem;
            }

            .compteur-cours {
                font-size: 1.1rem;
                padding: 10px 20px;
            }
        }

        @media (max-width: 768px) {
            .page-mes-cours {
                margin-top: 70px;
                padding: 15px 0;
            }

            .header-personnalise {
                padding: 20px;
                flex-direction: column;
                gap: 25px;
            }

            .titre-et-bouton {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
                width: 100%;
            }

            .btn-retour-header {
                width: 100%;
                justify-content: center;
                padding: 12px;
            }

            .titre-personnalise {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                width: 100%;
            }

            .info-header {
                text-align: center;
                width: 100%;
            }

            .compteur-cours {
                justify-content: center;
                width: 100%;
                margin-bottom: 15px;
            }

            .sous-titre {
                text-align: center;
            }

            .message-vide {
                padding: 60px 15px;
                margin-top: 15px;
            }

            .message-vide h3 {
                font-size: 1.3rem;
            }

            .message-vide i {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 480px) {
            .header-personnalise {
                padding: 15px;
                border-radius: 12px;
            }

            .titre-personnalise h1 {
                font-size: 1.5rem;
            }

            .titre-personnalise i {
                font-size: 2rem;
                padding: 15px;
            }

            .compteur-cours {
                font-size: 1rem;
                padding: 8px 16px;
            }

            .btn-retour-header {
                font-size: 0.9rem;
            }

            .message-vide {
                padding: 50px 15px;
            }

            .message-vide h3 {
                font-size: 1.2rem;
            }

            .message-vide i {
                font-size: 3rem;
            }

            .btn-explorer {
                padding: 10px 25px;
                font-size: 0.9rem;
            }
        }

        /* Animation pour le bouton retour */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .btn-retour-header {
            animation: slideInLeft 0.5s ease-out;
        }

        /* Animation pour le compteur */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .compteur-cours {
            animation: pulse 2s ease-in-out infinite;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header-area header-sticky header-bleu">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <a href="chatbot.php" class="logo">
                            <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
                        </a>
                        <ul class="nav">
                            <li class="scroll-to-section"><a href="index_connecte.php"><i class="fa fa-home"></i>
                                    Acceuil</a></li>
                            <li class="scroll-to-section"><a href="chatbot.php"><i class="fas fa-comment"></i>
                                    Chatbot</a></li>
                            <li class="scroll-to-section"><a href="Bibliotheque.php"><i class="fas fa-book-open"></i>
                                    Bibliothèque</a></li>
                            <li class="scroll-to-section"><a href="mes_cours.php" class="active"><i
                                        class="fas fa-star"></i> Mes Cours</a></li>
                            <li class="scroll-to-section"><a href="historique.php"><i class="fas fa-history"></i>
                                    Historique</a></li>

                            <?php if (estAdministrateur()): ?>
                                <li class="scroll-to-section"><a href="admin.php"><i class="fas fa-cog"></i>
                                        Administration</a></li>
                            <?php endif; ?>

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
                                                class="fas fa-plus"></i> Nouveau chat</a></li>
                                    <li><a href="#" onclick="ouvrirParametres()"><i class="fas fa-cog"></i>
                                            Paramètres</a></li>
                                    <li class="divider"></li>
                                    <li><a href="includes/logout.php" class="logout-btn"><i
                                                class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <div class="page-mes-cours">
        <div class="container-bibliotheque">
            <!-- En-tête personnalisé pour Mes Cours -->
            <!-- Dans le contenu principal, remplacez l'en-tête personnalisé par : -->
            <div class="header-personnalise">
                <div class="titre-et-bouton">
                    <button class="btn-retour-header" onclick="window.location.href='Bibliotheque.php'">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la bibliothèque
                    </button>
                    <div class="titre-personnalise">
                        <i class="fas fa-star"></i>
                        <div>
                            <h1>Mes Cours Favoris</h1>
                            <p>Retrouvez ici tous les cours que vous avez marqués comme favoris</p>
                        </div>
                    </div>
                </div>

                <div class="info-header">
                    <div class="compteur-cours">
                        <i class="fas fa-bookmark"></i>
                        <?php
                        $totalCours = 0;
                        foreach ($coursParNiveau as $niveau) {
                            $totalCours += count($niveau['cours']);
                        }
                        echo $totalCours . ' cours';
                        ?>
                    </div>
                    <p class="sous-titre">Ajoutés à vos favoris</p>
                </div>
            </div>

            <!-- Contenu principal (identique à bibliotheque.php) -->
            <?php if (empty($coursParNiveau)): ?>
                <div class="message-vide">
                    <i class="far fa-star"></i>
                    <h3>Vous n'avez pas encore de cours favoris</h3>
                    <p>Les cours que vous ajoutez aux favoris apparaîtront ici pour un accès rapide.</p>
                    <p>Parcourez la bibliothèque et cliquez sur l'étoile ♡ pour ajouter des cours à vos favoris.</p>
                    <a href="Bibliotheque.php" class="btn-explorer">
                        <i class="fas fa-book-open"></i> Explorer la bibliothèque
                    </a>
                </div>
            <?php else: ?>
                <!-- Filtres (optionnel pour les favoris) -->
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
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Paramètres (identique) -->
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

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br> Développé par Ibrahima Khalilou llah
                        Sylla - Licence 2 TDSI</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Données des cours favoris depuis PHP
        const coursParNiveau = <?php echo $coursJson; ?>;

        console.log('Cours favoris chargés:', coursParNiveau);

        // État de l'application
        let etatApplication = {
            filtreActuel: 'tous',
            rechercheActuelle: '',
            coursFiltres: [],
            matiereSelectionnee: null,
            vueActuelle: 'liste'
        };

        // Initialisation
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Initialisation de la page Mes Cours...');
            initialiserApplication();
        });

        function initialiserApplication() {
            afficherVueListe();
            configurerFiltres();
        }

        // Fonction pour afficher 3 colonnes
        function afficherVueListe() {
            etatApplication.vueActuelle = 'liste';
            const container = document.getElementById('niveaux-container');

            // Vérifier si des données existent
            if (!coursParNiveau || Object.keys(coursParNiveau).length === 0) {
                return; // Le message vide est déjà affiché en PHP
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

        // Fonction pour générer les cartes de cours avec affichage séparé TP/Leçons
        function genererCartesCours(coursArray) {
            if (!Array.isArray(coursArray)) {
                return '<div class="erreur-donnees">Erreur: données invalides</div>';
            }

            return coursArray.map(cours => {
                // Échapper les caractères spéciaux
                const nomEchappe = (cours.nom_matiere || '').replace(/'/g, "\\'").replace(/"/g, '\\"');
                const descriptionEchappee = (cours.description || '').replace(/'/g, "\\'").replace(/"/g, '\\"');

                // Déterminer si c'est un cours personnel
                const estPersonnel = cours.personnel || false;
                const badgePersonnel = estPersonnel ? '<span class="badge-perso"><i class="fas fa-user"></i> Personnel</span>' : '';

                return `
                <div class="carte-cours ${cours.couleur}" 
                     data-nom="${nomEchappe.toLowerCase()}" 
                     data-description="${descriptionEchappee.toLowerCase()}"
                     data-id="${cours.id_matiere}">
                    <div class="en-tete-carte ${cours.couleur}">
                        <i class="${cours.icon}"></i>
                    </div>
                    <div class="corps-carte">
                        <div class="header-carte">
                            <h3 class="${cours.couleur}">${cours.nom_matiere}</h3>
                            <button class="btn-favori favori-actif" onclick="toggleFavori(${cours.id_matiere}, this, event)"
                                    title="Retirer des favoris">
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                        ${badgePersonnel}
                        <p class="description-cours">${cours.description || 'Pas de description disponible'}</p>
                        
                        <!-- Section statistiques -->
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
                                onclick="afficherDetailsMatiere(${cours.id_matiere}, '${nomEchappe}', '${cours.couleur}')">
                            <i class="fas fa-eye"></i>
                            Voir le cours
                        </button>
                        ${cours.fichiers > 0 ? `
                            <button class="btn-action btn-telecharger ${cours.couleur}" 
                                    onclick="telechargerZipMatiere(${cours.id_matiere}, '${nomEchappe}')">
                                <i class="fas fa-download"></i>
                                Télécharger
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
            }).join('');
        }

        // Fonction pour afficher les détails d'une matière
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
                        Retour aux favoris
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

        // Fonction pour gérer les favoris avec event - VERSION AVEC NOTIFICATION
        function toggleFavori(idMatiere, boutonElement, event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }

            // Créer une notification personnalisée au lieu d'une alerte
            const notificationId = 'confirmation-retrait-' + idMatiere;

            // Vérifier si une notification existe déjà
            if (document.getElementById(notificationId)) {
                return;
            }

            // Récupérer le nom du cours
            const nomCours = boutonElement.closest('.carte-cours').querySelector('h3').textContent;

            // Créer la notification de confirmation
            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = 'notification-confirmation';
            notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-question-circle"></i>
            <div class="notification-text">
                <strong>Retirer des favoris ?</strong>
                <p>Voulez-vous retirer "${nomCours}" de vos favoris ?</p>
            </div>
        </div>
        <div class="notification-actions">
            <button class="btn-confirmation btn-annuler" onclick="annulerRetraitFavori('${notificationId}')">
                Annuler
            </button>
            <button class="btn-confirmation btn-confirmer" onclick="confirmerRetraitFavori(${idMatiere}, this, '${notificationId}')">
                Retirer
            </button>
        </div>
    `;

            // Appliquer le style CSS correctement
            Object.assign(notification.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                background: 'white',
                color: '#495057',
                padding: '20px',
                borderRadius: '12px',
                boxShadow: '0 10px 30px rgba(0,0,0,0.15)',
                zIndex: '10001',
                animation: 'slideInRight 0.3s ease',
                minWidth: '350px',
                maxWidth: '400px',
                borderLeft: '4px solid #ffc107',
                transform: 'translateX(120%)',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
            });

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);

            // Fermer automatiquement après 10 secondes
            setTimeout(() => {
                if (document.getElementById(notificationId)) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(120%)';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 10000);
        }

        // Fonction pour annuler le retrait
        function annulerRetraitFavori(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(120%)';
                setTimeout(() => notification.remove(), 300);
                afficherNotification('❌ Retrait annulé', 'info');
            }
        }

        // Fonction pour confirmer le retrait
        function confirmerRetraitFavori(idMatiere, boutonElement, notificationId) {
            const notification = document.getElementById(notificationId);

            if (notification) {
                // Mettre à jour l'état de la notification
                const icon = notification.querySelector('.notification-content i');
                icon.className = 'fas fa-spinner fa-spin';

                const btnConfirmer = notification.querySelector('.btn-confirmer');
                btnConfirmer.disabled = true;
                btnConfirmer.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
            }

            // Trouver l'élément bouton original si nécessaire
            let boutonOriginal = boutonElement;
            if (!boutonOriginal.closest('.carte-cours')) {
                boutonOriginal = document.querySelector(`.carte-cours[data-id="${idMatiere}"] .btn-favori`);
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
                    // Fermer la notification de confirmation
                    if (notification) {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateX(120%)';
                        setTimeout(() => notification.remove(), 300);
                    }

                    if (data.success && data.action === 'supprime') {
                        // Retirer la carte de la vue avec animation
                        const carte = boutonOriginal.closest('.carte-cours');
                        carte.style.opacity = '0.5';
                        carte.style.transform = 'scale(0.95) translateY(10px)';

                        setTimeout(() => {
                            carte.style.display = 'none';

                            // Mettre à jour le compteur
                            const compteur = document.querySelector('.compteur-cours');
                            const currentText = compteur.textContent.trim();
                            const currentCount = parseInt(currentText.match(/\d+/)[0]);
                            const newCount = currentCount - 1;
                            compteur.innerHTML = `<i class="fas fa-bookmark"></i> ${newCount} cours`;

                            // Animation du compteur
                            compteur.style.transform = 'scale(1.2)';
                            setTimeout(() => {
                                compteur.style.transform = 'scale(1)';
                            }, 300);

                            // Vérifier si plus de cours
                            setTimeout(() => {
                                const cartesVisibles = document.querySelectorAll('.carte-cours[style*="display: block"], .carte-cours:not([style*="display: none"])');
                                if (cartesVisibles.length === 0) {
                                    document.querySelector('#niveaux-container').innerHTML = '';
                                    document.querySelector('.filtres').style.display = 'none';
                                }
                            }, 100);

                            afficherNotification('📖 Cours retiré des favoris', 'success');
                        }, 500);
                    } else {
                        afficherNotification('❌ ' + (data.message || 'Erreur lors du retrait'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);

                    if (notification) {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateX(120%)';
                        setTimeout(() => notification.remove(), 300);
                    }

                    afficherNotification('❌ Erreur lors de la mise à jour', 'error');
                });
        }

        // Ajouter les styles CSS pour la notification de confirmation
        const styleConfirmation = document.createElement('style');
        styleConfirmation.textContent = `
    /* Styles pour les notifications de confirmation */
    .notification-confirmation .notification-content {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .notification-confirmation .notification-content i {
        font-size: 1.5rem;
        color: #ffc107;
        margin-top: 2px;
        flex-shrink: 0;
    }
    
    .notification-confirmation .notification-text {
        flex: 1;
    }
    
    .notification-confirmation .notification-text strong {
        display: block;
        font-size: 1rem;
        color: #212529;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .notification-confirmation .notification-text p {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
        line-height: 1.4;
    }
    
    .notification-confirmation .notification-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    
    .notification-confirmation .btn-confirmation {
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        min-width: 80px;
        text-align: center;
    }
    
    .notification-confirmation .btn-annuler {
        background: #f8f9fa;
        color: #495057;
        border: 1px solid #dee2e6;
    }
    
    .notification-confirmation .btn-annuler:hover {
        background: #e9ecef;
        border-color: #ced4da;
        transform: translateY(-1px);
    }
    
    .notification-confirmation .btn-confirmer {
        background: #dc3545;
        color: white;
    }
    
    .notification-confirmation .btn-confirmer:hover {
        background: #c82333;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3);
    }
    
    .notification-confirmation .btn-confirmer:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    /* Animations */
    @keyframes slideInRight {
        from { 
            transform: translateX(120%); 
            opacity: 0; 
        }
        to { 
            transform: translateX(0); 
            opacity: 1; 
        }
    }
    
    @keyframes slideOutRight {
        from { 
            transform: translateX(0); 
            opacity: 1; 
        }
        to { 
            transform: translateX(120%); 
            opacity: 0; 
        }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    /* Styles pour les notifications normales */
    .notification {
        display: flex;
        align-items: center;
        gap: 15px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        border-left: 4px solid;
    }
    
    .notification-success {
        border-left-color: #1cc88a !important;
    }
    
    .notification-error {
        border-left-color: #e74a3b !important;
    }
    
    .notification-info {
        border-left-color: #133ebe !important;
    }
    
    .notification-warning {
        border-left-color: #f6c23e !important;
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .notification-icon {
        font-size: 1.2rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .notification-confirmation {
            min-width: 300px !important;
            max-width: calc(100vw - 40px) !important;
            right: 20px !important;
            left: 20px !important;
        }
        
        .notification-confirmation .notification-actions {
            flex-direction: column;
        }
        
        .notification-confirmation .btn-confirmation {
            width: 100%;
        }
    }
    
    @media (max-width: 480px) {
        .notification-confirmation {
            min-width: 280px !important;
            padding: 15px !important;
        }
        
        .notification-confirmation .notification-content {
            gap: 10px;
        }
        
        .notification-confirmation .notification-content i {
            font-size: 1.3rem;
        }
        
        .notification-confirmation .notification-text strong {
            font-size: 0.95rem;
        }
        
        .notification-confirmation .notification-text p {
            font-size: 0.85rem;
        }
    }
`;

        // Ajouter le style au document
        document.head.appendChild(styleConfirmation);

        // Fonction pour afficher les notifications normales (système)
        function afficherNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
        <div class="notification-content">
            <i class="notification-icon ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;

            Object.assign(notification.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                background: getNotificationColor(type),
                color: 'white',
                padding: '15px 20px',
                borderRadius: '10px',
                boxShadow: '0 5px 15px rgba(0,0,0,0.2)',
                zIndex: '10000',
                animation: 'slideInRight 0.3s ease, fadeOut 0.3s ease 2.7s',
                minWidth: '300px',
                maxWidth: '400px',
                transform: 'translateX(120%)',
                opacity: '1'
            });

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(120%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Fonctions utilitaires pour les notifications
        function getNotificationIcon(type) {
            const icons = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'info': 'fas fa-info-circle',
                'warning': 'fas fa-exclamation-triangle'
            };
            return icons[type] || 'fas fa-info-circle';
        }

        function getNotificationColor(type) {
            const colors = {
                'success': 'linear-gradient(135deg, #1cc88a, #17a673)',
                'error': 'linear-gradient(135deg, #e74a3b, #d63a2b)',
                'info': 'linear-gradient(135deg, #133ebe, #0d2e8a)',
                'warning': 'linear-gradient(135deg, #f6c23e, #e4b22a)'
            };
            return colors[type] || colors.info;
        }
        // Modifier la fonction afficherNotification pour utiliser la bordure
        function afficherNotification(message, type = 'info') {
            // Vérifier si c'est une notification système (pas de confirmation)
            if (message.includes('Cours retiré des favoris') ||
                message.includes('Erreur') ||
                message.includes('Paramètres réinitialisés')) {

                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;
                notification.innerHTML = `
            <div class="notification-content">
                <i class="notification-icon ${getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;

                notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${getNotificationColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideInRight 0.3s ease, fadeOut 0.3s ease 2.7s;
            min-width: 300px;
            max-width: 400px;
            transform: translateX(120%);
            border-left: 4px solid ${getNotificationBorderColor(type)};
        `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 10);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(120%)';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        }

        document.head.appendChild(styleConfirmation);
        // Fonction pour voir une ressource
        function voirRessource(idRessource) {
            window.open(`includes/voir_ressource.php?id=${idRessource}`, '_blank');
        }

        // Fonction pour télécharger le ZIP
        function telechargerZipMatiere(idMatiere, nomMatiere) {
            afficherNotification('Préparation du téléchargement...', 'info');
            const link = document.createElement('a');
            link.href = 'includes/telecharger_zip_matiere.php?id_matiere=' + idMatiere;
            link.download = genererNomFichier(nomMatiere) + '.zip';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function genererNomFichier(nomMatiere) {
            return nomMatiere
                .toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                .replace(/[^a-z0-9]/g, '_')
                .replace(/_+/g, '_')
                .replace(/^_|_$/g, '');
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

        function filtrerEtAfficherCours() {
            if (etatApplication.vueActuelle === 'detail') return;

            let coursFiltres = obtenirTousLesCours();

            if (etatApplication.filtreActuel !== 'tous') {
                coursFiltres = coursFiltres.filter(cours =>
                    cours.couleur === etatApplication.filtreActuel
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
                    <p>Aucun cours ne correspond à vos critères de filtrage.</p>
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

        function getNotificationIcon(type) {
            const icons = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'info': 'fas fa-info-circle',
                'warning': 'fas fa-exclamation-triangle'
            };
            return icons[type] || 'fas fa-info-circle';
        }

        function getNotificationColor(type) {
            const colors = {
                'success': 'linear-gradient(135deg, #1cc88a, #17a673)',
                'error': 'linear-gradient(135deg, #e74a3b, #d63a2b)',
                'info': 'linear-gradient(135deg, #133ebe, #0d2e8a)',
                'warning': 'linear-gradient(135deg, #f6c23e, #e4b22a)'
            };
            return colors[type] || colors.info;
        }

        // Fonctions pour le modal paramètres
        function ouvrirParametres() {
            document.getElementById('modal-parametres').style.display = 'flex';
        }

        function fermerParametres() {
            document.getElementById('modal-parametres').style.display = 'none';
        }

        function reinitialiserParametres() {
            document.getElementById('auto-scroll').checked = true;
            document.getElementById('sons').checked = true;
            document.getElementById('vitesse-frappe').value = 'normal';
            afficherNotification('Paramètres réinitialisés', 'success');
        }

        function commencerNouvelleConversation() {
            window.location.href = 'chatbot.php?nouvelle_conversation=1';
        }

        // Export global des fonctions
        window.afficherVueListe = afficherVueListe;
        window.afficherDetailsMatiere = afficherDetailsMatiere;
        window.retourListe = retourListe;
        window.telechargerZipMatiere = telechargerZipMatiere;
        window.toggleFavori = toggleFavori;
        window.voirRessource = voirRessource;
    </script>
</body>

</html>