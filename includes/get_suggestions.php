<?php
require_once 'check_auth.php';
require_once 'questions_suggestions.php';

redirigerSiNonConnecte();
$utilisateur = obtenirUtilisateurConnecte();

header('Content-Type: application/json');

$niveau = $utilisateur['nom_level'] ?? ($utilisateur['code_level'] ?? 'debutant');
$suggestions = getQuestionsSuggestions($niveau);

echo json_encode($suggestions);
?>