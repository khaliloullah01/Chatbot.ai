<?php
require_once 'config.php';
require_once 'check_auth.php';

header('Content-Type: application/json');

if (!isset($_GET['id_matiere']) || !is_numeric($_GET['id_matiere'])) {
    echo json_encode(['error' => 'ID matière invalide']);
    exit;
}

$id_matiere = intval($_GET['id_matiere']);
$user_id = obtenirUtilisateurId();

try {
    // D'abord, récupérer le nom de la matière avec vérification d'accès
    $sql_matiere = "SELECT m.*, u.id_utilisateur as proprietaire_id 
                    FROM matiere m 
                    LEFT JOIN utilisateur u ON m.id_utilisateur = u.id_utilisateur 
                    WHERE m.id_matiere = ? 
                    AND (m.est_publique = 1 OR m.id_utilisateur = ?)";
    
    $stmt_matiere = $pdo->prepare($sql_matiere);
    $stmt_matiere->execute([$id_matiere, $user_id]);
    $matiere = $stmt_matiere->fetch(PDO::FETCH_ASSOC);
    
    if (!$matiere) {
        echo json_encode(['error' => 'Matière non trouvée ou non autorisée']);
        exit;
    }
    
    // Vérifier si c'est une matière personnelle
    $est_personnelle = !empty($matiere['id_utilisateur']);
    
    // Récupérer les leçons (type = 'lecon')
    $sql_lecons = "SELECT * FROM chapitre 
                   WHERE id_matiere = ? 
                   AND type_chapitre = 'lecon'
                   AND (id_utilisateur IS NULL OR id_utilisateur = ?)
                   ORDER BY ordre_chapitre";
    
    $stmt_lecons = $pdo->prepare($sql_lecons);
    $stmt_lecons->execute([$id_matiere, $user_id]);
    $lecons = $stmt_lecons->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les TP (type = 'tp')
    $sql_tp = "SELECT * FROM chapitre 
               WHERE id_matiere = ? 
               AND type_chapitre = 'tp'
               AND (id_utilisateur IS NULL OR id_utilisateur = ?)
               ORDER BY ordre_chapitre";
    
    $stmt_tp = $pdo->prepare($sql_tp);
    $stmt_tp->execute([$id_matiere, $user_id]);
    $tp = $stmt_tp->fetchAll(PDO::FETCH_ASSOC);
    
    $contenu = [
        'matiere' => $matiere['nom_matiere'],
        'est_personnelle' => $est_personnelle,
        'proprietaire_id' => $matiere['proprietaire_id'],
        'lecons' => [],
        'tp' => [],
        'totalFichiers' => 0,
        'dureeEstimee' => 'Environ ' . ((count($lecons) * 2) + (count($tp) * 1.5)) . ' heures'
    ];
    
    // Fonction pour traiter les chapitres
    function traiterChapitres($chapitres, $pdo, $user_id) {
        $resultat = [];
        $totalFichiers = 0;
        
        foreach ($chapitres as $chapitre) {
            $sql_ressources = "SELECT * FROM ressource 
                               WHERE id_chapitre = ? 
                               AND (id_utilisateur IS NULL OR id_utilisateur = ?)
                               ORDER BY titre_ressource";
            
            $stmt_ressources = $pdo->prepare($sql_ressources);
            $stmt_ressources->execute([$chapitre['id_chapitre'], $user_id]);
            $ressources = $stmt_ressources->fetchAll(PDO::FETCH_ASSOC);
            
            $ressources_formatees = [];
            foreach ($ressources as $ressource) {
                $chemin = $ressource['chemin_ressource'];
                $extension = pathinfo($chemin, PATHINFO_EXTENSION);
                $nom_fichier = pathinfo($chemin, PATHINFO_FILENAME);
                
                $ressources_formatees[] = [
                    'id' => $ressource['id_ressource'],
                    'nom' => $ressource['titre_ressource'] ?: $nom_fichier,
                    'type' => $extension ?: 'fichier',
                    'chemin' => $ressource['chemin_ressource'],
                    'taille' => 'N/A',
                    'est_personnelle' => !empty($ressource['id_utilisateur'])
                ];
            }
            
            $resultat[] = [
                'id' => $chapitre['id_chapitre'],
                'titre' => $chapitre['titre_chapitre'],
                'description' => $chapitre['contenu'],
                'ordre' => $chapitre['ordre_chapitre'],
                'est_personnel' => !empty($chapitre['id_utilisateur']),
                'ressources' => $ressources_formatees
            ];
            
            $totalFichiers += count($ressources_formatees);
        }
        
        return ['chapitres' => $resultat, 'total' => $totalFichiers];
    }
    
    // Traiter les leçons
    $resultatLecons = traiterChapitres($lecons, $pdo, $user_id);
    $contenu['lecons'] = $resultatLecons['chapitres'];
    
    // Traiter les TP
    $resultatTP = traiterChapitres($tp, $pdo, $user_id);
    $contenu['tp'] = $resultatTP['chapitres'];
    
    // Total des fichiers
    $contenu['totalFichiers'] = $resultatLecons['total'] + $resultatTP['total'];
    
    echo json_encode($contenu);
    
} catch (PDOException $e) {
    error_log("Erreur récupération contenu matière: " . $e->getMessage());
    echo json_encode(['error' => 'Erreur de chargement: ' . $e->getMessage()]);
}
?>