<?php
// Configuration de l'application
define('APP_NAME', 'FASO IMMO');
define('APP_URL', 'http://localhost/immobilier');
define('APP_ENV', 'development');

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'immobilier_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuration des uploads
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configuration de session
define('SESSION_LIFETIME', 3600);
define('BCRYPT_COST', 12);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}