<?php
// includes/forgot_password.php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $action = $_POST['action'] ?? 'send_code';
    
    $errors = [];
    $success = false;
    
    // Validation de l'email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer une adresse email valide.";
    }
    
    if (empty($errors)) {
        try {
            // Vérifier si l'email existe
            $stmt = $pdo->prepare("SELECT id_utilisateur, prenom, nom FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                if ($action === 'send_code') {
                    // Générer un code de vérification
                    $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $code_expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    
                    // Sauvegarder le code dans la base de données
                    $stmt = $pdo->prepare("UPDATE utilisateur SET verification_code = ?, code_expires = ? WHERE id_utilisateur = ?");
                    $stmt->execute([$verification_code, $code_expires, $user['id_utilisateur']]);
                    
                    // Enregistrer dans la session
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_user_id'] = $user['id_utilisateur'];
                    $_SESSION['verification_sent'] = true;
                    $_SESSION['verification_code'] = $verification_code; // Pour test seulement
                    
                    // Envoyer l'email (simulation pour le moment)
                    // Dans un environnement réel, utilisez PHPMailer ou autre
                    $to = $email;
                    $subject = "Code de vérification - Réinitialisation de mot de passe";
                    $message = "Bonjour {$user['prenom']},\n\n";
                    $message .= "Vous avez demandé à réinitialiser votre mot de passe.\n";
                    $message .= "Votre code de vérification est : {$verification_code}\n";
                    $message .= "Ce code expirera dans 15 minutes.\n\n";
                    $message .= "Si vous n'avez pas fait cette demande, ignorez simplement cet email.\n\n";
                    $message .= "Cordialement,\nL'équipe TDSI.ai";
                    $headers = "From: no-reply@tdsi.ai\r\n";
                    
                    // En production, décommentez cette ligne :
                    // mail($to, $subject, $message, $headers);
                    
                    $success = true;
                    $_SESSION['reset_message'] = "Un code de vérification a été envoyé à {$email}";
                    $_SESSION['message_type'] = 'success';
                    
                } elseif ($action === 'verify_code') {
                    $code = trim($_POST['code']);
                    
                    if (empty($code)) {
                        $errors[] = "Veuillez entrer le code de vérification.";
                    } else {
                        // Vérifier le code
                        $stmt = $pdo->prepare("SELECT id_utilisateur, verification_code, code_expires FROM utilisateur WHERE email = ?");
                        $stmt->execute([$email]);
                        $userData = $stmt->fetch();
                        
                        if ($userData && $userData['verification_code'] === $code) {
                            // Vérifier l'expiration
                            $now = date('Y-m-d H:i:s');
                            if ($userData['code_expires'] >= $now) {
                                // Code valide, créer un token de réinitialisation
                                $reset_token = bin2hex(random_bytes(32));
                                $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                                
                                $stmt = $pdo->prepare("UPDATE utilisateur SET reset_token = ?, reset_token_expires = ? WHERE id_utilisateur = ?");
                                $stmt->execute([$reset_token, $reset_expires, $userData['id_utilisateur']]);
                                
                                $_SESSION['reset_token'] = $reset_token;
                                $_SESSION['reset_verified'] = true;
                                $success = true;
                                
                            } else {
                                $errors[] = "Le code de vérification a expiré. Veuillez en demander un nouveau.";
                            }
                        } else {
                            $errors[] = "Code de vérification incorrect.";
                        }
                    }
                } elseif ($action === 'reset_password') {
                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];
                    $token = $_POST['token'] ?? '';
                    
                    if (empty($new_password) || empty($confirm_password)) {
                        $errors[] = "Veuillez remplir tous les champs.";
                    } elseif ($new_password !== $confirm_password) {
                        $errors[] = "Les mots de passe ne correspondent pas.";
                    } elseif (strlen($new_password) < 8) {
                        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
                    } else {
                        // Vérifier le token
                        $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE reset_token = ? AND reset_token_expires > NOW() AND email = ?");
                        $stmt->execute([$token, $email]);
                        $validToken = $stmt->fetch();
                        
                        if ($validToken) {
                            // Hasher le nouveau mot de passe
                            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                            
                            // Mettre à jour le mot de passe et nettoyer les tokens
                            $stmt = $pdo->prepare("UPDATE utilisateur SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL, verification_code = NULL, code_expires = NULL WHERE id_utilisateur = ?");
                            $stmt->execute([$password_hash, $validToken['id_utilisateur']]);
                            
                            // Nettoyer la session
                            unset($_SESSION['reset_email']);
                            unset($_SESSION['reset_token']);
                            unset($_SESSION['reset_verified']);
                            unset($_SESSION['verification_sent']);
                            
                            $success = true;
                            $_SESSION['reset_message'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                            $_SESSION['message_type'] = 'success';
                            
                        } else {
                            $errors[] = "Le lien de réinitialisation est invalide ou a expiré.";
                        }
                    }
                }
            } else {
                $errors[] = "Aucun compte trouvé avec cet email.";
            }
            
        } catch (PDOException $e) {
            error_log("Erreur réinitialisation mot de passe: " . $e->getMessage());
            $errors[] = "Une erreur est survenue. Veuillez réessayer.";
        }
    }
    
    // Stocker les erreurs ou succès dans la session
    if (!empty($errors)) {
        $_SESSION['reset_errors'] = $errors;
        $_SESSION['old_reset_data'] = ['email' => $email];
    }
    
    if ($success && $action === 'reset_password') {
        header('Location: ../connexion.php');
    } else {
        header('Location: ../mot_de_passe_oublie.php');
    }
    exit;
} else {
    header('Location: ../connexion.php');
    exit;
}
?>