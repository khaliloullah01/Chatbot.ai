<?php
require_once 'includes/check_auth.php';
require_once 'includes/fonctions_historique.php';

redirigerSiNonConnecte();
$utilisateur = obtenirUtilisateurConnecte();
$user_id = obtenirUtilisateurId();

// Récupérer les conversations
$conversations = getConversationsUtilisateur($user_id);

// Gérer les actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'supprimer':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                supprimerConversation($_GET['id'], $user_id);
                header('Location: historique.php?supprime=1');
                exit;
            }
            break;
        case 'charger':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                header('Location: chatbot.php?charger_conversation=' . $_GET['id']);
                exit;
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>tdsi.ai - Historique des conversations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>
    <!-- Header -->
    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky header-bleu">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="chatbot.php" class="logo">
                            <img src="assets/images/tdsi-ai-logo.png" alt="tdsi.ai Logo">
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li class="scroll-to-section"><a href="index_connecte.php"><i class="fa fa-home"></i> Acceuil</a>
                            </li>
                            <li class="scroll-to-section"><a href="chatbot.php" ><i
                                        class="fas fa-comment"></i>
                                    Chatbot</a></li>
                            <li class="scroll-to-section"><a href="Bibliotheque.php"><i class="fas fa-book-open"></i>
                                    Bibliothèque</a>
                            </li>
                            <li class="scroll-to-section"><a href="mes_cours.php"><i class="fas fa-star"></i> Mes
                                    Cours</a></li>
                            <li class="scroll-to-section"><a href="historique.php" class="active"><i class="fas fa-history"></i>
                                    Historique</a></li>


                            <!-- ***** LIEN ADMIN - AJOUTEZ ICI ***** -->
                            <?php if (estAdministrateur()): ?>
                                <li class="scroll-to-section"><a href="admin.php"><i class="fas fa-cog"></i>
                                        Administration</a></li>
                            <?php endif; ?>
                            <!-- ***** FIN DU LIEN ADMIN ***** -->

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle user-menu">
                                    <div class="user-avatar">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($utilisateur['prenom'] . '+' . $utilisateur['nom']); ?>&background=ffffff&color=133ebe&size=32"
                                            alt="avatar">
                                    </div>
                                    <span
                                        class="user-name"><?php echo htmlspecialchars($utilisateur['prenom'] ?? ''); ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-header">
                                        <div class="user-info">
                                            <strong><?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?></strong>
                                            <span><?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></span>
                                            <small>Niveau :
                                                <?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'Non défini')); ?></small>
                                        </div>
                                    </li>
                                    <li class="divider"></li>
                                    <li><a href="#" onclick="commencerNouvelleConversation()"><i
                                                class="fas fa-plus"></i> Nouveau chat</a>
                                    </li>
                                    <li><a href="#" onclick="ouvrirParametres()"><i class="fas fa-cog"></i>
                                            Paramètres</a></li>
                                    <li class="divider"></li>
                                    <li><a href="includes/logout.php" class="logout-btn"><i
                                                class="fas fa-sign-out-alt"></i>
                                            Déconnexion</a></li>
                                </ul>
                            </li>
                        </ul>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <div class="contenu-historique">
        <div class="container-historique">
            <!-- En-tête -->
            <div class="en-tete-historique">
                <div class="titre-historique">
                    <i class="fas fa-history"></i>
                    <h1>Historique des conversations</h1>
                </div>
                <a href="#" onclick="commencerNouvelleConversation()" class="btn-nouvelle-conversation">
                    <i class="fas fa-plus"></i>
                    Nouvelle conversation
                </a>
            </div>

            <!-- Alertes -->
            <?php if (isset($_GET['supprime'])): ?>
                <div class="alerte-succes">
                    <i class="fas fa-check-circle"></i>
                    Conversation supprimée avec succès.
                </div>
            <?php endif; ?>

            <!-- Liste des conversations -->
            <div class="conteneur-conversations">
                <?php if (empty($conversations)): ?>
                    <div class="etat-vide">
                        <div class="icone-vide">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Aucune conversation</h3>
                        <p>Commencez une nouvelle conversation pour la voir apparaître ici.</p>
                        <a href="chatbot.php" class="btn-principal">
                            <i class="fas fa-plus"></i>
                            Commencer une conversation
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grille-conversations">
                        <?php foreach ($conversations as $conv): ?>
                            <div class="carte-conversation">
                                <div class="en-tete-carte">
                                    <div class="info-conversation">
                                        <h3 class="titre-conversation"><?php echo htmlspecialchars($conv['sujet']); ?></h3>
                                        <span class="date-conversation"><?php echo $conv['date_formatee']; ?></span>
                                    </div>
                                    <div class="statut-conversation">
                                        <span class="badge-messages">
                                            <i class="fas fa-message"></i>
                                            <?php echo $conv['nb_messages']; ?> message(s)
                                        </span>
                                    </div>
                                </div>

                                <div class="contenu-carte">
                                    <p class="apercu-message">
                                        <?php
                                        if ($conv['dernier_message']) {
                                            echo htmlspecialchars(substr($conv['dernier_message'], 0, 150)) .
                                                (strlen($conv['dernier_message']) > 150 ? '...' : '');
                                        } else {
                                            echo 'Aucun message dans cette conversation';
                                        }
                                        ?>
                                    </p>
                                </div>

                                <div class="actions-carte">
                                    <a href="historique.php?action=charger&id=<?php echo $conv['id_conversation']; ?>"
                                        class="btn-action btn-reprendre">
                                        <i class="fas fa-play"></i>
                                        Reprendre
                                    </a>
                                    <button onclick="confirmerSuppression(<?php echo $conv['id_conversation']; ?>)"
                                        class="btn-action btn-supprimer">
                                        <i class="fas fa-trash"></i>
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation -->
    <div id="modal-suppression" class="modal-custom">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h5><i class="fas fa-exclamation-triangle"></i> Confirmation</h5>
                <span class="close-modal" onclick="fermerModalSuppression()">&times;</span>
            </div>
            <div class="modal-body-custom">
                <p>Êtes-vous sûr de vouloir supprimer cette conversation ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer-custom">
                <button class="btn-modal btn-cancel" onclick="fermerModalSuppression()">Annuler</button>
                <button id="btn-confirm-suppression" class="btn-modal btn-confirm">Supprimer</button>
            </div>
        </div>
    </div>

    <!-- Modal Paramètres -->
    <div id="modal-parametres" class="modal-custom">
        <div class="modal-content-custom modal-large">
            <div class="modal-header-custom">
                <h5><i class="fas fa-cogs"></i> Paramètres</h5>
                <span class="close-modal" onclick="fermerParametres()">&times;</span>
            </div>
            <div class="modal-body-custom">
                <div class="parametre-groupe">
                    <h6>Informations du compte</h6>
                    <div class="info-compte">
                        <p><strong>Nom:</strong>
                            <?php echo htmlspecialchars(($utilisateur['prenom'] ?? '') . ' ' . ($utilisateur['nom'] ?? '')); ?>
                        </p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($utilisateur['email'] ?? ''); ?></p>
                        <p><strong>Niveau:</strong>
                            <?php echo htmlspecialchars($utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'Non défini')); ?>
                        </p>
                        <p><strong>Rôle:</strong> <?php echo htmlspecialchars($utilisateur['role'] ?? ''); ?></p>
                    </div>
                </div>

                <div class="parametre-groupe">
                    <h6>Préférences de l'interface</h6>
                    <div class="parametre-option">
                        <label class="switch">
                            <input type="checkbox" id="auto-scroll" checked>
                            <span class="slider round"></span>
                        </label>
                        <div class="parametre-info">
                            <strong>Défilement automatique</strong>
                            <span>Descendre automatiquement vers les nouveaux messages</span>
                        </div>
                    </div>

                    <div class="parametre-option">
                        <label class="switch">
                            <input type="checkbox" id="sons" checked>
                            <span class="slider round"></span>
                        </label>
                        <div class="parametre-info">
                            <strong>Sons de notification</strong>
                            <span>Jouer un son à la fin de chaque réponse</span>
                        </div>
                    </div>
                </div>

                <div class="parametre-groupe">
                    <h6>Personnalisation</h6>
                    <div class="parametre-option">
                        <label for="vitesse-frappe" class="parametre-label">
                            <strong>Vitesse d'affichage</strong>
                            <span>Contrôle la rapidité de l'affichage progressif</span>
                        </label>
                        <select id="vitesse-frappe" class="parametre-select">
                            <option value="rapide">Rapide</option>
                            <option value="normal" selected>Normal</option>
                            <option value="lent">Lent</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button class="btn-modal btn-secondary" onclick="reinitialiserParametres()">Réinitialiser</button>
                <button class="btn-modal btn-confirm" onclick="fermerParametres()">Fermer</button>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright © tdsi.ai - Projet de Fin d'Année 2024-2025 <br> Développé par Ibrahima Khalilou llah
                        Sylla - Licence 2 TDSI </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let conversationASupprimer = null;

        function commencerNouvelleConversation() {
            window.location.href = 'chatbot.php?nouvelle_conversation=1';
        }

        function fermerParametres() {
            document.getElementById('modal-parametres').style.display = 'none';
        }

        function ouvrirParametres() {
            document.getElementById('modal-parametres').style.display = 'flex';
        }

        function confirmerSuppression(idConversation) {
            conversationASupprimer = idConversation;
            document.getElementById('modal-suppression').style.display = 'flex';
        }

        function fermerModalSuppression() {
            document.getElementById('modal-suppression').style.display = 'none';
            conversationASupprimer = null;
        }

        document.getElementById('btn-confirm-suppression').addEventListener('click', function () {
            if (conversationASupprimer) {
                window.location.href = 'historique.php?action=supprimer&id=' + conversationASupprimer;
            }
        });

        // Fermer la modal en cliquant à l'extérieur
        document.getElementById('modal-suppression').addEventListener('click', function (e) {
            if (e.target === this) {
                fermerModalSuppression();
            }
        });
    </script>
</body>

</html>