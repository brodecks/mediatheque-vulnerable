<?php
// =============================================================
//  FICHIER : models/LivreModel.php
//  Rôle    : Opérations CRUD sur les livres
// =============================================================

require_once __DIR__ . '/../config/database.php';

class LivreModel {

    /**
     * Retourne tous les livres, avec recherche optionnelle.
     *
     * @param string $search  Terme de recherche
     * @return array
     */
    public static function getAll($search = '') {
        $db = getDB();
        if ($search !== '') {
            // ⚠️ [VULN-12] Injection SQL dans la recherche
            // Payload : ' UNION SELECT login, password, role, null, null FROM utilisateurs --
            // Permet d'exfiltrer la table utilisateurs via la page de recherche
            $query = "SELECT * FROM livres WHERE titre LIKE '%$search%' OR auteur LIKE '%$search%'";
        } else {
            $query = "SELECT * FROM livres";
        }
        return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne un livre par son ID.
     */
    public static function getById($id) {
        $db = getDB();
        // ⚠️ [VULN-13] Injection SQL sur l'ID
        $result = $db->query("SELECT * FROM livres WHERE id = $id");
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un nouveau livre.
     */
    public static function create($titre, $auteur, $genre, $annee) {
        $db = getDB();
        // ⚠️ [VULN-14] Injection SQL à l'insertion
        $db->query("INSERT INTO livres (titre, auteur, genre, annee)
                    VALUES ('$titre', '$auteur', '$genre', '$annee')");
    }

    /**
     * Modifie un livre existant.
     */
    public static function update($id, $titre, $auteur, $genre, $annee) {
        $db = getDB();
        // ⚠️ [VULN-15] Injection SQL à la mise à jour
        $db->query("UPDATE livres SET titre='$titre', auteur='$auteur',
                    genre='$genre', annee='$annee' WHERE id=$id");
    }

    /**
     * Supprime un livre.
     */
    public static function delete($id) {
        $db = getDB();
        // ⚠️ [VULN-16] Injection SQL à la suppression
        $db->query("DELETE FROM livres WHERE id = $id");
    }
}
