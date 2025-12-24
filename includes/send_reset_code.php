<?php
// includes/send_reset_code.php
session_start();
require_once 'config.php';
require_once 'mail_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Inclure PHPMailer depuis le bon chemin
require_once "PHPMailer/src/PHPMailer.php";
require_once "PHPMailer/src/SMTP.php";
require_once "PHPMailer/src/Exception.php";

$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... reste du code inchangé ...
    $email = trim($_POST['email']);
    
    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer une adresse email valide.";
    } else {
        try {
            // Vérifier si l'email existe
            $stmt = $pdo->prepare("SELECT id_utilisateur, prenom, nom FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Générer un code à 6 chiffres
                $code = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                $code_expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                
                // Sauvegarder dans la base
                $stmt = $pdo->prepare("UPDATE utilisateur SET verification_code = ?, code_expires = ? WHERE id_utilisateur = ?");
                $stmt->execute([$code, $code_expires, $user['id_utilisateur']]);
                
                // Envoyer l'email avec PHPMailer
                $mail = new PHPMailer(true);
                
                try {
                    // Configuration SMTP
                    $mail->isSMTP();
                    $mail->CharSet = "utf-8";
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = SMTP_SECURE;
                    
                    $mail->Host = SMTP_HOST;
                    $mail->Port = SMTP_PORT;
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    $mail->isHTML(true);
                    
                    // Identifiants SMTP
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    
                    // Expéditeur et destinataire
                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                    $mail->addAddress($email, $user['prenom'] . ' ' . $user['nom']);
                    
                    // Sujet et contenu
                    $mail->Subject = 'Réinitialisation de votre mot de passe - TDSI.ai';
                    
                    // Contenu HTML de l'email (similaire à votre exemple)
                    $htmlContent = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background: linear-gradient(135deg, #133ebe, #0d2e8a); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
                            .code { background: #fff; border: 2px dashed #133ebe; padding: 15px; text-align: center; font-size: 32px; font-weight: bold; color: #133ebe; margin: 20px 0; letter-spacing: 5px; }
                            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h2>TDSI.ai</h2>
                                <p>Assistant IA pour étudiants de la TDSI</p>
                            </div>
                            <div class='content'>
                                <h3>Bonjour " . htmlspecialchars($user['prenom']) . ",</h3>
                                <p>Nous avons reçu une demande de réinitialisation de votre mot de passe.</p>
                                <p>Utilisez le code suivant pour réinitialiser votre mot de passe :</p>
                                <div class='code'>$code</div>
                                <p>Veuillez entrer ce code sur la page de réinitialisation pour continuer le processus.</p>
                                <p><strong>Ce code expirera dans 15 minutes.</strong></p>
                                <p>Si vous n'avez pas demandé de réinitialisation, vous pouvez ignorer cet email.</p>
                                <p>Cordialement,<br>L'équipe TDSI.ai</p>
                            </div>
                            <div class='footer'>
                                <p>© " . date('Y') . " TDSI.ai - Projet de Fin d'Année 2024-2025</p>
                                <p>Développé par Ibrahima Khalilou llah Sylla - Licence 2 TDSI</p>
                                <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
                    
                    $mail->Body = $htmlContent;
                    
                    // Contenu texte alternatif
                    $textContent = "Bonjour " . $user['prenom'] . ",\n\n";
                    $textContent .= "Nous avons reçu une demande de réinitialisation de votre mot de passe.\n\n";
                    $textContent .= "Votre code de vérification est : $code\n";
                    $textContent .= "Ce code expirera dans 15 minutes.\n\n";
                    $textContent .= "Si vous n'avez pas demandé de réinitialisation, ignorez cet email.\n\n";
                    $textContent .= "Cordialement,\nL'équipe TDSI.ai";
                    
                    $mail->AltBody = $textContent;
                    
                    // Envoyer l'email
                    if ($mail->send()) {
                        // Enregistrer dans la session
                        $_SESSION['reset_email'] = $email;
                        $_SESSION['reset_user_id'] = $user['id_utilisateur'];
                        $_SESSION['verification_sent'] = true;
                        $_SESSION['reset_message'] = "Un code de vérification a été envoyé à $email";
                        $_SESSION['message_type'] = 'success';
                        
                        $success = true;
                        
                        // En mode debug, afficher le code
                        if (DEBUG_MODE) {
                            $_SESSION['debug_code'] = $code;
                        }
                        
                    } else {
                        $error = "Erreur d'envoi de l'email : " . $mail->ErrorInfo;
                    }
                    
                } catch (Exception $e) {
                    $error = "Erreur lors de l'envoi de l'email : " . $e->getMessage();
                }
                
            } else {
                $error = "Aucun utilisateur trouvé avec cet email.";
            }
            
        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
        }
    }
    
    // Stocker les messages dans la session
    if ($success) {
        header('Location: ../verifier_code.php');
        exit();
    } else {
        $_SESSION['reset_errors'] = [$error];
        $_SESSION['old_reset_data'] = ['email' => $email];
        header('Location: ../mot_de_passe_oublie.php');
        exit();
    }
} else {
    header('Location: ../connexion.php');
    exit();
}
?>