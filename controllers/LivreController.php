<?php
// =============================================================
//  FICHIER : controllers/LivreController.php
//  Rôle    : CRUD livres + recherche
// =============================================================

require_once __DIR__ . '/../models/LivreModel.php';
require_once __DIR__ . '/../includes/session.php';

class LivreController {

    /**
     * Liste tous les livres (avec recherche).
     */
    public function index() {
        requireLogin();
        // Récupère le terme de recherche sans aucun nettoyage
        $search = $_GET['search'] ?? '';
        $livres = LivreModel::getAll($search);
        require __DIR__ . '/../views/livres/index.php';
    }

    /**
     * Affiche le formulaire d'ajout.
     */
    public function create() {
        requireLogin();
        require __DIR__ . '/../views/livres/form.php';
    }

    /**
     * Traite l'ajout d'un livre.
     */
    public function store() {
        requireLogin();

        // ⚠️ [VULN-21] CSRF — aucun token CSRF n'est vérifié
        // N'importe quel site tiers peut soumettre ce formulaire
        // via une requête POST forgée au nom de l'utilisateur connecté.
        // Exemple d'attaque :
        // <form action="http://mediatheque.local/index.php?action=store" method="POST">
        //   <input name="titre" value="Livre injecté">
        //   ...
        // </form>
        // <script>document.forms[0].submit();</script>

        $titre  = $_POST['titre']  ?? '';
        $auteur = $_POST['auteur'] ?? '';
        $genre  = $_POST['genre']  ?? '';
        $annee  = $_POST['annee']  ?? '';

        LivreModel::create($titre, $auteur, $genre, $annee);
        header('Location: index.php?action=livres');
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit() {
        requireLogin();
        $id    = $_GET['id'] ?? 0;
        $livre = LivreModel::getById($id);
        require __DIR__ . '/../views/livres/form.php';
    }

    /**
     * Traite la modification d'un livre.
     */
    public function update() {
        requireLogin();
        // ⚠️ [VULN-22] CSRF — même absence de token que store()
        $id     = $_POST['id']     ?? 0;
        $titre  = $_POST['titre']  ?? '';
        $auteur = $_POST['auteur'] ?? '';
        $genre  = $_POST['genre']  ?? '';
        $annee  = $_POST['annee']  ?? '';

        LivreModel::update($id, $titre, $auteur, $genre, $annee);
        header('Location: index.php?action=livres');
    }

    /**
     * Supprime un livre.
     */
    public function delete() {
        requireAdmin();
        // ⚠️ [VULN-23] CSRF — suppression déclenchable via un simple lien GET forgé
        // Payload : <img src="http://mediatheque.local/index.php?action=delete&id=1">
        // Un simple chargement d'image dans une page tierce suffit à supprimer un livre.
        $id = $_GET['id'] ?? 0;
        LivreModel::delete($id);
        header('Location: index.php?action=livres');
    }
}
