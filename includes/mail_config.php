<?php
// includes/mail_config.php

// Configuration SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'is6010625@gmail.com'); // Remplacez par votre email
define('SMTP_PASSWORD', 'hksb yrob xyay dxio'); // Mot de passe d'application Gmail
define('SMTP_FROM_EMAIL', 'no-reply@tdsi.ai');
define('SMTP_FROM_NAME', 'TDSI.ai');
define('SMTP_SECURE', 'tls');

// Test en local
define('DEBUG_MODE', ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1'));
?>