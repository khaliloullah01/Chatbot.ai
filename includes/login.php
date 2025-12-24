<?php
// login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    // Validation
    $erreurs = [];

    if (empty($email) || empty($password)) {
        $erreurs[] = "Veuillez remplir tous les champs.";
    }

    if (empty($erreurs)) {
        try {
            // Vérifier l'utilisateur
            $stmt = $pdo->prepare("
                SELECT u.*, n.nom_level 
                FROM utilisateur u 
                LEFT JOIN niveau n ON u.id_niveau = n.id_niveau 
                WHERE u.email = ?
            ");
            $stmt->execute([$email]);
            $utilisateur = $stmt->fetch();

            if ($utilisateur) {
                if (password_verify($password, $utilisateur['password_hash'])) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $utilisateur['id_utilisateur'];
                    $_SESSION['user_email'] = $utilisateur['email'];
                    $_SESSION['user_prenom'] = $utilisateur['prenom'];
                    $_SESSION['user_nom'] = $utilisateur['nom'];
                    $_SESSION['user_niveau'] = $utilisateur['nom_level'];
                    $_SESSION['user_role'] = $utilisateur['role'];
                    
                    // "Se souvenir de moi"
                    if ($remember_me) {
                        $token = bin2hex(random_bytes(32));
                        $expire = time() + (30 * 24 * 60 * 60); // 30 jours
                        
                        setcookie('remember_token', $token, $expire, '/');
                        setcookie('user_id', $utilisateur['id_utilisateur'], $expire, '/');
                        
                        // Stocker le token en base (optionnel)
                        $stmt = $pdo->prepare("UPDATE utilisateur SET remember_token = ? WHERE id_utilisateur = ?");
                        $stmt->execute([$token, $utilisateur['id_utilisateur']]);
                    }
                    
                    // Redirection vers le chatbot
                    header('Location: ../chatbot.php');
                    exit;
                } else {
                    $erreurs[] = "Mot de passe incorrect.";
                    $_SESSION['password_error'] = true;
                }
            } else {
                $erreurs[] = "Aucun compte trouvé avec cet email.";
                $_SESSION['email_not_found'] = true;
            }
            
        } catch (PDOException $e) {
            $erreurs[] = "Erreur de connexion : " . $e->getMessage();
        }
    }

    // Stocker les erreurs
    if (!empty($erreurs)) {
        $_SESSION['login_errors'] = $erreurs;
        $_SESSION['old_login_data'] = ['email' => $email];
        header('Location: ../connexion.php');
        exit;
    }
} else {
    header('Location: ../connexion.php');
    exit;
}
?>