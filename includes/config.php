<?php
// includes/config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Générer un token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'tdsi-chatbot');
define('DB_USER', 'root');
define('DB_PASS', '');


// Configuration application
define('APP_NAME', 'TDSI ChatBot Assistant');
define('APP_URL', 'http://localhost/tdsi-chatbot');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
?>