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

$id_matiere = intval($_GET['id']);

try {
    // Vérifier possession
    $sql_verif = "SELECT id_matiere FROM matiere WHERE id_matiere = ? AND id_utilisateur = ?";
    $stmt_verif = $pdo->prepare($sql_verif);
    $stmt_verif->execute([$id_matiere, $user_id]);
    
    if (!$stmt_verif->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Non autorisé']);
        exit;
    }
    
    // Supprimer la matière (CASCADE supprimera chapitres et ressources associés)
    $sql = "DELETE FROM matiere WHERE id_matiere = ? AND id_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id_matiere, $user_id])) {
        echo json_encode(['success' => true, 'message' => 'Matière supprimée']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>