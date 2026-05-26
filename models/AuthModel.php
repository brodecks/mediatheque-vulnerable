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
        $query = "SELECT * FROM utilisateurs WHERE login = :login";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Récupère un utilisateur par son ID.
     */
    public static function getUserById($id)
    {
        $db = getDB();
        // ⚠️ [VULN-9] Injection SQL (même problème sur l'ID)
        $query = "SELECT * FROM utilisateurs WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
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
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        // ⚠️ [VULN-11] Injection SQL (même pattern que login)
        $stmt = $db->prepare("INSERT INTO utilisateurs (login, password, role)
                    VALUES (:login, :password, :role)");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
    }
}
