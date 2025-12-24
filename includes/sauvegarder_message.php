<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'fonctions_historique.php';

header('Content-Type: application/json');

try {
    // Vérifier la méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    // Récupérer les données JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['id_conversation_courante'])) {
        throw new Exception('Utilisateur non connecté');
    }

    $id_utilisateur = $_SESSION['user_id'];
    $id_conversation = $_SESSION['id_conversation_courante'];
    $contenu = $input['contenu'] ?? '';
    $type = $input['type'] ?? 'utilisateur';

    if (empty($contenu)) {
        throw new Exception('Contenu vide');
    }

    // Sauvegarder le message
    $id_message = sauvegarderMessage($id_conversation, $contenu, $type, $type === 'utilisateur' ? $id_utilisateur : null);

    if ($id_message) {
        echo json_encode(['success' => true, 'id_message' => $id_message]);
    } else {
        throw new Exception('Erreur lors de la sauvegarde du message');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>