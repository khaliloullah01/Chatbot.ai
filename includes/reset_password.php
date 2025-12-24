<?php
// includes/reset_password.php
session_start();
require_once 'config.php';

$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($new_password) || empty($confirm_password)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($new_password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        try {
            // Vérifier le token
            $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE reset_token = ? AND reset_token_expires > NOW() AND email = ?");
            $stmt->execute([$token, $email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Hasher le nouveau mot de passe
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Mettre à jour le mot de passe et nettoyer les tokens
                $stmt = $pdo->prepare("UPDATE utilisateur SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL, verification_code = NULL, code_expires = NULL WHERE id_utilisateur = ?");
                $stmt->execute([$password_hash, $user['id_utilisateur']]);
                
                // Nettoyer la session
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_token']);
                unset($_SESSION['reset_verified']);
                unset($_SESSION['verification_sent']);
                unset($_SESSION['debug_code']);
                
                $success = true;
                $_SESSION['reset_message'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                $_SESSION['message_type'] = 'success';
                
            } else {
                $error = "Le lien de réinitialisation est invalide ou a expiré.";
            }
            
        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
        }
    }
    
    if ($success) {
        header('Location: ../connexion.php');
        exit();
    } else {
        $_SESSION['reset_errors'] = [$error];
        header('Location: ../nouveau_mot_de_passe.php');
        exit();
    }
} else {
    header('Location: ../connexion.php');
    exit();
}
?>