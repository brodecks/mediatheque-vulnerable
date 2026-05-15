<?php
// =============================================================
//  FICHIER : config/database.php
//  Rôle    : Connexion à la base de données
// =============================================================

// ⚠️ [VULN-1] Exposition de données sensibles
// Les identifiants de connexion sont en clair dans le code source
// et ce fichier est accessible publiquement (pas de protection .htaccess)
define('DB_HOST', 'localhost');
define('DB_NAME', 'mediatheque');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');

// ⚠️ [VULN-2] Exposition de données sensibles
// La clé secrète utilisée pour les sessions est triviale et hardcodée
define('SECRET_KEY', '1234');
define('ADMIN_DEFAULT_PASS', 'admin');   // mot de passe par défaut jamais changé

function getDB()
{
    // ⚠️ [VULN-3] Exposition de données sensibles
    // Les erreurs PDO sont affichées directement à l'utilisateur (mode ERRMODE_EXCEPTION sans try/catch global)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
