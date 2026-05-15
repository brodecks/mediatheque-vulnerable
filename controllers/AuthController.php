<?php
// =============================================================
//  FICHIER : controllers/AuthController.php
//  Rôle    : Gestion du login / logout
// =============================================================

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../includes/session.php';

class AuthController
{

    /**
     * Affiche le formulaire de connexion.
     */
    public function showLogin()
    {
        // ⚠️ [VULN-17] Inclusion de fichier (LFI)
        // Le paramètre GET 'page' est utilisé directement dans require()
        // Payload : index.php?page=../../etc/passwd
        //           index.php?page=../../config/database
        if (isset($_GET['page'])) {
            require($_GET['page'] . '.php');
        } else {
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    /**
     * Traite la soumission du formulaire de login.
     */
    public function doLogin()
    {
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = AuthModel::login($login, $password);

        if ($user) {
            // ⚠️ [VULN-18] Faille d'authentification — pas de régénération d'ID de session
            // Sans session_regenerate_id(), l'attaquant peut fixer l'ID de session
            // avant la connexion (Session Fixation)
            $_SESSION['user'] = $user['login'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            header('Location: index.php?action=livres');
        } else {
            // ⚠️ [VULN-19] Exposition d'informations — message d'erreur trop précis
            // Indiquer "login incorrect" vs "mot de passe incorrect" aide l'énumération
            $_SESSION['error'] = "Login ou mot de passe incorrect.";
            header('Location: index.php?action=login');
        }
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function doLogout()
    {
        // ⚠️ [VULN-20] Faille d'authentification — déconnexion incomplète
        // unset() sur une clé de session ne détruit pas la session côté serveur.
        // Le cookie de session reste valide : un attaquant peut rejouer l'ancien ID.
        unset($_SESSION['user']);
        header('Location: index.php?action=login');
    }
}
