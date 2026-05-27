<?php
// =============================================================
//  FICHIER : includes/session.php
//  Rôle    : Gestion des sessions et de l'authentification
// =============================================================
require_once __DIR__ . '/../models/AuthModel.php';
// ⚠️ [VULN-4] Faille d'authentification — durée de session illimitée
// Aucun timeout de session n'est défini : une session reste valide indéfiniment
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
    }
    $_SESSION['last_activity'] = time();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
/**
 * Vérifie si l'utilisateur est connecté.
 * Redirige vers la page de login sinon.
 */
function requireLogin()
{
    // ⚠️ [VULN-5] Faille d'authentification — contrôle insuffisant
    // On vérifie uniquement la présence de la clé, pas sa valeur ni son intégrité
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        header('Location: index.php?action=login');
        exit();
        // ⚠️ [VULN-6] Faille d'authentification — absence de exit() après header()
        // Sans exit(), l'exécution du script continue même après la redirection
        // Un attaquant peut ignorer les headers HTTP et lire la réponse complète
    }
}

/**
 * Vérifie si l'utilisateur est administrateur.
 */
function requireAdmin()
{
    requireLogin();
    // ⚠️ [VULN-7] Faille d'authentification — rôle stocké côté client (session non signée)
    // Le rôle 'admin' est simplement lu depuis la session sans vérification serveur
    // Si la session est volée ou forgée, l'attaquant obtient les droits admin\
    $user = AuthModel::getUserById($_SESSION['user_id']);
    if ($user['role'] !== 'admin') {
        die("Accès refusé.");
    }
}

/**
 * Retourne le nom de l'utilisateur connecté.
 */
function getCurrentUser()
{
    return $_SESSION['user'] ?? 'Invité';
}
