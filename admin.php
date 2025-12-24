<?php
require_once 'includes/check_auth.php';
require_once 'includes/fonctions_bibliotheque.php';
require_once 'includes/config.php'; // AJOUT IMPORTANT !

redirigerSiNonConnecte();
$utilisateur = obtenirUtilisateurConnecte();

// Vérifier si l'utilisateur est admin
if ($utilisateur['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Traitement du formulaire d'ajout de matière
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_matiere'])) {
        $nom_matiere = trim($_POST['nom_matiere']);
        $description = trim($_POST['description']);
        $niveau_id = intval($_POST['niveau_id']);
        
        if (!empty($nom_matiere) && !empty($niveau_id)) {
            // Insérer la nouvelle matière
            $sql = "INSERT INTO matiere (nom_matiere, description, niveau_id) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$nom_matiere, $description, $niveau_id])) {
                $success = "Matière ajoutée avec succès!";
            } else {
                $error = "Erreur lors de l'ajout de la matière.";
            }
        }
    }
    
    // Ajouter un chapitre
    if (isset($_POST['ajouter_chapitre'])) {
        $id_matiere = intval($_POST['id_matiere']);
        $titre_chapitre = trim($_POST['titre_chapitre']);
        $ordre_chapitre = intval($_POST['ordre_chapitre']);
        $contenu = trim($_POST['contenu']);
        
        if (!empty($id_matiere) && !empty($titre_chapitre)) {
            $sql = "INSERT INTO chapitre (id_matiere, titre_chapitre, ordre_chapitre, contenu) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id_matiere, $titre_chapitre, $ordre_chapitre, $contenu])) {
                $success = "Chapitre ajouté avec succès!";
            } else {
                $error = "Erreur lors de l'ajout du chapitre.";
            }
        }
    }
    
    // Ajouter une ressource
    if (isset($_POST['ajouter_ressource'])) {
        $id_chapitre = intval($_POST['id_chapitre']);
        $type_ressource = trim($_POST['type_ressource']);
        $chemin_ressource = trim($_POST['chemin_ressource']);
        $titre_ressource = trim($_POST['titre_ressource']);
        
        if (!empty($id_chapitre) && !empty($titre_ressource)) {
            $sql = "INSERT INTO ressource (id_chapitre, type_ressource, chemin_ressource, titre_ressource, date_ajout) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$id_chapitre, $type_ressource, $chemin_ressource, $titre_ressource])) {
                $success = "Ressource ajoutée avec succès!";
            } else {
                $error = "Erreur lors de l'ajout de la ressource.";
            }
        }
    }
}

// Récupérer les données pour les listes déroulantes
$niveaux = $pdo->query("SELECT * FROM niveau ORDER BY id_niveau")->fetchAll();// Dans admin.php, modifiez les requêtes SQL :
$matieres = $pdo->query("SELECT m.*, n.nom_level, u.nom as nom_user, u.prenom as prenom_user 
                         FROM matiere m 
                         LEFT JOIN niveau n ON m.niveau_id = n.id_niveau 
                         LEFT JOIN utilisateur u ON m.id_utilisateur = u.id_utilisateur 
                         ORDER BY m.est_publique DESC, n.id_niveau, m.nom_matiere")->fetchAll();

$chapitres = $pdo->query("SELECT c.*, m.nom_matiere, u.nom as nom_user, u.prenom as prenom_user 
                          FROM chapitre c 
                          LEFT JOIN matiere m ON c.id_matiere = m.id_matiere 
                          LEFT JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur 
                          ORDER BY m.nom_matiere, c.ordre_chapitre")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - tdsi.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <header class="header-area header-sticky header-bleu">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <a href="chatbot.php" class="logo">
                            <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
                        </a>
                        <ul class="nav">
                            <li class="scroll-to-section"><a href="index_connecte.php"><i class="fa fa-home"></i> Accueil</a></li>
                            <li class="scroll-to-section"><a href="chatbot.php"><i class="fas fa-comment"></i> Chatbot</a></li>
                            <li class="scroll-to-section"><a href="Bibliotheque.php"><i class="fas fa-book-open"></i> Bibliothèque</a></li>
                            <li class="scroll-to-section"><a href="admin.php" class="active"><i class="fas fa-cog"></i> Administration</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="contenu-admin" style="margin-top: 100px; padding: 20px;">
        <div class="container">
            <h1><i class="fas fa-cog"></i> Panel d'Administration</h1>
            <p class="text-muted">Gestion des matières, chapitres et ressources</p>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- Formulaire Ajout Matière -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5><i class="fas fa-book"></i> Ajouter une Matière</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nom de la matière</label>
                                    <input type="text" name="nom_matiere" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Niveau</label>
                                    <select name="niveau_id" class="form-control" required>
                                        <option value="">Sélectionner un niveau</option>
                                        <?php foreach ($niveaux as $niveau): ?>
                                            <option value="<?php echo $niveau['id_niveau']; ?>">
                                                <?php echo $niveau['nom_level']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" name="ajouter_matiere" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Ajouter la matière
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Formulaire Ajout Chapitre -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5><i class="fas fa-file-alt"></i> Ajouter un Chapitre</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Matière</label>
                                    <select name="id_matiere" class="form-control" required>
                                        <option value="">Sélectionner une matière</option>
                                        <?php foreach ($matieres as $matiere): ?>
                                            <option value="<?php echo $matiere['id_matiere']; ?>">
                                                <?php echo $matiere['nom_matiere']; ?> (<?php echo $matiere['nom_level']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Titre du chapitre</label>
                                    <input type="text" name="titre_chapitre" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ordre</label>
                                    <input type="number" name="ordre_chapitre" class="form-control" value="1" min="1">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contenu</label>
                                    <textarea name="contenu" class="form-control" rows="3" placeholder="Description du chapitre..."></textarea>
                                </div>
                                <button type="submit" name="ajouter_chapitre" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Ajouter le chapitre
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Formulaire Ajout Ressource -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5><i class="fas fa-file-pdf"></i> Ajouter une Ressource</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Chapitre</label>
                                    <select name="id_chapitre" class="form-control" required>
                                        <option value="">Sélectionner un chapitre</option>
                                        <?php foreach ($chapitres as $chapitre): ?>
                                            <option value="<?php echo $chapitre['id_chapitre']; ?>">
                                                <?php echo $chapitre['nom_matiere']; ?> - <?php echo $chapitre['titre_chapitre']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Type de ressource</label>
                                    <select name="type_ressource" class="form-control" required>
                                        <option value="pdf">PDF</option>
                                        <option value="video">Vidéo</option>
                                        <option value="exercice">Exercice</option>
                                        <option value="code">Code</option>
                                        <option value="sql">SQL</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Chemin de la ressource</label>
                                    <input type="text" name="chemin_ressource" class="form-control" placeholder="cours/matiere/fichier.pdf">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Titre de la ressource</label>
                                    <input type="text" name="titre_ressource" class="form-control" required>
                                </div>
                                <button type="submit" name="ajouter_ressource" class="btn btn-info">
                                    <i class="fas fa-plus"></i> Ajouter la ressource
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5><i class="fas fa-chart-bar"></i> Statistiques</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h3><?php echo count($matieres); ?></h3>
                                    <p class="text-muted">Matières</p>
                                </div>
                                <div class="col-md-3">
                                    <h3><?php echo count($chapitres); ?></h3>
                                    <p class="text-muted">Chapitres</p>
                                </div>
                                <div class="col-md-3">
                                    <?php
                                    $total_ressources = $pdo->query("SELECT COUNT(*) as total FROM ressource")->fetch()['total'];
                                    ?>
                                    <h3><?php echo $total_ressources; ?></h3>
                                    <p class="text-muted">Ressources</p>
                                </div>
                                <div class="col-md-3">
                                    <?php
                                    $total_utilisateurs = $pdo->query("SELECT COUNT(*) as total FROM utilisateur")->fetch()['total'];
                                    ?>
                                    <h3><?php echo $total_utilisateurs; ?></h3>
                                    <p class="text-muted">Utilisateurs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>