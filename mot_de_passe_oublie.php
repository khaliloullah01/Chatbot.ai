<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - TDSI.ai</title>
    
    <!-- Utiliser les mêmes styles que connexion.php -->
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
        
        .page-mot-de-passe {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding-top: 80px;
        }
        
        .password-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .password-card {
            background: linear-gradient(59deg, #05174a, #0c38bb);
            border-radius: 30px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.69);
            color: white;
        }
        
        .password-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .password-header i {
            font-size: 3rem;
            color: var(--couleur-success);
            margin-bottom: 15px;
            display: block;
        }
        
        .password-header h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .password-header p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .input-with-icon {
            position: relative;
            margin-bottom: 25px;
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
        
        .btn-password {
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
        }
        
        .btn-password:hover {
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
        
        .password-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }
        
        .message-success {
            background: rgba(76, 175, 80, 0.2);
            color: #a5d6a7;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .message-error {
            background: rgba(244, 67, 54, 0.2);
            color: #ef9a9a;
            border: 1px solid rgba(244, 67, 54, 0.3);
        }
        
        .message-info {
            background: rgba(33, 150, 243, 0.2);
            color: #90caf9;
            border: 1px solid rgba(33, 150, 243, 0.3);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .password-container {
                padding: 20px;
            }
            
            .password-card {
                padding: 30px;
                border-radius: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .password-card {
                padding: 20px;
            }
            
            .password-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body class="page-mot-de-passe">
    <!-- Header (identique à connexion.php) -->
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

    <div class="password-container">
        <div class="password-card">
            <!-- En-tête -->
            <div class="password-header">
                <i class="fas fa-key"></i>
                <h2>Mot de passe oublié</h2>
                <p>Entrez votre adresse email pour recevoir un code de vérification</p>
            </div>

            <!-- Messages d'erreur/succès -->
            <?php if (isset($_SESSION['reset_errors'])): ?>
                <div class="password-message message-error">
                    <?php 
                    foreach ($_SESSION['reset_errors'] as $error) {
                        echo htmlspecialchars($error) . '<br>';
                    }
                    unset($_SESSION['reset_errors']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['reset_message'])): ?>
                <div class="password-message message-<?php echo $_SESSION['message_type'] ?? 'info'; ?>">
                    <?php 
                    echo htmlspecialchars($_SESSION['reset_message']);
                    unset($_SESSION['reset_message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Afficher le code de débogage en local -->
            <?php if (isset($_SESSION['debug_code']) && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1')): ?>
                <div class="password-message message-info">
                    <strong>Code de débogage (localhost seulement) :</strong><br>
                    <?php 
                    echo htmlspecialchars($_SESSION['debug_code']);
                    unset($_SESSION['debug_code']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'email -->
            <form method="POST" action="includes/send_reset_code.php">
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" 
                           placeholder="votre@email.com" required
                           value="<?php echo isset($_SESSION['old_reset_data']['email']) ? htmlspecialchars($_SESSION['old_reset_data']['email']) : ''; 
                                  unset($_SESSION['old_reset_data']); ?>">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn-password">
                        <span>Envoyer le code</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                    
                    <button type="button" class="btn-back" onclick="window.location.href='connexion.php'">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour à la connexion</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation du formulaire
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const email = this.querySelector('input[name="email"]').value;
                
                if (!email || !email.includes('@')) {
                    e.preventDefault();
                    alert('Veuillez entrer une adresse email valide.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>