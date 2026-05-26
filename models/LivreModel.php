<?php
// =============================================================
//  FICHIER : models/LivreModel.php
//  Rôle    : Opérations CRUD sur les livres
// =============================================================

require_once __DIR__ . '/../config/database.php';

class LivreModel
{

    /**
     * Retourne tous les livres, avec recherche optionnelle.
     *
     * @param string $search  Terme de recherche
     * @return array
     */
    public static function getAll($search = '')
    {
        $db = getDB();
        if ($search !== '') {
            // ⚠️ [VULN-12] Injection SQL dans la recherche
            // Payload : ' UNION SELECT login, password, role, null, null FROM utilisateurs --
            // Permet d'exfiltrer la table utilisateurs via la page de recherche
            $stmt = $db->prepare("SELECT * FROM livres WHERE titre LIKE :search OR auteur LIKE :search");
            $stmt->bindValue(':search', '%' . $search . '%');
        } else {
            $stmt = $db->prepare("SELECT * FROM livres");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne un livre par son ID.
     */
    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM livres WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un nouveau livre.
     */
    public static function create($titre, $auteur, $genre, $annee)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO livres (titre, auteur, genre, annee)
                VALUES (:titre, :auteur, :genre, :annee)");
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':auteur', $auteur);
        $stmt->bindValue(':genre', $genre);
        $stmt->bindValue(':annee', $annee, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Modifie un livre existant.
     */
    public static function update($id, $titre, $auteur, $genre, $annee)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE livres SET titre = :titre, auteur = :auteur,
                genre = :genre, annee = :annee WHERE id = :id");
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':auteur', $auteur);
        $stmt->bindValue(':genre', $genre);
        $stmt->bindValue(':annee', $annee, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Supprime un livre.
     */
    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM livres WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
