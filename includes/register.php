<?php
// login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmation_mot_de_passe = $_POST['confirmation_mot_de_passe'];
    $niveau_etude = $_POST['niveau_etude'] ?? '';

    // Validation
    $erreurs = [];

    // Vérification des champs obligatoires
    if (empty($prenom)) {
        $erreurs[] = "Le prénom est obligatoire.";
    }
    if (empty($nom)) {
        $erreurs[] = "Le nom est obligatoire.";
    }
    if (empty($email)) {
        $erreurs[] = "L'email est obligatoire.";
    }
    if (empty($mot_de_passe)) {
        $erreurs[] = "Le mot de passe est obligatoire.";
    }
    if (empty($niveau_etude)) {
        $erreurs[] = "Le niveau d'étude est obligatoire.";
    }

    // Validation email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email n'est pas valide.";
    }

    // Vérification mot de passe
    if (!empty($mot_de_passe) && strlen($mot_de_passe) < 6) {
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    if (!empty($mot_de_passe) && $mot_de_passe !== $confirmation_mot_de_passe) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérifier si l'email existe déjà
    if (empty($erreurs) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $erreurs[] = "Cet email est déjà utilisé. Veuillez vous connecter plutôt.";
                $_SESSION['email_exists_error'] = true;
            }
        } catch (PDOException $e) {
            $erreurs[] = "Erreur de base de données : " . $e->getMessage();
        }
    }

    // Si pas d'erreurs, créer l'utilisateur
    if (empty($erreurs)) {
        try {
            // Hasher le mot de passe
            $hash_mot_de_passe = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            // Générer un username unique
            $username = strtolower($prenom . '.' . $nom);
            $base_username = $username;
            $counter = 1;
            
            // Vérifier si le username existe déjà
            $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE username = ?");
            $stmt->execute([$username]);
            
            while ($stmt->rowCount() > 0) {
                $username = $base_username . $counter;
                $stmt->execute([$username]);
                $counter++;
            }
            
            // Déterminer l'ID du niveau en fonction de la sélection
            $niveau_map = [
                'L1' => 1,
                'L2' => 2, 
                'L3' => 3,
                'M1' => 4,
                'M2' => 5,
                'Doctorat' => 6
            ];
            
            $id_niveau = $niveau_map[$niveau_etude] ?? 1;
            
            // Insérer l'utilisateur
            $stmt = $pdo->prepare("
                INSERT INTO utilisateur (prenom, nom, email, username, password_hash, role, id_niveau) 
                VALUES (?, ?, ?, ?, ?, 'etudiant', ?)
            ");
            
            $result = $stmt->execute([$prenom, $nom, $email, $username, $hash_mot_de_passe, $id_niveau]);
            
            if ($result) {
                // Succès
                $stmt = $pdo->prepare("
                    SELECT n.nom_level, n.code_level 
                    FROM niveau n 
                    WHERE n.id_niveau = ?
                ");
                $stmt->execute([$id_niveau]);
                $niveau_info = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Connecter automatiquement l'utilisateur
                $user_id = $pdo->lastInsertId();
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_niveau'] = $niveau_info['nom_level'] ?? $niveau_etude;
                $_SESSION['user_role'] = 'etudiant';
                
                $_SESSION['success_message'] = "Inscription réussie ! Bienvenue $prenom.";
                header('Location: ../chatbot.php');
                exit;
            } else {
                $erreurs[] = "Erreur lors de l'inscription. Veuillez réessayer.";
            }
            
        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }

    // Stocker les erreurs pour les afficher
    if (!empty($erreurs)) {
        $_SESSION['register_errors'] = $erreurs;
        $_SESSION['old_register_data'] = [
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'niveau_etude' => $niveau_etude
        ];
        
        if (in_array("Cet email est déjà utilisé. Veuillez vous connecter plutôt.", $erreurs)) {
            $_SESSION['email_exists_error'] = true;
        }
        
        header('Location: ../index.php#register');
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}
?>