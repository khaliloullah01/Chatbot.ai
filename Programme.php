<?php
// Programme.php
require_once 'includes/check_auth.php';
require_once 'includes/fonctions_programme.php';

redirigerSiNonConnecte();
$utilisateur = obtenirUtilisateurConnecte();
$user_id = obtenirUtilisateurId();

// Vérifier et initialiser les tables si nécessaire
if (!verifierTablesProgramme()) {
    initialiserTablesProgramme();
}

// Récupérer les données
try {
    $matieres = getMatieresUtilisateur($user_id);
    $types_evenements = getTypesEvenement();
    $evenements = getEvenementsUtilisateur($user_id);
    $emploi_temps = getEmploiTempsUtilisateur($user_id);
    $statistiques = getStatistiquesEtude($user_id, 'semaine');
    
} catch (Exception $e) {
    error_log("Erreur dans Programme.php: " . $e->getMessage());
    // Initialiser les variables en cas d'erreur
    $matieres = [];
    $types_evenements = [];
    $evenements = [];
    $emploi_temps = [];
    $statistiques = [
        'temps_total' => 0,
        'evenements_termines' => 0,
        'matieres_etudiees' => []
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tdsi.ai - Mon Programme d'Étude</title>

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Moment.js pour le formatage des dates -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/fr.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
    
    <style>
        :root {
            --couleur-principale: #133ebe;
            --couleur-secondaire: #2e59d9;
            --couleur-accent: #e74c3c;
            --couleur-success: #1cc88a;
            --couleur-warning: #f6c23e;
            --couleur-info: #36b9cc;
        }

        .page-programme {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px 0;
        }

        .container-programme {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header-programme {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 25px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--couleur-principale);
            position: relative;
            overflow: hidden;
        }

        .header-programme::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
        }

        .titre-programme {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .titre-programme i {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .titre-programme h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--couleur-principale);
        }

        .actions-programme {
            display: flex;
            gap: 15px;
        }

        .btn-action-programme {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
            font-size: 1rem;
        }

        .btn-nouveau {
            background: linear-gradient(135deg, var(--couleur-success), #17a673);
            color: white;
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.4);
        }

        .btn-nouveau:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(28, 200, 138, 0.6);
        }

        .btn-emploi {
            background: linear-gradient(135deg, var(--couleur-info), #2c9faf);
            color: white;
            box-shadow: 0 4px 15px rgba(54, 185, 204, 0.4);
        }

        .btn-emploi:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(54, 185, 204, 0.6);
        }

        /* Statistiques */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .carte-stat {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--couleur-principale);
            transition: all 0.3s ease;
        }

        .carte-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .carte-stat i {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .carte-stat.temps i {
            color: var(--couleur-success);
        }

        .carte-stat.evenements i {
            color: var(--couleur-principale);
        }

        .carte-stat.progres i {
            color: var(--couleur-warning);
        }

        .carte-stat h3 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            color: #666;
            font-weight: 600;
        }

        .carte-stat .valeur {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }

        /* Section Liste des Événements */
        .section-evenements {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .section-evenements h2 {
            color: var(--couleur-principale);
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .liste-evenements {
            max-height: 600px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .evenement-item {
            background: white;
            border-left: 4px solid var(--couleur-principale);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .evenement-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: var(--couleur-principale);
        }

        .evenement-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .evenement-titre {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0;
            flex: 1;
        }

        .evenement-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-left: 10px;
        }

        .evenement-date {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .evenement-date i {
            color: var(--couleur-principale);
        }

        .evenement-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .evenement-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #666;
        }

        .evenement-detail i {
            color: var(--couleur-principale);
            width: 16px;
        }

        .priorite-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .priorite-haute {
            background-color: #fee;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }

        .priorite-moyenne {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .priorite-faible {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .btn-group-evenement {
            display: flex;
            gap: 8px;
            margin-top: 15px;
        }

        .btn-group-evenement .btn {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        /* Emploi du temps */
        .section-emploi-temps {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .section-emploi-temps h2 {
            color: var(--couleur-principale);
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .emploi-container {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 15px;
        }

        @media (max-width: 1200px) {
            .emploi-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 768px) {
            .emploi-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .emploi-container {
                grid-template-columns: 1fr;
            }
        }

        .jour-emploi {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .jour-emploi:hover {
            border-color: var(--couleur-principale);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(19, 62, 190, 0.1);
        }

        .jour-emploi h3 {
            color: var(--couleur-principale);
            margin: 0 0 15px 0;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .cours-emploi {
            background: white;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid var(--couleur-principale);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .cours-emploi .heure {
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .cours-emploi .titre {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .cours-emploi .matiere {
            font-size: 0.85rem;
            color: #666;
        }

        /* Modal styles */
        .modal-programme .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            background: white;
        }

        .modal-programme .modal-header {
            background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
            color: white;
            border-radius: 16px 16px 0 0;
            border: none;
            padding: 20px 30px;
        }

        .modal-programme .modal-body {
            padding: 30px;
        }

        .modal-programme .btn-close {
            filter: brightness(0) invert(1);
        }

        .form-group-programme {
            margin-bottom: 20px;
        }

        .form-group-programme label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control-programme {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
            background: white;
            color: #333;
        }

        .form-control-programme:focus {
            outline: none;
            border-color: var(--couleur-principale);
            box-shadow: 0 0 0 3px rgba(19, 62, 190, 0.1);
            background: white;
            color: #333;
        }

        .form-control-programme::placeholder {
            color: #999;
        }

        /* Correction pour les sélecteurs */
        select.form-control-programme {
            -webkit-appearance: menulist;
            -moz-appearance: menulist;
            appearance: menulist;
            background-color: white;
            color: #333;
            padding-right: 35px;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
        }

        select.form-control-programme option {
            background-color: white;
            color: #333;
            padding: 10px;
        }

        select.form-control-programme option[value=""] {
            color: #999;
            font-style: italic;
        }

        .couleur-picker {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .couleur-option {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .couleur-option:hover {
            transform: scale(1.1);
            border-color: #ccc;
        }

        .couleur-option.active {
            border-color: #333;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .couleur-option::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s ease;
            text-shadow: 0 0 3px rgba(0,0,0,0.5);
        }

        .couleur-option.active::after {
            opacity: 1;
        }

        input[type="time"].form-control-programme {
            padding: 10px 12px;
        }

        .sous-tache-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .sous-tache-item input {
            flex: 1;
        }

        .btn-ajouter-tache {
            background: var(--couleur-principale);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .btn-ajouter-tache:hover {
            background: var(--couleur-secondaire);
            transform: translateY(-2px);
        }

        /* Style pour les selects dans les formulaires de cours */
        .cours-formulaire {
            background: #f8f9fa;
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .cours-formulaire label {
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        .form-control-color {
            height: 40px;
            padding: 5px;
            cursor: pointer;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            animation: slideInRight 0.3s ease, fadeOut 0.3s ease 2.7s;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(120%);
            }
            to {
                transform: translateX(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
                transform: translateX(120%);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-programme {
                padding: 15px 0;
                margin-top: 70px;
            }

            .container-programme {
                padding: 0 15px;
            }

            .header-programme {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 20px;
            }

            .actions-programme {
                flex-direction: column;
                width: 100%;
            }

            .btn-action-programme {
                width: 100%;
                justify-content: center;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }
            
            select.form-control-programme {
                font-size: 16px;
                height: 44px;
            }
            
            .evenement-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .evenement-type {
                align-self: flex-start;
            }
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
                            <li class="scroll-to-section"><a href="index.php"><i class="fa fa-home"></i> Accueil</a></li>
                            <li class="scroll-to-section"><a href="chatbot.php"><i class="fas fa-comment"></i> Chatbot</a></li>
                            <li class="scroll-to-section"><a href="Programme.php" class="active"><i class="fas fa-calendar-alt"></i> Mon Programme</a></li>
                            <li class="scroll-to-section"><a href="Bibliotheque.php"><i class="fas fa-book-open"></i> Bibliothèque</a></li>
                            <li class="scroll-to-section"><a href="mes_cours.php"><i class="fas fa-star"></i> Mes Cours</a></li>
                            <?php if (estAdministrateur()): ?>
                                <li class="scroll-to-section"><a href="admin.php"><i class="fas fa-cog"></i> Administration</a></li>
                            <?php endif; ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle user-menu">
                                    <div class="user-avatar">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(($utilisateur['prenom'] ?? '') . '+' . ($utilisateur['nom'] ?? '')); ?>&background=ffffff&color=133ebe&size=32" alt="avatar">
                                    </div>
                                    <span class="user-name"><?php echo htmlspecialchars($utilisateur['prenom'] ?? ''); ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-header">
                                        <div class="user-info">
                                            <strong><?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?></strong>
                                            <span><?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></span>
                                            <small>Niveau : <?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'Non défini')); ?></small>
                                        </div>
                                    </li>
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="commencerNouvelleConversation()"><i class="fas fa-plus"></i> Nouveau chat</a></li>
                                    <li><a href="historique.php"><i class="fas fa-history"></i> Historique</a></li>
                                    <li><a href="#" onclick="ouvrirParametres()"><i class="fas fa-cog"></i> Paramètres</a></li>
                                    <li class="divider"></li>
                                    <li><a href="includes/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <div class="page-programme">
        <div class="container-programme">
            <!-- En-tête -->
            <div class="header-programme">
                <div class="titre-programme">
                    <i class="fas fa-calendar-alt"></i>
                    <h1>Mon Programme d'Étude</h1>
                </div>
                <div class="actions-programme">
                    <button class="btn-action-programme btn-nouveau" onclick="ouvrirModalEvenement()">
                        <i class="fas fa-plus"></i>
                        Nouvel événement
                    </button>
                    <button class="btn-action-programme btn-emploi" onclick="ouvrirModalEmploiTemps()">
                        <i class="fas fa-clock"></i>
                        Emploi du temps
                    </button>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="stats-container">
                <div class="carte-stat temps">
                    <i class="fas fa-clock"></i>
                    <h3>Temps d'étude cette semaine</h3>
                    <div class="valeur">
                        <?php
                        $heures = floor($statistiques['temps_total'] / 60);
                        $minutes = $statistiques['temps_total'] % 60;
                        echo $heures . 'h ' . $minutes . 'min';
                        ?>
                    </div>
                </div>

                <div class="carte-stat evenements">
                    <i class="fas fa-check-circle"></i>
                    <h3>Événements terminés</h3>
                    <div class="valeur"><?php echo $statistiques['evenements_termines']; ?></div>
                </div>

                <div class="carte-stat progres">
                    <i class="fas fa-chart-line"></i>
                    <h3>Progression moyenne</h3>
                    <div class="valeur">
                        <?php
                        $progres = 0;
                        if (!empty($statistiques['matieres_etudiees'])) {
                            $progres = count($statistiques['matieres_etudiees']) * 10;
                            $progres = min($progres, 100);
                        }
                        echo $progres . '%';
                        ?>
                    </div>
                </div>
            </div>

            <!-- Liste des Événements -->
            <div class="section-evenements">
                <h2>
                    <i class="fas fa-list"></i>
                    Mes Événements
                    <span class="badge bg-primary"><?php echo count($evenements); ?> événement(s)</span>
                </h2>
                <div class="liste-evenements">
                    <?php if (empty($evenements)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucun événement programmé</h4>
                            <p class="text-muted">Cliquez sur "Nouvel événement" pour commencer</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($evenements as $event): ?>
                            <?php
                            // Déterminer la couleur de la priorité
                            $priorite_class = '';
                            switch ($event['priorite']) {
                                case 'haute':
                                    $priorite_class = 'priorite-haute';
                                    break;
                                case 'moyenne':
                                    $priorite_class = 'priorite-moyenne';
                                    break;
                                case 'faible':
                                    $priorite_class = 'priorite-faible';
                                    break;
                            }
                            
                            // Formater la date
                            $date_debut = new DateTime($event['date_debut']);
                            $date_fin = $event['date_fin'] ? new DateTime($event['date_fin']) : null;
                            ?>
                            <div class="evenement-item" style="border-left-color: <?php echo $event['couleur'] ?? '#133ebe'; ?>;">
                                <div class="evenement-header">
                                    <h4 class="evenement-titre">
                                        <?php echo htmlspecialchars($event['titre']); ?>
                                        <?php if ($event['nom_type']): ?>
                                            <span class="evenement-type" style="background-color: <?php echo $event['couleur_type'] ?? '#e3e6f0'; ?>; color: #333;">
                                                <i class="<?php echo $event['icone'] ?? 'fas fa-calendar'; ?>"></i>
                                                <?php echo htmlspecialchars($event['nom_type']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </h4>
                                    <span class="priorite-badge <?php echo $priorite_class; ?>">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <?php echo ucfirst($event['priorite']); ?>
                                    </span>
                                </div>
                                
                                <div class="evenement-date">
                                    <i class="far fa-clock"></i>
                                    <?php echo $date_debut->format('d/m/Y à H:i'); ?>
                                    <?php if ($date_fin): ?>
                                        - <?php echo $date_fin->format('H:i'); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($event['description']): ?>
                                    <p class="evenement-description"><?php echo htmlspecialchars($event['description']); ?></p>
                                <?php endif; ?>
                                
                                <div class="evenement-details">
                                    <?php if ($event['nom_matiere']): ?>
                                        <div class="evenement-detail">
                                            <i class="fas fa-book"></i>
                                            <span><?php echo htmlspecialchars($event['nom_matiere']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($event['lieu']): ?>
                                        <div class="evenement-detail">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo htmlspecialchars($event['lieu']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($event['sous_taches'])): ?>
                                        <div class="evenement-detail">
                                            <i class="fas fa-tasks"></i>
                                            <span><?php echo count(json_decode($event['sous_taches'], true) ?: []); ?> sous-tâche(s)</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="btn-group-evenement">
                                    <button class="btn btn-outline-primary btn-sm" onclick="modifierEvenement(<?php echo $event['id_evenement']; ?>)">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="supprimerEvenement(<?php echo $event['id_evenement']; ?>)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Emploi du temps -->
            <div class="section-emploi-temps">
                <h2>
                    <i class="fas fa-clock"></i>
                    Emploi du temps hebdomadaire
                </h2>
                <div class="emploi-container">
                    <?php
                    $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
                    $noms_jours = [
                        'lundi' => 'Lundi',
                        'mardi' => 'Mardi',
                        'mercredi' => 'Mercredi',
                        'jeudi' => 'Jeudi',
                        'vendredi' => 'Vendredi',
                        'samedi' => 'Samedi',
                        'dimanche' => 'Dimanche'
                    ];

                    foreach ($jours as $jour) {
                        $cours_du_jour = array_filter($emploi_temps, function ($cours) use ($jour) {
                            return $cours['jour_semaine'] === $jour;
                        });
                    ?>
                    <div class="jour-emploi">
                        <h3><?php echo $noms_jours[$jour]; ?></h3>
                        <?php if (empty($cours_du_jour)): ?>
                            <div style="text-align: center; color: #999; padding: 20px 0;">
                                <i class="fas fa-coffee"></i><br>
                                Aucun cours
                            </div>
                        <?php else: ?>
                            <?php foreach ($cours_du_jour as $cours): ?>
                            <div class="cours-emploi" style="border-left-color: <?php echo $cours['couleur'] ?? '#133ebe'; ?>;">
                                <div class="heure">
                                    <i class="far fa-clock"></i>
                                    <?php echo date('H:i', strtotime($cours['heure_debut'])); ?> -
                                    <?php echo date('H:i', strtotime($cours['heure_fin'])); ?>
                                </div>
                                <div class="titre"><?php echo htmlspecialchars($cours['titre']); ?></div>
                                <?php if ($cours['nom_matiere']): ?>
                                    <div class="matiere"><?php echo htmlspecialchars($cours['nom_matiere']); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br> Développé par Ibrahima Khalilou llah Sylla - Licence 2 TDSI</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal Nouvel Événement -->
    <div class="modal fade modal-programme" id="modalEvenement" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus"></i>
                        Nouvel événement
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEvenement">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-programme">
                                    <label for="titre"><i class="fas fa-heading"></i> Titre *</label>
                                    <input type="text" id="titre" name="titre" class="form-control-programme" required placeholder="Ex: Examen d'algèbre">
                                </div>

                                <div class="form-group-programme">
                                    <label for="type_evenement"><i class="fas fa-tag"></i> Type d'événement *</label>
                                    <select id="type_evenement" name="id_type" class="form-control-programme" required>
                                        <option value="">-- Sélectionner un type --</option>
                                        <?php foreach ($types_evenements as $type): ?>
                                            <option value="<?php echo $type['id_type']; ?>" 
                                                    data-couleur="<?php echo $type['couleur']; ?>"
                                                    data-icone="<?php echo $type['icone']; ?>">
                                                <?php echo htmlspecialchars($type['nom_type']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group-programme">
                                    <label for="matiere"><i class="fas fa-book"></i> Matière (optionnel)</label>
                                    <select id="matiere" name="id_matiere" class="form-control-programme">
                                        <option value="">-- Sélectionner une matière --</option>
                                        <?php foreach ($matieres as $matiere): ?>
                                            <option value="<?php echo $matiere['id_matiere']; ?>">
                                                <?php echo htmlspecialchars($matiere['nom_matiere']); ?>
                                                <?php if (!empty($matiere['nom_level'])): ?>
                                                    (<?php echo htmlspecialchars($matiere['nom_level']); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group-programme">
                                    <label for="priorite"><i class="fas fa-exclamation-circle"></i> Priorité</label>
                                    <select id="priorite" name="priorite" class="form-control-programme">
                                        <option value="faible">Faible</option>
                                        <option value="moyenne" selected>Moyenne</option>
                                        <option value="haute">Haute</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-programme">
                                    <label for="date_debut"><i class="far fa-calendar"></i> Date et heure de début *</label>
                                    <input type="text" id="date_debut" name="date_debut" class="form-control-programme" required>
                                </div>

                                <div class="form-group-programme">
                                    <label for="date_fin"><i class="far fa-calendar-times"></i> Date et heure de fin (optionnel)</label>
                                    <input type="text" id="date_fin" name="date_fin" class="form-control-programme">
                                </div>

                                <div class="form-group-programme">
                                    <label><i class="fas fa-palette"></i> Couleur</label>
                                    <div class="couleur-picker">
                                        <?php
                                        $couleurs = [
                                            '#133ebe' => 'Bleu principal',
                                            '#e74c3c' => 'Rouge (Examen)',
                                            '#2ecc71' => 'Vert (Cours)',
                                            '#3498db' => 'Bleu clair (TD)',
                                            '#9b59b6' => 'Violet (Projet)',
                                            '#f39c12' => 'Orange (Révision)'
                                        ];
                                        
                                        foreach ($couleurs as $couleur => $nom): ?>
                                            <div class="couleur-option <?php echo $couleur == '#133ebe' ? 'active' : ''; ?>" 
                                                 data-couleur="<?php echo $couleur; ?>" 
                                                 title="<?php echo $nom; ?>"
                                                 style="background: <?php echo $couleur; ?>;"></div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" id="couleur_perso" name="couleur_perso" value="#133ebe">
                                </div>

                                <div class="form-group-programme">
                                    <label for="lieu"><i class="fas fa-map-marker-alt"></i> Lieu (optionnel)</label>
                                    <input type="text" id="lieu" name="lieu" class="form-control-programme" placeholder="Salle, amphithéâtre, etc.">
                                </div>
                            </div>
                        </div>

                        <div class="form-group-programme">
                            <label for="description"><i class="fas fa-align-left"></i> Description (optionnel)</label>
                            <textarea id="description" name="description" class="form-control-programme" rows="3" placeholder="Description détaillée de l'événement..."></textarea>
                        </div>

                        <div id="sous-taches-container">
                            <div class="sous-tache-item">
                                <input type="text" class="form-control-programme sous-tache-input" placeholder="Ajouter une sous-tâche">
                                <button type="button" class="btn-ajouter-tache" onclick="ajouterSousTache()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Emploi du Temps -->
    <div class="modal fade modal-programme" id="modalEmploiTemps" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clock"></i>
                        Gérer l'emploi du temps
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="contenu-emploi-temps">
                        <p>Chargement de l'emploi du temps...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    <script>
        const userId = <?php echo $user_id; ?>;
        let dateDebutPicker, dateFinPicker;

        // Initialisation de Flatpickr
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr.localize(flatpickr.l10ns.fr);
            
            dateDebutPicker = flatpickr("#date_debut", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                locale: "fr",
                defaultDate: new Date(),
                minuteIncrement: 5
            });

            dateFinPicker = flatpickr("#date_fin", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                locale: "fr",
                minuteIncrement: 5
            });

            configurerEvenements();
        });

        function configurerEvenements() {
            // Sélecteur de couleur
            document.querySelectorAll('.couleur-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.couleur-option').forEach(o => o.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('couleur_perso').value = this.dataset.couleur;
                });
            });

            // Sélecteur de type - mettre à jour la couleur
            document.getElementById('type_evenement').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const couleur = selectedOption.dataset.couleur;
                
                if (couleur) {
                    document.querySelectorAll('.couleur-option').forEach(o => {
                        o.classList.remove('active');
                        if (o.dataset.couleur === couleur) {
                            o.classList.add('active');
                            document.getElementById('couleur_perso').value = couleur;
                        }
                    });
                }
            });

            // Formulaire d'événement
            document.getElementById('formEvenement').addEventListener('submit', function(e) {
                e.preventDefault();
                sauvegarderEvenement();
            });
        }

        function ouvrirModalEvenement(date = null) {
            // Fermer d'abord le modal s'il est ouvert
            const existingModal = bootstrap.Modal.getInstance(document.getElementById('modalEvenement'));
            if (existingModal) {
                existingModal.hide();
            }

            // Réinitialiser le formulaire
            document.getElementById('formEvenement').reset();

            // Réinitialiser les sélecteurs
            document.getElementById('type_evenement').selectedIndex = 0;
            document.getElementById('matiere').selectedIndex = 0;
            document.getElementById('priorite').selectedIndex = 1;

            // Réinitialiser les couleurs
            document.getElementById('couleur_perso').value = '#133ebe';
            document.querySelectorAll('.couleur-option').forEach(option => {
                option.classList.remove('active');
                if (option.dataset.couleur === '#133ebe') {
                    option.classList.add('active');
                }
            });

            // Réinitialiser les dates
            if (date) {
                const now = new Date();
                const dateStr = moment(date).format('YYYY-MM-DD');
                dateDebutPicker.setDate(dateStr + 'T' + now.getHours().toString().padStart(2, '0') + ':00');
            } else {
                const now = new Date();
                const dateStr = moment().add(1, 'hour').startOf('hour').format('YYYY-MM-DDTHH:00');
                dateDebutPicker.setDate(dateStr);
            }

            // Réinitialiser les sous-tâches
            document.getElementById('sous-taches-container').innerHTML = `
                <div class="sous-tache-item">
                    <input type="text" class="form-control-programme sous-tache-input" placeholder="Ajouter une sous-tâche">
                    <button type="button" class="btn-ajouter-tache" onclick="ajouterSousTache()">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            `;

            // Ouvrir le modal
            setTimeout(() => {
                const newModal = new bootstrap.Modal(document.getElementById('modalEvenement'));
                newModal.show();
                
                // Focus sur le titre après ouverture
                setTimeout(() => {
                    document.getElementById('titre').focus();
                }, 300);
            }, 100);
        }

        function ouvrirModalEmploiTemps() {
            chargerEmploiTemps();
            const modal = new bootstrap.Modal(document.getElementById('modalEmploiTemps'));
            modal.show();
        }

        function ajouterSousTache() {
            const container = document.getElementById('sous-taches-container');
            const div = document.createElement('div');
            div.className = 'sous-tache-item';
            div.innerHTML = `
                <input type="text" class="form-control-programme sous-tache-input" placeholder="Ajouter une sous-tâche">
                <button type="button" class="btn btn-danger btn-sm" onclick="supprimerSousTache(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(div);
            div.querySelector('input').focus();
        }

        function supprimerSousTache(btn) {
            btn.parentElement.remove();
        }

        function sauvegarderEvenement() {
            const formData = {
                id_utilisateur: userId,
                id_type: document.getElementById('type_evenement').value,
                titre: document.getElementById('titre').value,
                description: document.getElementById('description').value,
                id_matiere: document.getElementById('matiere').value || null,
                date_debut: document.getElementById('date_debut').value,
                date_fin: document.getElementById('date_fin').value || null,
                couleur_perso: document.getElementById('couleur_perso').value,
                lieu: document.getElementById('lieu').value || null,
                priorite: document.getElementById('priorite').value,
                sous_taches: []
            };

            // Récupérer les sous-tâches
            document.querySelectorAll('.sous-tache-input').forEach(input => {
                if (input.value.trim()) {
                    formData.sous_taches.push({description: input.value.trim(), terminee: false});
                }
            });

            // Validation
            if (!formData.titre || !formData.id_type || !formData.date_debut) {
                afficherNotification('Veuillez remplir les champs obligatoires (*)', 'error');
                return;
            }

            console.log('Données à envoyer:', formData);

            fetch('includes/gestion_programme.php?action=ajouter_evenement', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Réponse du serveur:', data);
                if (data.success) {
                    afficherNotification('Événement ajouté avec succès!', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEvenement'));
                    if (modal) modal.hide();
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    afficherNotification('Erreur: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur détaillée:', error);
                afficherNotification('Erreur lors de l\'enregistrement: ' + error.message, 'error');
            });
        }

        function chargerEmploiTemps() {
            fetch('includes/gestion_programme.php?action=get_emploi_temps')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        afficherFormulaireEmploiTemps(data.emploi_temps);
                    } else {
                        document.getElementById('contenu-emploi-temps').innerHTML =
                            '<p class="text-danger">Erreur de chargement: ' + (data.message || 'Inconnue') + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('contenu-emploi-temps').innerHTML =
                        '<p class="text-danger">Erreur de connexion au serveur: ' + error.message + '</p>';
                });
        }

        function afficherFormulaireEmploiTemps(emploiTemps) {
            const jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
            const nomsJours = {
                lundi: 'Lundi', mardi: 'Mardi', mercredi: 'Mercredi',
                jeudi: 'Jeudi', vendredi: 'Vendredi',
                samedi: 'Samedi', dimanche: 'Dimanche'
            };

            let html = `
                <form id="formEmploiTemps">
                    <div id="cours-container">
                        <p class="text-muted mb-3">Ajoutez vos cours pour chaque jour de la semaine :</p>
            `;

            if (emploiTemps.length === 0) {
                html += genererChampCours({}, 0);
            } else {
                emploiTemps.forEach((cours, index) => {
                    html += genererChampCours(cours, index);
                });
            }

            html += `
                    </div>
                    
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary" onclick="ajouterChampCours()">
                            <i class="fas fa-plus"></i> Ajouter un cours
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            `;

            document.getElementById('contenu-emploi-temps').innerHTML = html;

            document.getElementById('formEmploiTemps').addEventListener('submit', function(e) {
                e.preventDefault();
                sauvegarderEmploiTemps();
            });
        }

        function genererChampCours(cours, index) {
            // Récupérer la liste des matières depuis PHP
            const matieresOptions = <?php echo json_encode(array_map(function($m) {
                return ['id' => $m['id_matiere'], 'nom' => $m['nom_matiere']];
            }, $matieres)); ?>;

            let matieresSelect = '<option value="">Sélectionner une matière...</option>';
            matieresOptions.forEach(matiere => {
                const selected = cours.id_matiere && cours.id_matiere == matiere.id ? 'selected' : '';
                matieresSelect += `<option value="${matiere.id}" ${selected}>${matiere.nom}</option>`;
            });

            const jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
            const nomsJours = {
                lundi: 'Lundi', mardi: 'Mardi', mercredi: 'Mercredi',
                jeudi: 'Jeudi', vendredi: 'Vendredi',
                samedi: 'Samedi', dimanche: 'Dimanche'
            };

            return `
                <div class="cours-formulaire mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Jour *</label>
                            <select name="cours[${index}][jour_semaine]" class="form-control-programme" required>
                                <option value="">Sélectionner...</option>
                                ${jours.map(jour => `
                                    <option value="${jour}" ${cours.jour_semaine === jour ? 'selected' : ''}>${nomsJours[jour]}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Heure début *</label>
                            <input type="time" name="cours[${index}][heure_debut]" class="form-control-programme" 
                                   value="${cours.heure_debut || '08:00'}" required>
                        </div>
                        <div class="col-md-2">
                            <label>Heure fin *</label>
                            <input type="time" name="cours[${index}][heure_fin]" class="form-control-programme" 
                                   value="${cours.heure_fin || '10:00'}" required>
                        </div>
                        <div class="col-md-3">
                            <label>Titre *</label>
                            <input type="text" name="cours[${index}][titre]" class="form-control-programme" 
                                   value="${cours.titre || ''}" placeholder="Nom du cours" required>
                        </div>
                        <div class="col-md-2">
                            <label>Action</label>
                            <button type="button" class="btn btn-danger w-100" onclick="supprimerChampCours(this)" 
                                    ${index < <?php echo count($emploi_temps); ?> ? 'disabled' : ''}>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Matière</label>
                            <select name="cours[${index}][id_matiere]" class="form-control-programme">
                                ${matieresSelect}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Couleur</label>
                            <input type="color" name="cours[${index}][couleur]" class="form-control-programme form-control-color" 
                                   value="${cours.couleur || '#133ebe'}">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label>Description</label>
                            <input type="text" name="cours[${index}][description]" class="form-control-programme" 
                                   value="${cours.description || ''}" placeholder="Description optionnelle">
                        </div>
                    </div>
                </div>
            `;
        }

        function ajouterChampCours() {
            const container = document.getElementById('cours-container');
            const index = container.querySelectorAll('.cours-formulaire').length;
            container.insertAdjacentHTML('beforeend', genererChampCours({}, index));
        }

        function supprimerChampCours(btn) {
            if (confirm('Supprimer ce cours ?')) {
                btn.closest('.cours-formulaire').remove();
            }
        }

        function sauvegarderEmploiTemps() {
            const form = document.getElementById('formEmploiTemps');
            const cours = [];
            
            // Collecter les données des cours
            const coursContainers = form.querySelectorAll('.cours-formulaire');
            coursContainers.forEach((container, index) => {
                const jour = container.querySelector('select[name="cours[' + index + '][jour_semaine]"]')?.value;
                const heureDebut = container.querySelector('input[name="cours[' + index + '][heure_debut]"]')?.value;
                const heureFin = container.querySelector('input[name="cours[' + index + '][heure_fin]"]')?.value;
                const titre = container.querySelector('input[name="cours[' + index + '][titre]"]')?.value;
                const idMatiere = container.querySelector('select[name="cours[' + index + '][id_matiere]"]')?.value;
                const couleur = container.querySelector('input[name="cours[' + index + '][couleur]"]')?.value;
                const description = container.querySelector('input[name="cours[' + index + '][description]"]')?.value;
                
                if (jour && heureDebut && heureFin && titre) {
                    cours.push({
                        jour_semaine: jour,
                        heure_debut: heureDebut,
                        heure_fin: heureFin,
                        titre: titre,
                        id_matiere: idMatiere || null,
                        couleur: couleur || '#133ebe',
                        description: description || null
                    });
                }
            });

            const data = { cours: cours };

            console.log('Données emploi du temps à envoyer:', data);

            fetch('includes/gestion_programme.php?action=sauvegarder_emploi_temps', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Réponse du serveur:', data);
                if (data.success) {
                    afficherNotification('Emploi du temps sauvegardé!', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEmploiTemps'));
                    if (modal) modal.hide();
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    afficherNotification('Erreur: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                afficherNotification('Erreur lors de la sauvegarde: ' + error.message, 'error');
            });
        }

        function modifierEvenement(eventId) {
            alert('Fonctionnalité de modification en développement');
        }

        function supprimerEvenement(eventId) {
            if (confirm('Voulez-vous vraiment supprimer cet événement ?')) {
                fetch(`includes/gestion_programme.php?action=supprimer_evenement&id_evenement=${eventId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            afficherNotification('Événement supprimé', 'success');
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            afficherNotification('Erreur: ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        afficherNotification('Erreur lors de la suppression', 'error');
                    });
            }
        }

        function afficherNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification`;
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
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="${getNotificationIcon(type)}" style="font-size: 1.2em;"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);

            setTimeout(() => {
                notification.remove();
            }, 3000);
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
                'success': '#1cc88a',
                'error': '#e74a3b',
                'info': '#133ebe',
                'warning': '#f6c23e'
            };
            return colors[type] || colors.info;
        }

        // Fonctions du header
        function commencerNouvelleConversation() {
            window.location.href = 'chatbot.php?nouvelle_conversation=1';
        }

        function ouvrirParametres() {
            window.location.href = 'parametres.php';
        }
    </script>
</body>
</html>