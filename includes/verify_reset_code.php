<?php
// includes/verify_reset_code.php
session_start();
require_once 'config.php';

$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $code = trim($_POST['code']);
    
    if (empty($code) || strlen($code) !== 6) {
        $error = "Veuillez entrer un code valide à 6 chiffres.";
    } else {
        try {
            // Vérifier le code
            $stmt = $pdo->prepare("SELECT id_utilisateur, verification_code, code_expires FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && $user['verification_code'] === $code) {
                // Vérifier l'expiration
                $now = date('Y-m-d H:i:s');
                if ($user['code_expires'] >= $now) {
                    // Code valide, créer un token de réinitialisation
                    $reset_token = bin2hex(random_bytes(32));
                    $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    $stmt = $pdo->prepare("UPDATE utilisateur SET reset_token = ?, reset_token_expires = ? WHERE id_utilisateur = ?");
                    $stmt->execute([$reset_token, $reset_expires, $user['id_utilisateur']]);
                    
                    $_SESSION['reset_token'] = $reset_token;
                    $_SESSION['reset_verified'] = true;
                    $success = true;
                    
                } else {
                    $error = "Le code de vérification a expiré. Veuillez en demander un nouveau.";
                }
            } else {
                $error = "Code de vérification incorrect.";
            }
            
        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
        }
    }
    
    if ($success) {
        header('Location: ../nouveau_mot_de_passe.php');
        exit();
    } else {
        $_SESSION['reset_errors'] = [$error];
        header('Location: ../verifier_code.php');
        exit();
    }
} else {
    header('Location: ../connexion.php');
    exit();
}
?>