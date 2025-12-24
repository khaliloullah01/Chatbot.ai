<?php
// includes/send_email.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Si vous utilisez Composer

function sendPasswordResetEmail($to, $name, $code) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuration du serveur
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Ou votre serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'votre-email@gmail.com';
        $mail->Password = 'votre-mot-de-passe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Destinataires
        $mail->setFrom('no-reply@tdsi.ai', 'TDSI.ai');
        $mail->addAddress($to, $name);
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Code de vérification - Réinitialisation de mot de passe';
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #133ebe, #0d2e8a); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
                .code { background: #fff; border: 2px dashed #133ebe; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; color: #133ebe; margin: 20px 0; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>TDSI.ai</h2>
                </div>
                <div class='content'>
                    <h3>Bonjour {$name},</h3>
                    <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
                    <p>Utilisez le code ci-dessous pour vérifier votre identité :</p>
                    <div class='code'>{$code}</div>
                    <p>Ce code expirera dans 15 minutes.</p>
                    <p>Si vous n'avez pas fait cette demande, ignorez simplement cet email.</p>
                    <p>Cordialement,<br>L'équipe TDSI.ai</p>
                </div>
                <div class='footer'>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->Body = $htmlContent;
        $mail->AltBody = "Bonjour {$name},\n\nCode de vérification : {$code}\n\nCe code expirera dans 15 minutes.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur envoi email: " . $mail->ErrorInfo);
        return false;
    }
}
?>