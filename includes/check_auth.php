<?php
function redirigerSiNonConnecte($urlRedirection = '../connexion.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . $urlRedirection);
        exit;
    }
}

function obtenirUtilisateurConnecte() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Vérifier que l'utilisateur existe dans la session
    if (isset($_SESSION['utilisateur']) && is_array($_SESSION['utilisateur'])) {
        return $_SESSION['utilisateur'];
    }
    
    // Si l'utilisateur n'est pas dans la session, le récupérer depuis la base de données
    if (isset($_SESSION['user_id'])) {
        require_once 'config.php';
        global $pdo;
        
        // REQUÊTE AVEC JOINTURE CORRECTE
        $sql = "SELECT u.*, n.nom_level, n.code_level 
                FROM utilisateur u 
                LEFT JOIN niveau n ON u.id_niveau = n.id_niveau 
                WHERE u.id_utilisateur = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($utilisateur) {
            $_SESSION['utilisateur'] = $utilisateur;
            return $utilisateur;
        }
    }
    
    // Rediriger vers la connexion si aucun utilisateur n'est trouvé
    header('Location: ../connexion.php');
    exit;
}

function obtenirUtilisateurId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['user_id'] ?? null;
}

function estAdministrateur() {
    $utilisateur = obtenirUtilisateurConnecte();
    return isset($utilisateur['role']) && $utilisateur['role'] === 'admin';
}

function redirigerSiNonAdmin($urlRedirection = '../index.php') {
    if (!estAdministrateur()) {
        header('Location: ' . $urlRedirection);
        exit;
    }
}

?>