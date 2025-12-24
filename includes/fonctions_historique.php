<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

/**
 * Crée une nouvelle conversation pour un utilisateur
 */
function creerConversation($id_utilisateur, $premier_message = null) {
    global $pdo;

    try {
        // Générer un titre automatique basé sur la date
        $titre = "Conversation du " . date('d/m/Y à H:i');

        if ($premier_message) {
            // Extraire les premiers mots pour le titre
            $mots = explode(' ', strip_tags($premier_message));
            $extrait = implode(' ', array_slice($mots, 0, 5));
            $titre = $extrait . (count($mots) > 5 ? '...' : '');
            
            // Limiter la longueur du titre
            if (strlen($titre) > 200) {
                $titre = substr($titre, 0, 197) . '...';
            }
        }

        $sql = "INSERT INTO conversation (sujet, id_utilisateur, date_debut, statut) 
                VALUES (?, ?, NOW(), 'active')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titre, $id_utilisateur]);

        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erreur création conversation: " . $e->getMessage());
        return false;
    }
}

/**
 * Sauvegarde un message dans la base de données
 */
function sauvegarderMessage($id_conversation, $contenu, $type, $auteur_id) {
    global $pdo;

    try {
        // Nettoyer le contenu
        $contenu = trim($contenu);
        if (empty($contenu)) {
            return false;
        }

        $sql = "INSERT INTO message (id_conversation, message_contenu, message_type, auteur_id, message_date) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_conversation, $contenu, $type, $auteur_id]);

        $id_message = $pdo->lastInsertId();

        // Mettre à jour le dernier message de la conversation
        $sql = "UPDATE conversation SET dernier_message_id = ? WHERE id_conversation = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_message, $id_conversation]);

        return $id_message;
    } catch (PDOException $e) {
        error_log("Erreur sauvegarde message: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère les conversations d'un utilisateur
 */
function getConversationsUtilisateur($id_utilisateur) {
    global $pdo;

    $sql = "SELECT c.*, 
                   m.message_contenu as dernier_message,
                   DATE_FORMAT(c.date_debut, '%d/%m/%Y %H:%i') as date_formatee,
                   (SELECT COUNT(*) FROM message WHERE id_conversation = c.id_conversation) as nb_messages
            FROM conversation c
            LEFT JOIN message m ON c.dernier_message_id = m.id_message
            WHERE c.id_utilisateur = ?
            ORDER BY c.date_debut DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_utilisateur]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère les messages d'une conversation
 */
function getMessagesConversation($id_conversation) {
    global $pdo;

    $sql = "SELECT m.*, 
                   u.prenom,
                   u.nom,
                   DATE_FORMAT(m.message_date, '%d/%m/%Y à %H:%i') as date_formatee
            FROM message m
            LEFT JOIN utilisateur u ON m.auteur_id = u.id_utilisateur
            WHERE m.id_conversation = ?
            ORDER BY m.message_date ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_conversation]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Supprime une conversation
 */
function supprimerConversation($id_conversation, $id_utilisateur) {
    global $pdo;

    try {
        // Vérifier que l'utilisateur possède la conversation
        $sql = "SELECT id_conversation FROM conversation WHERE id_conversation = ? AND id_utilisateur = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_conversation, $id_utilisateur]);

        if ($stmt->fetch()) {
            // Supprimer d'abord les messages
            $sql = "DELETE FROM message WHERE id_conversation = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_conversation]);

            // Puis la conversation
            $sql = "DELETE FROM conversation WHERE id_conversation = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_conversation]);

            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Erreur suppression conversation: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si une conversation a des messages
 */
function conversationAVecMessages($id_conversation) {
    global $pdo;

    $sql = "SELECT COUNT(*) as nb_messages FROM message WHERE id_conversation = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_conversation]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['nb_messages'] > 0;
}

/**
 * Récupère une conversation spécifique avec vérification de propriété
 */
function getConversation($id_conversation, $id_utilisateur) {
    global $pdo;

    $sql = "SELECT c.* FROM conversation c 
            WHERE c.id_conversation = ? AND c.id_utilisateur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_conversation, $id_utilisateur]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>