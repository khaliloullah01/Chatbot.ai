<?php
require_once 'config.php';
require_once 'check_auth.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_matiere = $data['id_matiere'] ?? null;
    
    if (!$id_matiere) {
        echo json_encode(['success' => false, 'message' => 'ID matière manquant']);
        exit;
    }
    
    // Inclure les fonctions
    require_once 'fonctions_bibliotheque.php';
    
    try {
        $resultat = toggleFavori($user_id, $id_matiere);
        
        echo json_encode([
            'success' => true,
            'action' => $resultat,
            'message' => $resultat === 'ajoute' ? 'Ajouté aux favoris' : 'Retiré des favoris'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>