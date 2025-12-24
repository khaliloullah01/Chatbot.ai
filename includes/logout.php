<?php
// login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire les cookies de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire les cookies "se souvenir de moi"
setcookie('remember_token', '', time() - 3600, '/');
setcookie('user_id', '', time() - 3600, '/');

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header('Location: ../index.php');
exit;
?>