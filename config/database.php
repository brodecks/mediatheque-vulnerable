<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
// =============================================================
//  FICHIER : config/database.php
//  Rôle    : Connexion à la base de données
// =============================================================

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// ⚠️ [VULN-1] Exposition de données sensibles
// Les identifiants de connexion sont en clair dans le code source
// et ce fichier est accessible publiquement (pas de protection .htaccess)
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_USER_PWD']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);

function getDB()
{
    // ⚠️ [VULN-3] Exposition de données sensibles
    // Les erreurs PDO sont affichées directement à l'utilisateur (mode ERRMODE_EXCEPTION sans try/catch global)
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        error_log($e->getMessage() . PHP_EOL, 3, __DIR__ . '/../logs/error.log');
        http_response_code(500);
        die("Une erreur est survenue, veuillez réessayer plus tard.");
    }
}
