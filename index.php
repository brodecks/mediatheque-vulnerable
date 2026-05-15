<?php
// =============================================================
//  FICHIER : index.php
//  Rôle    : Routeur principal (Front Controller)
// =============================================================

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/LivreController.php';

$action = $_GET['action'] ?? 'login';

$authCtrl  = new AuthController();
$livreCtrl = new LivreController();

switch ($action) {
    // --- Auth ---
    case 'login':
        $authCtrl->showLogin();
        break;
    case 'doLogin':
        $authCtrl->doLogin();
        break;
    case 'logout':
        $authCtrl->doLogout();
        break;

    // --- Livres ---
    case 'livres':
        $livreCtrl->index();
        break;
    case 'create':
        $livreCtrl->create();
        break;
    case 'store':
        $livreCtrl->store();
        break;
    case 'edit':
        $livreCtrl->edit();
        break;
    case 'update':
        $livreCtrl->update();
        break;
    case 'delete':
        $livreCtrl->delete();
        break;

    default:
        // ⚠️ [VULN-31] XSS réfléchi dans le message d'erreur 404
        // $action provient directement de $_GET sans aucun échappement
        echo "Action inconnue : " . $action;
        break;
}
