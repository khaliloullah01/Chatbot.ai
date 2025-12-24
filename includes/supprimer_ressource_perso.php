<?php
require_once 'config.php';
require_once 'check_auth.php';

header('Content-Type: application/json');

redirigerSiNonConnecte();
$user_id = obtenirUtilisateurId();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit;
}

$id_ressource = intval($_GET['id']);

try {
    // Vérifier possession
    $sql_verif = "SELECT chemin_ressource FROM ressource WHERE id_ressource = ? AND id_utilisateur = ?";
    $stmt_verif = $pdo->prepare($sql_verif);
    $stmt_verif->execute([$id_ressource, $user_id]);
    $ressource = $stmt_verif->fetch(PDO::FETCH_ASSOC);
    
    if (!$ressource) {
        echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        exit;
    }
    
    // Supprimer le fichier physique
    if (file_exists($ressource['chemin_ressource'])) {
        unlink($ressource['chemin_ressource']);
    }
    
    // Supprimer de la base
    $sql = "DELETE FROM ressource WHERE id_ressource = ? AND id_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id_ressource, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Ressource supprimée']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>