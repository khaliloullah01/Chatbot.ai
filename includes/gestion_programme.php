<?php
// gestion_programme.php
require_once 'config.php';
require_once 'check_auth.php';
require_once 'fonctions_programme.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

// Récupérer l'action depuis POST ou GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'ajouter_evenement':
            // Lire les données JSON
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            
            // Si json_decode échoue, essayer avec $_POST
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                $data = $_POST;
            }
            
            if (empty($data)) {
                echo json_encode(['success' => false, 'message' => 'Aucune donnée reçue']);
                break;
            }
            
            $data['id_utilisateur'] = $user_id;
            $id_evenement = ajouterEvenement($data);
            
            if ($id_evenement) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Événement ajouté avec succès',
                    'id_evenement' => $id_evenement
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }
            break;
            
        case 'get_evenements':
            $debut = $_GET['debut'] ?? null;
            $fin = $_GET['fin'] ?? null;
            $evenements = getEvenementsUtilisateur($user_id, $debut, $fin);
            echo json_encode([
                'success' => true,
                'evenements' => $evenements
            ]);
            break;
            
        case 'get_emploi_temps':
            $emploi_temps = getEmploiTempsUtilisateur($user_id);
            echo json_encode([
                'success' => true,
                'emploi_temps' => $emploi_temps
            ]);
            break;
            
        case 'sauvegarder_emploi_temps':
            // Lire les données JSON
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            
            // Si json_decode échoue, essayer avec $_POST
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                $data = $_POST;
            }
            
            // Commencer une transaction
            $pdo->beginTransaction();
            
            try {
                // Supprimer l'ancien emploi du temps
                $pdo->prepare("DELETE FROM emploi_temps WHERE id_utilisateur = ?")
                    ->execute([$user_id]);
                
                // Ajouter les nouveaux cours
                if (!empty($data['cours'])) {
                    $sql = "INSERT INTO emploi_temps (id_utilisateur, jour_semaine, heure_debut, heure_fin, titre, description, id_matiere, couleur, recurrence) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    
                    foreach ($data['cours'] as $cours) {
                        // Vérifier et formater les données
                        if (empty($cours['titre']) || empty($cours['jour_semaine']) || empty($cours['heure_debut']) || empty($cours['heure_fin'])) {
                            continue;
                        }
                        
                        // Convertir id_matiere en int si c'est une chaîne vide
                        $id_matiere = !empty($cours['id_matiere']) ? (int)$cours['id_matiere'] : null;
                        
                        $stmt->execute([
                            $user_id,
                            $cours['jour_semaine'],
                            $cours['heure_debut'],
                            $cours['heure_fin'],
                            $cours['titre'],
                            $cours['description'] ?? null,
                            $id_matiere,
                            $cours['couleur'] ?? '#133ebe',
                            'toutes_semaines'
                        ]);
                    }
                }
                
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'message' => 'Emploi du temps sauvegardé'
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur: ' . $e->getMessage()
                ]);
            }
            break;
            
        case 'get_matieres':
            $matieres = getMatieresUtilisateur($user_id);
            echo json_encode([
                'success' => true,
                'matieres' => $matieres
            ]);
            break;
            
        case 'get_types_evenement':
            $types = getTypesEvenement();
            echo json_encode([
                'success' => true,
                'types' => $types
            ]);
            break;
            
        case 'mettre_a_jour_evenement':
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);
            $id_evenement = $data['id_evenement'] ?? 0;
            unset($data['id_evenement']);
            
            $resultat = mettreAJourEvenement($id_evenement, $data);
            echo json_encode($resultat);
            break;
            
        case 'supprimer_evenement':
            $id_evenement = $_POST['id_evenement'] ?? $_GET['id_evenement'] ?? 0;
            $resultat = supprimerEvenement((int)$id_evenement, $user_id);
            echo json_encode($resultat);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    }
} catch (Exception $e) {
    error_log("Erreur gestion_programme: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>