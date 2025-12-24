<?php

// Inclure config.php pour avoir accès à $pdo
require_once 'config.php';

// Fonction pour obtenir la connexion PDO
function getPDO() {
    global $pdo;
    return $pdo;
}

function getCoursParNiveau() {
    $pdo = getPDO();
    if (!$pdo) {
        error_log("Erreur: Connexion PDO non disponible");
        return [];
    }

    // Récupérer l'ID utilisateur
    $user_id = 0;
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }

    try {
        // Récupérer les matières publiques ET personnelles
        $sql = "
            SELECT m.*, n.code_level, n.nom_level 
            FROM matiere m 
            LEFT JOIN niveau n ON m.niveau_id = n.id_niveau 
            WHERE m.est_publique = 1 OR m.id_utilisateur = ?
            ORDER BY 
                CASE 
                    WHEN m.id_utilisateur IS NULL THEN 0
                    ELSE 1
                END,
                n.id_niveau, 
                m.nom_matiere
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $coursParNiveau = [];

        foreach ($matieres as $matiere) {
            $niveauKey = strtolower($matiere['code_level']);

            if (!isset($coursParNiveau[$niveauKey])) {
                $coursParNiveau[$niveauKey] = [
                    'titre' => $matiere['nom_level'],
                    'cours' => []
                ];
            }

            // Compter leçons
            $sqlLecons = "SELECT COUNT(*) as nb_lecons 
                         FROM chapitre 
                         WHERE id_matiere = ? 
                         AND type_chapitre = 'lecon'
                         AND (id_utilisateur IS NULL OR id_utilisateur = ?)";

            $stmtLecons = $pdo->prepare($sqlLecons);
            $stmtLecons->execute([$matiere['id_matiere'], $user_id]);
            $nbLecons = $stmtLecons->fetch(PDO::FETCH_ASSOC)['nb_lecons'];

            // Compter TP
            $sqlTP = "SELECT COUNT(*) as nb_tp 
                     FROM chapitre 
                     WHERE id_matiere = ? 
                     AND type_chapitre = 'tp'
                     AND (id_utilisateur IS NULL OR id_utilisateur = ?)";

            $stmtTP = $pdo->prepare($sqlTP);
            $stmtTP->execute([$matiere['id_matiere'], $user_id]);
            $nbTP = $stmtTP->fetch(PDO::FETCH_ASSOC)['nb_tp'];

            // Compter ressources totales
            $sqlRessources = "SELECT COUNT(*) as nb_fichiers 
                              FROM ressource r
                              JOIN chapitre c ON r.id_chapitre = c.id_chapitre
                              WHERE c.id_matiere = ? 
                              AND (r.id_utilisateur IS NULL OR r.id_utilisateur = ?)
                              AND (c.id_utilisateur IS NULL OR c.id_utilisateur = ?)";

            $stmtRessources = $pdo->prepare($sqlRessources);
            $stmtRessources->execute([$matiere['id_matiere'], $user_id, $user_id]);
            $nbFichiers = $stmtRessources->fetch(PDO::FETCH_ASSOC)['nb_fichiers'];

            // Vérifier favoris
            $estFavori = false;
            if ($user_id > 0) {
                $sqlFavoris = "SELECT COUNT(*) as count FROM favoris 
                               WHERE id_utilisateur = ? 
                               AND type_favori = 'matiere' 
                               AND id_cible = ?";
                $stmtFavoris = $pdo->prepare($sqlFavoris);
                $stmtFavoris->execute([$user_id, $matiere['id_matiere']]);
                $resultFavoris = $stmtFavoris->fetch(PDO::FETCH_ASSOC);
                $estFavori = $resultFavoris['count'] > 0;
            }

            // Déterminer l'icône
            $icon = getIconForMatiere($matiere['nom_matiere']);

            // Déterminer la couleur
            $couleur = $niveauKey;
            if (!empty($matiere['id_utilisateur'])) {
                $couleur = $niveauKey . '-perso';
            }

            // Ajouter le cours
            $coursParNiveau[$niveauKey]['cours'][] = [
                'id' => $matiere['id_matiere'],
                'nom' => $matiere['nom_matiere'],
                'description' => $matiere['description'] ?? 'Cours de ' . $matiere['nom_matiere'],
                'icon' => $icon,
                'fichiers' => $nbFichiers,
                'lecons' => $nbLecons,
                'tp' => $nbTP,
                'couleur' => $couleur,
                'dossier' => genererNomDossier($matiere['nom_matiere']),
                'personnel' => !empty($matiere['id_utilisateur']),
                'est_publique' => $matiere['est_publique'],
                'estFavori' => $estFavori
            ];
        }

        return $coursParNiveau;

    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des cours: " . $e->getMessage());
        return [];
    }
}

// Fonction pour obtenir l'icône selon le nom de la matière
function getIconForMatiere($nomMatiere)
{
    $icons = [
        'algèbre' => 'fas fa-square-root-alt',
        'algorithm' => 'fas fa-project-diagram',
        'analyse' => 'fas fa-chart-line',
        'architecture' => 'fas fa-microchip',
        'base de données' => 'fas fa-database',
        'cryptographie' => 'fas fa-lock',
        'cryptologie' => 'fas fa-lock',
        'électricité' => 'fas fa-bolt',
        'développement web' => 'fas fa-code',
        'web' => 'fas fa-code',
        'java' => 'fas fa-coffee',
        'langage c' => 'fas fa-copyright',
        'c' => 'fas fa-copyright',
        'linux' => 'fas fa-terminal',
        'réseaux' => 'fas fa-network-wired',
        'système' => 'fas fa-desktop',
        'system' => 'fas fa-desktop',
        'intelligence artificielle' => 'fas fa-brain',
        'ia' => 'fas fa-brain',
        'gestion de projet' => 'fas fa-tasks',
        'projet' => 'fas fa-tasks',
        'big data' => 'fas fa-chart-line',
        'data' => 'fas fa-chart-line',
        'cloud computing' => 'fas fa-cloud',
        'cloud' => 'fas fa-cloud',
        'devops' => 'fas fa-cogs',
        'cybersécurité' => 'fas fa-shield-alt',
        'securite' => 'fas fa-shield-alt',
        'blockchain' => 'fas fa-link',
        'informatique quantique' => 'fas fa-atom',
        'quantique' => 'fas fa-atom',
        'programmation' => 'fas fa-laptop-code',
        'mathématiques' => 'fas fa-calculator',
        'math' => 'fas fa-calculator',
        'statistique' => 'fas fa-chart-bar',
        'probabilité' => 'fas fa-chart-bar'
    ];

    $nomMatiereLower = strtolower($nomMatiere);
    
    foreach ($icons as $keyword => $icon) {
        if (strpos($nomMatiereLower, $keyword) !== false) {
            return $icon;
        }
    }
    
    return 'fas fa-book';
}

function genererNomDossier($nomMatiere)
{
    return preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($nomMatiere));
}

// Fonctions pour ajouter des éléments personnels
function ajouterMatierePersonnelle($user_id, $nom, $description = '', $niveau_id = null)
{
    require_once 'config.php';

    try {
        $sql = "INSERT INTO matiere (nom_matiere, description, niveau_id, id_utilisateur, est_publique) 
                VALUES (?, ?, ?, ?, 0)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nom, $description, $niveau_id, $user_id]);
    } catch (PDOException $e) {
        error_log("Erreur ajout matière personnelle: " . $e->getMessage());
        return false;
    }
}

function ajouterChapitrePersonnel($user_id, $id_matiere, $titre, $ordre = 1, $contenu = '')
{
    require_once 'config.php';

    // Vérifier que l'utilisateur possède la matière
    $sql_verif = "SELECT id_matiere FROM matiere WHERE id_matiere = ? AND id_utilisateur = ?";
    $stmt_verif = $pdo->prepare($sql_verif);
    $stmt_verif->execute([$id_matiere, $user_id]);

    if (!$stmt_verif->fetch()) {
        return false; // L'utilisateur ne possède pas cette matière
    }

    try {
        $sql = "INSERT INTO chapitre (id_matiere, titre_chapitre, type_chapitre, ordre_chapitre, contenu, id_utilisateur) 
                VALUES (?, ?, 'lecon', ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_matiere, $titre, $ordre, $contenu, $user_id]);
    } catch (PDOException $e) {
        error_log("Erreur ajout chapitre personnel: " . $e->getMessage());
        return false;
    }
}

function ajouterRessourcePersonnelle($user_id, $id_chapitre, $type, $chemin, $titre)
{
    require_once 'config.php';

    // Vérifier que l'utilisateur possède le chapitre
    $sql_verif = "SELECT id_chapitre FROM chapitre WHERE id_chapitre = ? AND id_utilisateur = ?";
    $stmt_verif = $pdo->prepare($sql_verif);
    $stmt_verif->execute([$id_chapitre, $user_id]);

    if (!$stmt_verif->fetch()) {
        return false; // L'utilisateur ne possède pas ce chapitre
    }

    try {
        $sql = "INSERT INTO ressource (id_chapitre, type_ressource, chemin_ressource, titre_ressource, id_utilisateur) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_chapitre, $type, $chemin, $titre, $user_id]);
    } catch (PDOException $e) {
        error_log("Erreur ajout ressource personnelle: " . $e->getMessage());
        return false;
    }
}

// Fonction pour vérifier si une matière est en favoris
function estEnFavoris($id_utilisateur, $id_matiere) {
    $pdo = getPDO();
    if (!$pdo) {
        error_log("Erreur: Connexion PDO non disponible");
        return false;
    }
    
    $sql = "SELECT COUNT(*) as count FROM favoris 
            WHERE id_utilisateur = ? 
            AND type_favori = 'matiere' 
            AND id_cible = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur, $id_matiere]);
    $result = $stmt->fetch();
    
    return $result['count'] > 0;
}

// Fonction pour ajouter/supprimer un favori
function toggleFavori($id_utilisateur, $id_matiere) {
    $pdo = getPDO();
    if (!$pdo) {
        throw new Exception("Connexion base de données non disponible");
    }
    
    // Vérifier si déjà en favoris
    if (estEnFavoris($id_utilisateur, $id_matiere)) {
        // Supprimer le favori
        $sql = "DELETE FROM favoris 
                WHERE id_utilisateur = ? 
                AND type_favori = 'matiere' 
                AND id_cible = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_utilisateur, $id_matiere]);
        return 'supprime';
    } else {
        // Ajouter le favori
        $sql = "INSERT INTO favoris (id_utilisateur, type_favori, id_cible) 
                VALUES (?, 'matiere', ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_utilisateur, $id_matiere]);
        return 'ajoute';
    }
}

// Fonction pour récupérer les cours favoris d'un utilisateur
function getCoursFavoris($id_utilisateur) {
    $pdo = getPDO();
    if (!$pdo) {
        error_log("Erreur: Connexion PDO non disponible dans getCoursFavoris");
        return [];
    }
    
    $sql = "SELECT m.*, n.code_level, n.nom_level, f.date_favoris as date_ajout
            FROM favoris f
            JOIN matiere m ON f.id_cible = m.id_matiere
            JOIN niveau n ON m.niveau_id = n.id_niveau
            WHERE f.id_utilisateur = ? 
            AND f.type_favori = 'matiere'
            ORDER BY f.date_favoris DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur]);
    return $stmt->fetchAll();
}

// Fonction pour obtenir les statistiques de contenu (leçons/TP)
function getStatsContenuMatiere($id_matiere, $id_utilisateur) {
    $pdo = getPDO();
    
    try {
        // Compter les leçons
        $sql_lecons = "SELECT COUNT(*) as nb_lecons FROM chapitre 
                      WHERE id_matiere = ? 
                      AND type_chapitre = 'lecon'
                      AND (id_utilisateur IS NULL OR id_utilisateur = ?)";
        
        $stmt_lecons = $pdo->prepare($sql_lecons);
        $stmt_lecons->execute([$id_matiere, $id_utilisateur]);
        $nb_lecons = $stmt_lecons->fetchColumn();
        
        // Compter les TP
        $sql_tp = "SELECT COUNT(*) as nb_tp FROM chapitre 
                  WHERE id_matiere = ? 
                  AND type_chapitre = 'tp'
                  AND (id_utilisateur IS NULL OR id_utilisateur = ?)";
        
        $stmt_tp = $pdo->prepare($sql_tp);
        $stmt_tp->execute([$id_matiere, $id_utilisateur]);
        $nb_tp = $stmt_tp->fetchColumn();
        
        return [
            'lecons' => $nb_lecons,
            'tp' => $nb_tp
        ];
        
    } catch (PDOException $e) {
        error_log("Erreur stats contenu: " . $e->getMessage());
        return ['lecons' => 0, 'tp' => 0];
    }
}

// Fonction pour ajouter un TP personnel
function ajouterTPPersonnel($user_id, $id_matiere, $titre, $ordre = 1, $contenu = '')
{
    require_once 'config.php';

    // Vérifier que l'utilisateur possède la matière
    $sql_verif = "SELECT id_matiere FROM matiere WHERE id_matiere = ? AND id_utilisateur = ?";
    $stmt_verif = $pdo->prepare($sql_verif);
    $stmt_verif->execute([$id_matiere, $user_id]);

    if (!$stmt_verif->fetch()) {
        return false; // L'utilisateur ne possède pas cette matière
    }

    try {
        $sql = "INSERT INTO chapitre (id_matiere, titre_chapitre, type_chapitre, ordre_chapitre, contenu, id_utilisateur) 
                VALUES (?, ?, 'tp', ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_matiere, $titre, $ordre, $contenu, $user_id]);
    } catch (PDOException $e) {
        error_log("Erreur ajout TP personnel: " . $e->getMessage());
        return false;
    }
}

?>