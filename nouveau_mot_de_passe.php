<?php
session_start();

// Vérifier que le token est dans la session
if (!isset($_SESSION['reset_token']) || !isset($_SESSION['reset_email']) || !isset($_SESSION['reset_verified'])) {
    header('Location: mot_de_passe_oublie.php');
    exit();
}

$email = $_SESSION['reset_email'];
$token = $_SESSION['reset_token'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - TDSI.ai</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/main.css">
    
    <style>
        :root {
            --couleur-principale1: #133ebe;
            --couleur-secondaire1: #2e59d9;
            --couleur-success: #ffc527;
        }
        
        .page-nouveau-mdp {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 80px;
        }
        
        .nouveau-mdp-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .nouveau-mdp-card {
            background: linear-gradient(59deg, #05174a, #0c38bb);
            border-radius: 30px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.69);
            color: white;
        }
        
        .nouveau-mdp-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .nouveau-mdp-header i {
            font-size: 3rem;
            color: var(--couleur-success);
            margin-bottom: 15px;
            display: block;
        }
        
        .nouveau-mdp-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .nouveau-mdp-header p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .input-with-icon {
            position: relative;
            margin-bottom: 20px;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--couleur-success);
            font-size: 18px;
        }
        
        .input-with-icon input {
            width: 100%;
            height: 52px;
            padding: 0 15px 0 45px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.04);
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .input-with-icon input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .input-with-icon input:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: var(--couleur-success);
            box-shadow: 0 6px 18px rgba(246, 194, 62, 0.08);
            outline: none;
        }
        
        .password-strength {
            margin-top: 5px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .strength-weak {
            background: #ff4757;
        }
        
        .strength-medium {
            background: #ffa502;
        }
        
        .strength-strong {
            background: #2ed573;
        }
        
        .password-rules {
            margin-top: 10px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .password-rules ul {
            padding-left: 20px;
            margin: 5px 0;
        }
        
        .password-rules li.valid {
            color: var(--couleur-success);
        }
        
        .password-rules li.valid:before {
            content: '✓ ';
        }
        
        .btn-nouveau-mdp {
            width: 100%;
            height: 52px;
            border-radius: 8px;
            background: linear-gradient(135deg, #fac021ff, var(--couleur-success));
            border: none;
            color: white;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn-nouveau-mdp:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 159, 28, 0.3);
        }
        
        .btn-back {
            width: 100%;
            height: 52px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .nouveau-mdp-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }
        
        .message-error {
            background: rgba(244, 67, 54, 0.2);
            color: #ef9a9a;
            border: 1px solid rgba(244, 67, 54, 0.3);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .nouveau-mdp-container {
                padding: 20px;
            }
            
            .nouveau-mdp-card {
                padding: 30px;
                border-radius: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .nouveau-mdp-card {
                padding: 20px;
            }
            
            .nouveau-mdp-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body class="page-nouveau-mdp">
    <!-- Header -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <a href="index.php" class="logo">
                            <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
                        </a>
                        <ul class="nav">
                            <li class="scroll-to-section"><a href="index.php#top">Accueil</a></li>
                            <li class="scroll-to-section"><a href="index.php#services">Services</a></li>
                            <li class="scroll-to-section"><a href="index.php#about">À Propos</a></li>
                            <li class="scroll-to-section"><a href="index.php#testimonials">Créateur</a></li>
                            <li class="scroll-to-section"><a href="index.php#register">Inscription</a></li>
                            <li class="scroll-to-section1"><a href="connexion.php">Connexion</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="nouveau-mdp-container">
        <div class="nouveau-mdp-card">
            <!-- En-tête -->
            <div class="nouveau-mdp-header">
                <i class="fas fa-lock"></i>
                <h2>Nouveau mot de passe</h2>
                <p>Créez un nouveau mot de passe sécurisé pour votre compte</p>
            </div>

            <!-- Messages d'erreur -->
            <?php if (isset($_SESSION['reset_errors'])): ?>
                <div class="nouveau-mdp-message message-error">
                    <?php 
                    foreach ($_SESSION['reset_errors'] as $error) {
                        echo htmlspecialchars($error) . '<br>';
                    }
                    unset($_SESSION['reset_errors']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form method="POST" action="includes/reset_password.php">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" id="new_password" name="new_password" 
                           placeholder="Nouveau mot de passe" required>
                </div>
                <div class="password-strength">
                    <div class="strength-bar" id="password-strength-bar"></div>
                </div>
                <div class="password-rules">
                    <ul id="password-rules">
                        <li>Au moins 8 caractères</li>
                        <li>Une lettre majuscule</li>
                        <li>Une lettre minuscule</li>
                        <li>Un chiffre</li>
                    </ul>
                </div>

                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           placeholder="Confirmer le mot de passe" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn-nouveau-mdp">
                        <span>Réinitialiser le mot de passe</span>
                        <i class="fas fa-check"></i>
                    </button>
                    
                    <button type="button" class="btn-back" onclick="window.location.href='verifier_code.php'">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation du mot de passe
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthBar = document.getElementById('password-strength-bar');
            const passwordRules = document.getElementById('password-rules').querySelectorAll('li');

            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                
                // Règles de validation
                const rules = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password)
                };

                // Calcul de la force
                let strength = 0;
                if (rules.length) strength += 25;
                if (rules.uppercase) strength += 25;
                if (rules.lowercase) strength += 25;
                if (rules.number) strength += 25;

                // Mettre à jour la barre
                strengthBar.style.width = strength + '%';
                strengthBar.className = 'strength-bar';
                
                if (strength < 50) {
                    strengthBar.classList.add('strength-weak');
                } else if (strength < 75) {
                    strengthBar.classList.add('strength-medium');
                } else {
                    strengthBar.classList.add('strength-strong');
                }

                // Mettre à jour les règles
                passwordRules[0].className = rules.length ? 'valid' : '';
                passwordRules[1].className = rules.uppercase ? 'valid' : '';
                passwordRules[2].className = rules.lowercase ? 'valid' : '';
                passwordRules[3].className = rules.number ? 'valid' : '';
            });

            // Validation de confirmation du mot de passe
            function validatePasswords() {
                const password = newPasswordInput.value;
                const confirm = confirmPasswordInput.value;
                
                if (password && confirm && password !== confirm) {
                    confirmPasswordInput.style.borderColor = '#ff4757';
                    return false;
                } else {
                    confirmPasswordInput.style.borderColor = password ? '#2ed573' : 'rgba(255, 255, 255, 0.08)';
                    return true;
                }
            }

            newPasswordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);

            // Validation du formulaire
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const password = newPasswordInput.value;
                const confirm = confirmPasswordInput.value;
                
                if (!validatePasswords()) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas.');
                    return;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 8 caractères.');
                    return;
                }
                
                const rules = {
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password)
                };
                
                if (!rules.uppercase || !rules.lowercase || !rules.number) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.');
                    return;
                }
            });
        });
    </script>
</body>
</html>