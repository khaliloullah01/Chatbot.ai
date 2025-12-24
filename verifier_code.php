<?php
session_start();

// Vérifier que l'email est dans la session
if (!isset($_SESSION['reset_email'])) {
    header('Location: mot_de_passe_oublie.php');
    exit();
}

$email = $_SESSION['reset_email'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérifier le code - TDSI.ai</title>
    
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
        
        .page-verification {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 80px;
        }
        
        .verification-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .verification-card {
            background: linear-gradient(59deg, #05174a, #0c38bb);
            border-radius: 30px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.69);
            color: white;
        }
        
        .verification-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .verification-header i {
            font-size: 3rem;
            color: var(--couleur-success);
            margin-bottom: 15px;
            display: block;
        }
        
        .verification-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .verification-header p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .code-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
        }
        
        .code-inputs input {
            width: 55px;
            height: 65px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04);
            color: white;
            transition: all 0.3s ease;
        }
        
        .code-inputs input:focus {
            border-color: var(--couleur-success);
            background: rgba(255, 255, 255, 0.08);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        }
        
        .btn-verification {
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
        
        .btn-verification:hover {
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
        
        .verification-message {
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
        
        .resend-link {
            text-align: center;
            margin: 20px 0;
        }
        
        .resend-link a {
            color: var(--couleur-success);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        
        .resend-link a:hover {
            text-decoration: underline;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .verification-container {
                padding: 20px;
            }
            
            .verification-card {
                padding: 30px;
                border-radius: 20px;
            }
            
            .code-inputs input {
                width: 50px;
                height: 60px;
                font-size: 24px;
            }
        }
        
        @media (max-width: 480px) {
            .verification-card {
                padding: 20px;
            }
            
            .code-inputs {
                gap: 8px;
            }
            
            .code-inputs input {
                width: 45px;
                height: 55px;
                font-size: 20px;
            }
        }
    </style>
</head>

<body class="page-verification">
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

    <div class="verification-container">
        <div class="verification-card">
            <!-- En-tête -->
            <div class="verification-header">
                <i class="fas fa-shield-alt"></i>
                <h2>Vérification du code</h2>
                <p>Entrez le code à 6 chiffres envoyé à votre email</p>
                <p style="color: var(--couleur-success); font-weight: 600;"><?php echo htmlspecialchars($email); ?></p>
            </div>

            <!-- Messages d'erreur -->
            <?php if (isset($_SESSION['reset_errors'])): ?>
                <div class="verification-message message-error">
                    <?php 
                    foreach ($_SESSION['reset_errors'] as $error) {
                        echo htmlspecialchars($error) . '<br>';
                    }
                    unset($_SESSION['reset_errors']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire de vérification -->
            <form method="POST" action="includes/verify_reset_code.php">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                
                <div class="code-inputs">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="1" autocomplete="off">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="2" autocomplete="off">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="3" autocomplete="off">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="4" autocomplete="off">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="5" autocomplete="off">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" data-index="6" autocomplete="off">
                </div>
                <input type="hidden" id="verification_code" name="code">

                <div class="resend-link">
                    <a href="mot_de_passe_oublie.php">Renvoyer le code</a>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn-verification">
                        <span>Vérifier le code</span>
                        <i class="fas fa-check"></i>
                    </button>
                    
                    <button type="button" class="btn-back" onclick="window.location.href='mot_de_passe_oublie.php'">
                        <i class="fas fa-arrow-left"></i>
                        <span>Changer d'email</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du code de vérification (auto-avancement)
            const codeInputs = document.querySelectorAll('.code-inputs input');
            const hiddenCodeInput = document.getElementById('verification_code');
            
            codeInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    // Limiter à un chiffre
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);
                    
                    // Mettre à jour le champ caché
                    updateHiddenCode();
                    
                    // Passer au champ suivant
                    if (this.value && index < codeInputs.length - 1) {
                        codeInputs[index + 1].focus();
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        codeInputs[index - 1].focus();
                    }
                });
                
                // Coller le code complet
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const paste = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
                    if (paste.length === 6) {
                        paste.split('').forEach((char, i) => {
                            if (codeInputs[i]) {
                                codeInputs[i].value = char;
                            }
                        });
                        updateHiddenCode();
                        codeInputs[5].focus();
                    }
                });
            });

            function updateHiddenCode() {
                let code = '';
                codeInputs.forEach(input => {
                    code += input.value || '';
                });
                hiddenCodeInput.value = code;
            }

            // Validation du formulaire
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const code = hiddenCodeInput.value;
                
                if (code.length !== 6) {
                    e.preventDefault();
                    alert('Veuillez entrer un code à 6 chiffres.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>