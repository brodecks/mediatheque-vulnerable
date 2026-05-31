-- =============================================================
--  FICHIER : config/schema.sql
--  Rôle    : Schéma de base de données pour la médiathèque
-- =============================================================

CREATE DATABASE IF NOT EXISTS mediatheque CHARACTER SET utf8 COLLATE utf8_general_ci;
USE mediatheque;

-- Table des utilisateurs
-- ⚠️ [VULN-32] Exposition de données sensibles
-- Le mot de passe est stocké en MD5 (colonne 'password' de longueur 32)
-- MD5 est vulnérable aux attaques rainbow table
CREATE TABLE utilisateurs (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    login    VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255)  NOT NULL,   -- MD5 : 32 caractères (devrait être VARCHAR(255) pour bcrypt)
    role     ENUM('user', 'admin') DEFAULT 'user'
);

-- Table des livres
CREATE TABLE livres (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    titre  VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    genre  VARCHAR(100),
    annee  INT
);

-- Données de test
-- ⚠️ Mots de passe MD5 : 'admin' = 21232f297a57a5a743894a0e4a801fc3
--                        'user123' = 3fc0a7acf087f549ac2b266baf94b8b1
INSERT INTO utilisateurs (login, password, role) VALUES
    ('admin', '$2y$10$otanSptOo0n3s2GZv41Bv.cYmXiK0QRFo32afLsXE9lM8Nt1GaTqa', 'admin'),
    ('alice', '$2y$10$wFDZ9L5SvPcwlE1PmcWdX.va5Ml.fOkHFviN5y1ZmMabcYXAmPVjK', 'user');

INSERT INTO livres (titre, auteur, genre, annee) VALUES
    ('Le Petit Prince',          'Antoine de Saint-Exupéry', 'Conte',   1943),
    ('1984',                     'George Orwell',             'Dystopie',1949),
    ('L\Étranger',              'Albert Camus',              'Roman',   1942),
    ('Harry Potter T1',          'J.K. Rowling',              'Fantasy', 1997),
    ('Les Misérables',           'Victor Hugo',               'Roman',   1862);
