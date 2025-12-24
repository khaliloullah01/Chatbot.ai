<?php
// fonctions_programme.php

require_once 'config.php';

// Fonction pour vérifier si les tables existent
function verifierTablesProgramme() {
    global $pdo;
    
    $tables_necessaires = ['type_evenement', 'programme_etude', 'sous_tache', 'emploi_temps'];
    
    foreach ($tables_necessaires as $table) {
        try {
            $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
    }
    
    return true;
}

// Fonction pour initialiser les tables si elles n'existent pas
function initialiserTablesProgramme() {
    global $pdo;
    
    try {
        // Créer la table type_evenement si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS type_evenement (
            id_type INT AUTO_INCREMENT PRIMARY KEY,
            nom_type VARCHAR(50) NOT NULL,
            couleur VARCHAR(7) DEFAULT '#133ebe',
            icone VARCHAR(50) DEFAULT 'fas fa-calendar',
            UNIQUE KEY unique_nom_type (nom_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Insérer les types par défaut s'ils n'existent pas
        $types = [
            ['Examen', '#e74c3c', 'fas fa-file-alt'],
            ['TD', '#3498db', 'fas fa-laptop-code'],
            ['Cours', '#2ecc71', 'fas fa-chalkboard-teacher'],
            ['Projet', '#9b59b6', 'fas fa-project-diagram'],
            ['Révision', '#f39c12', 'fas fa-book-open'],
            ['Autre', '#95a5a6', 'fas fa-calendar']
        ];
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO type_evenement (nom_type, couleur, icone) VALUES (?, ?, ?)");
        foreach ($types as $type) {
            $stmt->execute($type);
        }
        
        // Créer la table programme_etude (anciennement evenements) si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS programme_etude (
            id_evenement INT AUTO_INCREMENT PRIMARY KEY,
            id_utilisateur INT NOT NULL,
            id_type INT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description TEXT,
            id_matiere INT NULL,
            date_debut DATETIME NOT NULL,
            date_fin DATETIME NULL,
            couleur_perso VARCHAR(7) DEFAULT '#133ebe',
            lieu VARCHAR(255) NULL,
            priorite ENUM('faible', 'moyenne', 'haute') DEFAULT 'moyenne',
            statut ENUM('planifié', 'en_cours', 'terminé', 'annulé') DEFAULT 'planifié',
            rappel_minutes INT NULL,
            recurrence VARCHAR(50) NULL,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_utilisateur (id_utilisateur),
            INDEX idx_type (id_type),
            INDEX idx_matiere (id_matiere),
            INDEX idx_date_debut (date_debut),
            CONSTRAINT fk_programme_utilisateur 
                FOREIGN KEY (id_utilisateur) 
                REFERENCES utilisateurs(id_utilisateur) 
                ON DELETE CASCADE,
            CONSTRAINT fk_programme_type 
                FOREIGN KEY (id_type) 
                REFERENCES type_evenement(id_type) 
                ON DELETE RESTRICT,
            CONSTRAINT fk_programme_matiere 
                FOREIGN KEY (id_matiere) 
                REFERENCES matieres(id_matiere) 
                ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Créer la table sous_tache si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS sous_tache (
            id_sous_tache INT AUTO_INCREMENT PRIMARY KEY,
            id_evenement INT NOT NULL,
            description VARCHAR(255) NOT NULL,
            terminee BOOLEAN DEFAULT FALSE,
            ordre INT DEFAULT 0,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_evenement (id_evenement),
            CONSTRAINT fk_sous_tache_evenement 
                FOREIGN KEY (id_evenement) 
                REFERENCES programme_etude(id_evenement) 
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Créer la table emploi_temps si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS emploi_temps (
            id_emploi INT AUTO_INCREMENT PRIMARY KEY,
            id_utilisateur INT NOT NULL,
            jour_semaine ENUM('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche') NOT NULL,
            heure_debut TIME NOT NULL,
            heure_fin TIME NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description TEXT,
            id_matiere INT NULL,
            couleur VARCHAR(7) DEFAULT '#133ebe',
            recurrence ENUM('toutes_semaines', 'semaines_paires', 'semaines_impaires') DEFAULT 'toutes_semaines',
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_utilisateur_emploi (id_utilisateur),
            INDEX idx_jour_semaine (jour_semaine),
            INDEX idx_matiere_emploi (id_matiere),
            CONSTRAINT fk_emploi_utilisateur 
                FOREIGN KEY (id_utilisateur) 
                REFERENCES utilisateurs(id_utilisateur) 
                ON DELETE CASCADE,
            CONSTRAINT fk_emploi_matiere 
                FOREIGN KEY (id_matiere) 
                REFERENCES matieres(id_matiere) 
                ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Ajouter la colonne sous_taches dans programme_etude pour stocker les sous-tâches en JSON
        try {
            $pdo->exec("ALTER TABLE programme_etude 
                       ADD COLUMN sous_taches JSON NULL AFTER recurrence");
        } catch (Exception $e) {
            // La colonne existe peut-être déjà
            error_log("Note: Colonne sous_taches peut déjà exister: " . $e->getMessage());
        }
        
        // Créer un index composite pour améliorer les performances
        try {
            $pdo->exec("CREATE INDEX idx_utilisateur_date 
                       ON programme_etude (id_utilisateur, date_debut)");
        } catch (Exception $e) {
            // L'index existe peut-être déjà
        }
        
        error_log("✅ Tables programme initialisées avec succès");
        return true;
        
    } catch (Exception $e) {
        error_log("❌ Erreur lors de l'initialisation des tables programme: " . $e->getMessage());
        return false;
    }
}

// Vérifier et initialiser les tables au chargement
if (!verifierTablesProgramme()) {
    initialiserTablesProgramme();
}

// Fonction pour récupérer tous les événements d'un utilisateur
function getEvenementsUtilisateur($id_utilisateur, $debut = null, $fin = null) {
    global $pdo;
    
    // CORRECTION : Retirer "m.code_matiere" qui n'existe pas
    $sql = "SELECT pe.*, te.nom_type, te.couleur as couleur_type, te.icone,
                   m.nom_matiere, m.description as description_matiere
            FROM programme_etude pe
            JOIN type_evenement te ON pe.id_type = te.id_type
            LEFT JOIN matiere m ON pe.id_matiere = m.id_matiere
            WHERE pe.id_utilisateur = ?";
    
    $params = [$id_utilisateur];
    
    if ($debut && $fin) {
        $sql .= " AND ((pe.date_debut BETWEEN ? AND ?) OR 
                      (pe.date_fin BETWEEN ? AND ?) OR 
                      (pe.date_debut <= ? AND pe.date_fin >= ?))";
        array_push($params, $debut, $fin, $debut, $fin, $debut, $fin);
    }
    
    $sql .= " ORDER BY pe.date_debut, pe.priorite DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les sous-tâches pour chaque événement
    foreach ($evenements as &$evenement) {
        $evenement['sous_taches'] = getSousTachesEvenement($evenement['id_evenement']);
        
        // Formater les dates pour le calendrier
        $evenement['start'] = $evenement['date_debut'];
        $evenement['end'] = $evenement['date_fin'] ?: $evenement['date_debut'];
        $evenement['title'] = $evenement['titre'];
        $evenement['color'] = $evenement['couleur_perso'] ?: $evenement['couleur_type'];
        $evenement['textColor'] = '#ffffff';
        
        // Ajouter des classes CSS pour le calendrier
        $prioriteClass = [
            'haute' => 'evenement-haute-priorite',
            'moyenne' => 'evenement-moyenne-priorite',
            'faible' => 'evenement-faible-priorite'
        ];
        $evenement['classNames'] = [$prioriteClass[$evenement['priorite']] ?? ''];
        
        // Ajouter les propriétés étendues
        $evenement['extendedProps'] = [
            'icone' => $evenement['icone'] ?? 'fas fa-calendar',
            'priorite' => $evenement['priorite'],
            'nom_matiere' => $evenement['nom_matiere'] ?? null,
            'description' => $evenement['description'] ?? null,
            'lieu' => $evenement['lieu'] ?? null,
            'sous_taches' => $evenement['sous_taches']
        ];
    }
    
    return $evenements;
}

// Fonction pour récupérer les sous-tâches d'un événement
function getSousTachesEvenement($id_evenement) {
    global $pdo;
    
    $sql = "SELECT * FROM sous_tache 
            WHERE id_evenement = ? 
            ORDER BY ordre, id_sous_tache";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_evenement]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer l'emploi du temps hebdomadaire
function getEmploiTempsUtilisateur($id_utilisateur, $semaine = null) {
    global $pdo;
    
    // CORRECTION : Retirer "m.code_matiere" qui n'existe pas
    $sql = "SELECT et.*, m.nom_matiere, m.description as description_matiere
            FROM emploi_temps et
            LEFT JOIN matiere m ON et.id_matiere = m.id_matiere
            WHERE et.id_utilisateur = ?
            ORDER BY 
                FIELD(et.jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'),
                et.heure_debut";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les matières de l'utilisateur
function getMatieresUtilisateur($user_id) {
    global $pdo;
    
    try {
        // CORRECTION: Table matiere (singulier) au lieu de matieres (pluriel)
        $sql = "SELECT m.*, n.nom_level 
                FROM matiere m 
                LEFT JOIN niveaux n ON m.niveau_id = n.id_level
                WHERE m.est_publique = 1 OR m.id_utilisateur = ?
                ORDER BY m.nom_matiere";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $matieres;
    } catch (Exception $e) {
        error_log("Erreur getMatieresUtilisateur: " . $e->getMessage());
        return [];
    }
}


// Fonction pour récupérer les types d'événements

function getTypesEvenement() {
    global $pdo;
    
    try {
        // CORRECTION: Utiliser le bon nom de table (type_evenement au lieu de types_evenement)
        $sql = "SELECT DISTINCT * FROM type_evenement ORDER BY id_type";
        $stmt = $pdo->query($sql);
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si aucun type n'existe, créer des types par défaut
        if (empty($types)) {
            return creerTypesEvenementParDefaut();
        }
        
        // Retourner uniquement les 6 premiers (vous avez des doublons dans la base)
        return array_slice($types, 0, 6);
        
    } catch (Exception $e) {
        error_log("Erreur getTypesEvenement: " . $e->getMessage());
        return creerTypesEvenementParDefaut();
    }
}

// Fonction pour ajouter un nouvel événement
function ajouterEvenement($data) {
    global $pdo;
    
    $sql = "INSERT INTO programme_etude (
                id_utilisateur, id_type, titre, description, id_matiere,
                date_debut, date_fin, couleur_perso, lieu, priorite,
                statut, rappel_minutes, recurrence
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        $data['id_utilisateur'],
        $data['id_type'],
        $data['titre'],
        $data['description'] ?? null,
        $data['id_matiere'] ?? null,
        $data['date_debut'],
        $data['date_fin'] ?? null,
        $data['couleur_perso'] ?? null,
        $data['lieu'] ?? null,
        $data['priorite'] ?? 'moyenne',
        $data['statut'] ?? 'planifié',
        $data['rappel_minutes'] ?? null,
        $data['recurrence'] ?? null
    ]);
    
    if ($success) {
        $id_evenement = $pdo->lastInsertId();
        
        // Ajouter les sous-tâches si elles existent
        if (!empty($data['sous_taches'])) {
            ajouterSousTaches($id_evenement, $data['sous_taches']);
        }
        
        return $id_evenement;
    }
    
    return false;
}

// Fonction pour ajouter des sous-tâches
function ajouterSousTaches($id_evenement, $sous_taches) {
    global $pdo;
    
    $sql = "INSERT INTO sous_tache (id_evenement, description, ordre) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    $ordre = 1;
    foreach ($sous_taches as $tache) {
        if (!empty(trim($tache))) {
            $stmt->execute([$id_evenement, trim($tache), $ordre++]);
        }
    }
}

// Fonction pour récupérer les statistiques d'étude
function getStatistiquesEtude($id_utilisateur, $periode = 'semaine') {
    global $pdo;
    
    // Vérifier si la table suivi_etude existe
    try {
        $pdo->query("SELECT 1 FROM suivi_etude LIMIT 1");
        $tableSuiviExiste = true;
    } catch (Exception $e) {
        $tableSuiviExiste = false;
    }
    
    $whereClause = "WHERE s.id_utilisateur = ?";
    $params = [$id_utilisateur];
    
    switch ($periode) {
        case 'jour':
            $whereClause .= " AND s.date = CURDATE()";
            break;
        case 'semaine':
            $whereClause .= " AND YEARWEEK(s.date, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'mois':
            $whereClause .= " AND MONTH(s.date) = MONTH(CURDATE()) AND YEAR(s.date) = YEAR(CURDATE())";
            break;
    }
    
    // Temps total d'étude (si la table existe)
    if ($tableSuiviExiste) {
        $sqlTemps = "SELECT COALESCE(SUM(s.temps_minutes), 0) as total_minutes
                     FROM suivi_etude s
                     $whereClause";
        $stmt = $pdo->prepare($sqlTemps);
        $stmt->execute($params);
        $temps = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $temps = ['total_minutes' => 0];
    }
    
    // Nombre d'événements terminés
    $sqlEvenements = "SELECT COUNT(*) as total 
                      FROM programme_etude 
                      WHERE id_utilisateur = ? 
                      AND statut = 'terminé'";
    if ($periode !== 'tout') {
        $sqlEvenements .= " AND DATE(date_debut) >= DATE_SUB(CURDATE(), INTERVAL 1 $periode)";
    }
    $stmt = $pdo->prepare($sqlEvenements);
    $stmt->execute([$id_utilisateur]);
    $evenements = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Matières étudiées (si la table existe)
    if ($tableSuiviExiste) {
        $sqlMatieres = "SELECT m.nom_matiere, SUM(s.temps_minutes) as temps_total
                        FROM suivi_etude s
                        LEFT JOIN matiere m ON s.id_matiere = m.id_matiere
                        $whereClause
                        GROUP BY s.id_matiere
                        ORDER BY temps_total DESC
                        LIMIT 5";
        $stmt = $pdo->prepare($sqlMatieres);
        $stmt->execute($params);
        $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $matieres = [];
    }
    
    return [
        'temps_total' => $temps['total_minutes'] ?? 0,
        'evenements_termines' => $evenements['total'] ?? 0,
        'matieres_etudiees' => $matieres
    ];
}

// Fonction pour enregistrer du temps d'étude
function enregistrerTempsEtude($id_utilisateur, $data) {
    global $pdo;
    
    // Vérifier et créer la table si elle n'existe pas
    try {
        $pdo->query("SELECT 1 FROM suivi_etude LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS suivi_etude (
            id_suivi INT AUTO_INCREMENT PRIMARY KEY,
            id_utilisateur INT NOT NULL,
            id_matiere INT NULL,
            date DATE NOT NULL,
            temps_minutes INT NOT NULL,
            description VARCHAR(255) NULL,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    $sql = "INSERT INTO suivi_etude (id_utilisateur, id_matiere, date, temps_minutes, description)
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $id_utilisateur,
        $data['id_matiere'] ?? null,
        $data['date'] ?? date('Y-m-d'),
        $data['temps_minutes'],
        $data['description'] ?? null
    ]);
}

// Fonction pour mettre à jour un événement
function mettreAJourEvenement($id_evenement, $data) {
    global $pdo;
    
    // Vérifier que l'utilisateur possède cet événement
    $sqlCheck = "SELECT id_utilisateur FROM programme_etude WHERE id_evenement = ?";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$id_evenement]);
    $evenement = $stmtCheck->fetch();
    
    if (!$evenement) {
        return ['success' => false, 'message' => 'Événement non trouvé'];
    }
    
    // Construire la requête de mise à jour
    $updates = [];
    $params = [];
    
    $champs = [
        'id_type', 'titre', 'description', 'id_matiere', 
        'date_debut', 'date_fin', 'couleur_perso', 'lieu', 
        'priorite', 'statut', 'rappel_minutes', 'recurrence'
    ];
    
    foreach ($champs as $champ) {
        if (isset($data[$champ])) {
            $updates[] = "$champ = ?";
            $params[] = $data[$champ];
        }
    }
    
    if (empty($updates)) {
        return ['success' => false, 'message' => 'Aucune donnée à mettre à jour'];
    }
    
    $params[] = $id_evenement;
    $sql = "UPDATE programme_etude SET " . implode(', ', $updates) . " WHERE id_evenement = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute($params);
    
    if ($success && isset($data['sous_taches'])) {
        // Mettre à jour les sous-tâches
        mettreAJourSousTaches($id_evenement, $data['sous_taches']);
    }
    
    return [
        'success' => $success,
        'message' => $success ? 'Événement mis à jour' : 'Erreur lors de la mise à jour'
    ];
}

// Fonction pour mettre à jour les sous-tâches
function mettreAJourSousTaches($id_evenement, $sous_taches) {
    global $pdo;
    
    // Supprimer les anciennes sous-tâches
    $pdo->prepare("DELETE FROM sous_tache WHERE id_evenement = ?")->execute([$id_evenement]);
    
    // Ajouter les nouvelles si elles existent
    if (!empty($sous_taches)) {
        ajouterSousTaches($id_evenement, $sous_taches);
    }
}

// Fonction pour supprimer un événement
function supprimerEvenement($id_evenement, $id_utilisateur) {
    global $pdo;
    
    // Supprimer d'abord les sous-tâches
    $pdo->prepare("DELETE FROM sous_tache WHERE id_evenement = ?")->execute([$id_evenement]);
    
    // Puis supprimer l'événement
    $sql = "DELETE FROM programme_etude WHERE id_evenement = ? AND id_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$id_evenement, $id_utilisateur]);
    
    return [
        'success' => $success,
        'message' => $success ? 'Événement supprimé' : 'Erreur lors de la suppression'
    ];
}

// Fonction pour récupérer les événements à venir (prochains 7 jours)
function getEvenementsAVenir($id_utilisateur, $jours = 7) {
    global $pdo;
    
    $sql = "SELECT pe.*, te.nom_type, te.couleur as couleur_type, te.icone,
                   m.nom_matiere, m.description as description_matiere
            FROM programme_etude pe
            JOIN type_evenement te ON pe.id_type = te.id_type
            LEFT JOIN matiere m ON pe.id_matiere = m.id_matiere
            WHERE pe.id_utilisateur = ?
            AND pe.date_debut >= CURDATE()
            AND pe.date_debut <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
            AND pe.statut = 'planifié'
            ORDER BY pe.date_debut, pe.priorite DESC
            LIMIT 20";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur, $jours]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les rappels d'événements
function getRappelsUtilisateur($id_utilisateur) {
    global $pdo;
    
    $sql = "SELECT pe.*, te.nom_type, te.icone,
                   m.nom_matiere,
                   TIMESTAMPDIFF(MINUTE, NOW(), DATE_SUB(pe.date_debut, INTERVAL pe.rappel_minutes MINUTE)) as minutes_avant_rappel
            FROM programme_etude pe
            JOIN type_evenement te ON pe.id_type = te.id_type
            LEFT JOIN matiere m ON pe.id_matiere = m.id_matiere
            WHERE pe.id_utilisateur = ?
            AND pe.rappel_minutes IS NOT NULL
            AND pe.statut = 'planifié'
            AND DATE_SUB(pe.date_debut, INTERVAL pe.rappel_minutes MINUTE) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 MINUTE)
            ORDER BY pe.date_debut";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer le temps d'étude par jour de la semaine
function getTempsEtudeParJour($id_utilisateur, $semaines = 4) {
    global $pdo;
    
    // Vérifier si la table existe
    try {
        $pdo->query("SELECT 1 FROM suivi_etude LIMIT 1");
    } catch (Exception $e) {
        return [];
    }
    
    $sql = "SELECT 
                DAYNAME(s.date) as jour,
                SUM(s.temps_minutes) as total_minutes,
                COUNT(DISTINCT s.id_matiere) as matieres_etudiees
            FROM suivi_etude s
            WHERE s.id_utilisateur = ?
            AND s.date >= DATE_SUB(CURDATE(), INTERVAL ? WEEK)
            GROUP BY DAYOFWEEK(s.date), DAYNAME(s.date)
            ORDER BY DAYOFWEEK(s.date)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur, $semaines]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les objectifs d'étude
function getObjectifsEtude($id_utilisateur) {
    global $pdo;
    
    // Vérifier si la table existe
    try {
        $pdo->query("SELECT 1 FROM objectif_etude LIMIT 1");
    } catch (Exception $e) {
        return [];
    }
    
    $sql = "SELECT * FROM objectif_etude 
            WHERE id_utilisateur = ?
            ORDER BY 
                CASE statut 
                    WHEN 'actif' THEN 1
                    WHEN 'atteint' THEN 2
                    ELSE 3
                END,
                date_fin ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour créer un nouvel objectif d'étude
function creerObjectifEtude($id_utilisateur, $data) {
    global $pdo;
    
    // Vérifier et créer la table si elle n'existe pas
    try {
        $pdo->query("SELECT 1 FROM objectif_etude LIMIT 1");
    } catch (Exception $e) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS objectif_etude (
            id_objectif INT AUTO_INCREMENT PRIMARY KEY,
            id_utilisateur INT NOT NULL,
            titre VARCHAR(100) NOT NULL,
            description TEXT,
            type_objectif ENUM('journalier','hebdomadaire','mensuel','semestriel') NOT NULL,
            matieres_concernes TEXT,
            date_debut DATE NOT NULL,
            date_fin DATE NULL,
            progres INT DEFAULT 0,
            statut ENUM('actif','atteint','abandonné') DEFAULT 'actif',
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    $sql = "INSERT INTO objectif_etude (id_utilisateur, titre, description, type_objectif, 
                                         matieres_concernes, date_debut, date_fin, progres, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        $id_utilisateur,
        $data['titre'],
        $data['description'] ?? null,
        $data['type_objectif'],
        $data['matieres_concernes'] ?? null,
        $data['date_debut'],
        $data['date_fin'] ?? null,
        $data['progres'] ?? 0,
        $data['statut'] ?? 'actif'
    ]);
    
    return [
        'success' => $success,
        'id_objectif' => $success ? $pdo->lastInsertId() : null
    ];
}

// Fonction pour récupérer le résumé du mois
function getResumeMensuel($id_utilisateur, $mois = null, $annee = null) {
    global $pdo;
    
    if (!$mois) $mois = date('m');
    if (!$annee) $annee = date('Y');
    
    // Temps d'étude total (si la table existe)
    $tableSuiviExiste = false;
    try {
        $pdo->query("SELECT 1 FROM suivi_etude LIMIT 1");
        $tableSuiviExiste = true;
    } catch (Exception $e) {}
    
    if ($tableSuiviExiste) {
        $sqlTemps = "SELECT COALESCE(SUM(temps_minutes), 0) as total_minutes
                     FROM suivi_etude
                     WHERE id_utilisateur = ?
                     AND MONTH(date) = ? AND YEAR(date) = ?";
        $stmt = $pdo->prepare($sqlTemps);
        $stmt->execute([$id_utilisateur, $mois, $annee]);
        $temps = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $temps = ['total_minutes' => 0];
    }
    
    // Événements terminés
    $sqlEvenements = "SELECT COUNT(*) as total, te.nom_type
                      FROM programme_etude pe
                      JOIN type_evenement te ON pe.id_type = te.id_type
                      WHERE pe.id_utilisateur = ?
                      AND pe.statut = 'terminé'
                      AND MONTH(pe.date_debut) = ? AND YEAR(pe.date_debut) = ?
                      GROUP BY pe.id_type";
    $stmt = $pdo->prepare($sqlEvenements);
    $stmt->execute([$id_utilisateur, $mois, $annee]);
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Matières les plus étudiées (si la table existe)
    if ($tableSuiviExiste) {
        $sqlMatieres = "SELECT m.nom_matiere, SUM(s.temps_minutes) as temps_total
                        FROM suivi_etude s
                        LEFT JOIN matiere m ON s.id_matiere = m.id_matiere
                        WHERE s.id_utilisateur = ?
                        AND MONTH(s.date) = ? AND YEAR(s.date) = ?
                        GROUP BY s.id_matiere
                        ORDER BY temps_total DESC
                        LIMIT 5";
        $stmt = $pdo->prepare($sqlMatieres);
        $stmt->execute([$id_utilisateur, $mois, $annee]);
        $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $matieres = [];
    }
    
    // Tendance (comparaison avec le mois précédent)
    $tendance = [];
    if ($tableSuiviExiste) {
        $sqlTendance = "SELECT 
                            MONTH(date) as mois,
                            SUM(temps_minutes) as total
                        FROM suivi_etude
                        WHERE id_utilisateur = ?
                        AND YEAR(date) = ?
                        AND MONTH(date) IN (?, ?)
                        GROUP BY MONTH(date)
                        ORDER BY mois";
        $stmt = $pdo->prepare($sqlTendance);
        $stmt->execute([$id_utilisateur, $annee, $mois, $mois - 1]);
        $tendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return [
        'temps_total' => $temps['total_minutes'] ?? 0,
        'evenements_termines' => $evenements,
        'matieres_etudiees' => $matieres,
        'tendance' => $tendance
    ];
}

// Fonction pour générer un rapport d'étude personnalisé
function genererRapportEtude($id_utilisateur, $debut, $fin) {
    global $pdo;
    
    $rapport = [
        'periode' => ['debut' => $debut, 'fin' => $fin],
        'statistiques' => [],
        'recommendations' => []
    ];
    
    // Vérifier si la table suivi_etude existe
    try {
        $pdo->query("SELECT 1 FROM suivi_etude LIMIT 1");
        $tableSuiviExiste = true;
    } catch (Exception $e) {
        $tableSuiviExiste = false;
    }
    
    if ($tableSuiviExiste) {
        // Statistiques générales
        $sqlStats = "SELECT 
                        COUNT(DISTINCT s.id_matiere) as matieres_etudiees,
                        SUM(s.temps_minutes) as temps_total,
                        AVG(s.temps_minutes) as temps_moyen_jour,
                        MAX(s.temps_minutes) as temps_max_jour
                     FROM suivi_etude s
                     WHERE s.id_utilisateur = ?
                     AND s.date BETWEEN ? AND ?";
        $stmt = $pdo->prepare($sqlStats);
        $stmt->execute([$id_utilisateur, $debut, $fin]);
        $rapport['statistiques'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Matières avec le plus de temps
        $sqlTopMatieres = "SELECT 
                              m.nom_matiere,
                              SUM(s.temps_minutes) as temps_total,
                              ROUND(SUM(s.temps_minutes) * 100.0 / (SELECT SUM(temps_minutes) 
                                  FROM suivi_etude WHERE id_utilisateur = ? AND date BETWEEN ? AND ?), 1) as pourcentage
                           FROM suivi_etude s
                           LEFT JOIN matiere m ON s.id_matiere = m.id_matiere
                           WHERE s.id_utilisateur = ?
                           AND s.date BETWEEN ? AND ?
                           GROUP BY s.id_matiere
                           ORDER BY temps_total DESC
                           LIMIT 5";
        $stmt = $pdo->prepare($sqlTopMatieres);
        $stmt->execute([$id_utilisateur, $debut, $fin, $id_utilisateur, $debut, $fin]);
        $rapport['top_matieres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Jours les plus productifs
        $sqlJoursProductifs = "SELECT 
                                  date,
                                  SUM(temps_minutes) as temps_jour,
                                  COUNT(DISTINCT id_matiere) as matieres_jour
                               FROM suivi_etude
                               WHERE id_utilisateur = ?
                               AND date BETWEEN ? AND ?
                               GROUP BY date
                               ORDER BY temps_jour DESC
                               LIMIT 5";
        $stmt = $pdo->prepare($sqlJoursProductifs);
        $stmt->execute([$id_utilisateur, $debut, $fin]);
        $rapport['jours_productifs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recommandations basées sur les données
        $rapport['recommendations'] = genererRecommandations($rapport['statistiques'], $rapport['top_matieres']);
    } else {
        $rapport['statistiques'] = [
            'matieres_etudiees' => 0,
            'temps_total' => 0,
            'temps_moyen_jour' => 0,
            'temps_max_jour' => 0
        ];
        $rapport['top_matieres'] = [];
        $rapport['jours_productifs'] = [];
        $rapport['recommendations'] = [];
    }
    
    return $rapport;
}

// Fonction pour générer des recommandations intelligentes
function genererRecommandations($stats, $top_matieres) {
    $recommandations = [];
    
    if ($stats['temps_moyen_jour'] < 60) {
        $recommandations[] = [
            'type' => 'temps',
            'message' => 'Votre temps d\'étude quotidien moyen est faible. Essayez d\'étudier au moins 2 heures par jour.',
            'priorite' => 'haute'
        ];
    }
    
    if ($stats['matieres_etudiees'] < 3) {
        $recommandations[] = [
            'type' => 'diversite',
            'message' => 'Vous étudiez peu de matières différentes. Essayez de diversifier votre apprentissage.',
            'priorite' => 'moyenne'
        ];
    }
    
    if (!empty($top_matieres)) {
        $top_matiere = $top_matieres[0];
        if ($top_matiere['pourcentage'] > 50) {
            $recommandations[] = [
                'type' => 'equilibre',
                'message' => 'Vous consacrez plus de 50% de votre temps à ' . $top_matiere['nom_matiere'] . '. Pensez à équilibrer avec d\'autres matières.',
                'priorite' => 'moyenne'
            ];
        }
    }
    
    // Recommandation positive si bon temps d'étude
    if ($stats['temps_total'] > 1000) { // Plus de 16 heures
        $recommandations[] = [
            'type' => 'encouragement',
            'message' => 'Excellent travail ! Vous avez dépassé les 16 heures d\'étude cette période.',
            'priorite' => 'faible'
        ];
    }
    
    return $recommandations;
}

// Fonction pour exporter le programme au format iCal
function exporterCalendrierICal($id_utilisateur) {
    global $pdo;
    
    $sql = "SELECT pe.*, te.nom_type, m.nom_matiere
            FROM programme_etude pe
            JOIN type_evenement te ON pe.id_type = te.id_type
            LEFT JOIN matiere m ON pe.id_matiere = m.id_matiere
            WHERE pe.id_utilisateur = ?
            ORDER BY pe.date_debut";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur]);
    $evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Générer le contenu iCal
    $ical = "BEGIN:VCALENDAR\r\n";
    $ical .= "VERSION:2.0\r\n";
    $ical .= "PRODID:-//TDSI.ai//Mon Programme//FR\r\n";
    $ical .= "CALSCALE:GREGORIAN\r\n";
    $ical .= "METHOD:PUBLISH\r\n";
    $ical .= "X-WR-CALNAME:Mon Programme d'Étude TDSI.ai\r\n";
    $ical .= "X-WR-TIMEZONE:Europe/Paris\r\n";
    
    foreach ($evenements as $event) {
        $uid = $event['id_evenement'] . '@tdsi.ai';
        $dtstart = date('Ymd\THis', strtotime($event['date_debut']));
        $dtend = $event['date_fin'] ? date('Ymd\THis', strtotime($event['date_fin'])) : $dtstart;
        
        $summary = htmlspecialchars($event['titre']);
        $description = htmlspecialchars($event['description'] ?? '');
        
        if ($event['nom_matiere']) {
            $description = "Matière: " . $event['nom_matiere'] . "\n" . $description;
        }
        
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:" . $uid . "\r\n";
        $ical .= "DTSTAMP:" . date('Ymd\THis') . "\r\n";
        $ical .= "DTSTART:" . $dtstart . "\r\n";
        $ical .= "DTEND:" . $dtend . "\r\n";
        $ical .= "SUMMARY:" . $summary . "\r\n";
        
        if ($description) {
            $ical .= "DESCRIPTION:" . str_replace(["\r", "\n"], ['', '\\n'], $description) . "\r\n";
        }
        
        if ($event['lieu']) {
            $ical .= "LOCATION:" . htmlspecialchars($event['lieu']) . "\r\n";
        }
        
        $ical .= "CATEGORIES:" . $event['nom_type'] . "\r\n";
        $ical .= "PRIORITY:" . ($event['priorite'] == 'haute' ? '1' : ($event['priorite'] == 'moyenne' ? '3' : '5')) . "\r\n";
        $ical .= "END:VEVENT\r\n";
    }
    
    $ical .= "END:VCALENDAR\r\n";
    
    return $ical;
}

// Fonction utilitaire pour générer une palette de couleurs harmonieuse
function genererPaletteCouleurs($baseCouleur = '#133ebe') {
    // Convertir hex en RGB
    list($r, $g, $b) = sscanf($baseCouleur, "#%02x%02x%02x");
    
    // Générer des couleurs complémentaires
    $palette = [
        'primaire' => $baseCouleur,
        'secondaire' => sprintf("#%02x%02x%02x", 
            min(255, $r + 30), 
            min(255, $g + 30), 
            min(255, $b + 30)),
        'accent' => sprintf("#%02x%02x%02x", 
            ($r + 128) % 256, 
            ($g + 64) % 256, 
            ($b + 32) % 256),
        'clair' => sprintf("#%02x%02x%02x", 
            min(255, $r + 100), 
            min(255, $g + 100), 
            min(255, $b + 100)),
        'foncé' => sprintf("#%02x%02x%02x", 
            max(0, $r - 50), 
            max(0, $g - 50), 
            max(0, $b - 50))
    ];
    
    return $palette;
}

// Fonction pour obtenir une couleur aléatoire harmonieuse
function getCouleurAleatoire() {
    $couleurs = [
        '#133ebe', '#0abb77', '#fa9016', '#f6c23e', '#e74a3b',
        '#3498db', '#2ecc71', '#9b59b6', '#1abc9c', '#d35400',
        '#c0392b', '#8e44ad', '#16a085', '#27ae60', '#2980b9'
    ];
    
    return $couleurs[array_rand($couleurs)];
}

// Fonction pour formater la durée en heures/minutes
function formaterDuree($minutes) {
    if ($minutes < 60) {
        return $minutes . ' min';
    }
    
    $heures = floor($minutes / 60);
    $minutes_restantes = $minutes % 60;
    
    if ($minutes_restantes == 0) {
        return $heures . 'h';
    }
    
    return $heures . 'h ' . $minutes_restantes . 'min';
}

// Fonction pour ajouter un cours à l'emploi du temps
function ajouterCoursEmploiTemps($data) {
    global $pdo;
    
    $sql = "INSERT INTO emploi_temps (
                id_utilisateur, jour_semaine, heure_debut, heure_fin, 
                titre, description, id_matiere, couleur, recurrence
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['id_utilisateur'],
        $data['jour_semaine'],
        $data['heure_debut'],
        $data['heure_fin'],
        $data['titre'],
        $data['description'] ?? null,
        $data['id_matiere'] ?? null,
        $data['couleur'] ?? '#133ebe',
        $data['recurrence'] ?? 'toutes_semaines'
    ]);
}
?>