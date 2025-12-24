<?php
// includes/voir_ressource.php
require_once 'config.php';
require_once 'check_auth.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID ressource invalide');
}

$id_ressource = intval($_GET['id']);

try {
    $sql = "SELECT * FROM ressource WHERE id_ressource = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_ressource]);
    $ressource = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ressource) {
        die('Ressource non trouvée');
    }
    
    $chemin = $ressource['chemin_ressource'];
    
    // Vérifier si le fichier existe
    if (file_exists($chemin) || filter_var($chemin, FILTER_VALIDATE_URL)) {
        // Rediriger vers le fichier
        header("Location: " . $chemin);
        exit;
    } else {
        // Si c'est une URL, rediriger directement
        if (strpos($chemin, 'http') === 0) {
            header("Location: " . $chemin);
            exit;
        } else {
            die('Fichier non trouvé: ' . $chemin);
        }
    }
    
} catch (PDOException $e) {
    die('Erreur: ' . $e->getMessage());
}
?>