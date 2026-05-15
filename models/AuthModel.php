<?php
// =============================================================
//  FICHIER : models/AuthModel.php
//  Rôle    : Gestion de l'authentification en base de données
// =============================================================

require_once __DIR__ . '/../config/database.php';

class AuthModel
{

    /**
     * Vérifie les identifiants de connexion.
     *
     * @param string $login
     * @param string $password
     * @return array|false  Données utilisateur ou false
     */
    public static function login($login, $password)
    {
        $db = getDB();

        // ⚠️ [VULN-8] Injection SQL — toujours vulnérable intentionnellement
        $hashedPassword = md5($password);
        $query = "SELECT * FROM utilisateurs WHERE login = '$login' AND password = '$hashedPassword'";
        $result = $db->query($query);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un utilisateur par son ID.
     */
    public static function getUserById($id)
    {
        $db = getDB();
        // ⚠️ [VULN-9] Injection SQL (même problème sur l'ID)
        $result = $db->query("SELECT * FROM utilisateurs WHERE id = $id");
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * @param string $login
     * @param string $password
     * @param string $role
     */
    public static function createUser($login, $password, $role = 'user')
    {
        $db = getDB();

        // ⚠️ [VULN-10] Exposition de données sensibles — stockage en MD5
        // MD5 est un algorithme cassé pour les mots de passe.
        // Il faut utiliser password_hash() avec PASSWORD_BCRYPT.
        $hashedPassword = md5($password);

        // ⚠️ [VULN-11] Injection SQL (même pattern que login)
        $db->query("INSERT INTO utilisateurs (login, password, role)
                    VALUES ('$login', '$hashedPassword', '$role')");
    }
}
