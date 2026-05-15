# 🏛️ Médiathèque — Application volontairement vulnérable

> Projet d'entraînement à la cybersécurité dans le cadre du **BTS SIO option SLAM**.  
> Cette application contient des vulnérabilités intentionnelles à identifier et corriger.

---

## ⚠️ Avertissement

**Ce projet est volontairement non sécurisé.**  
Il est destiné uniquement à un usage pédagogique en environnement local.  
Ne jamais déployer cette application sur un serveur accessible publiquement.

---

## 🎯 Objectif

Identifier et corriger **29 vulnérabilités** réparties dans le code source, couvrant :

| Catégorie                       | Occurrences |
| ------------------------------- | :---------: |
| Injection SQL                   |      7      |
| XSS (réfléchi et stocké)        |      6      |
| CSRF                            |      4      |
| Authentification / Session      |      6      |
| Inclusion de fichiers (LFI)     |      1      |
| Exposition de données sensibles |      5      |

---

## 🗂️ Structure du projet

```
mediatheque/
├── index.php                  ← Routeur principal (Front Controller)
├── .env                       ← Variables d'environnement (non versionné)
├── .env.example               ← Modèle de configuration
├── config/
│   ├── database.php           ← Connexion PDO
│   └── schema.sql             ← Schéma BDD + données de test
├── controllers/
│   ├── AuthController.php     ← Login / Logout
│   └── LivreController.php    ← CRUD livres
├── models/
│   ├── AuthModel.php          ← Requêtes auth en BDD
│   └── LivreModel.php         ← Requêtes livres en BDD
├── views/
│   ├── auth/login.php         ← Page de connexion
│   └── livres/
│       ├── index.php          ← Liste des livres
│       └── form.php           ← Formulaire ajout/modification
├── includes/
│   └── session.php            ← Gestion des sessions
├── assets/
│   └── style.css              ← Feuille de styles
└── CORRECTION.md              ← 🔒 Corrigé (à ouvrir après !)
```

---

## 🚀 Installation

### Prérequis

- WAMP / XAMPP
- PHP 8.x
- MySQL
- Composer

### 1. Cloner le projet

```bash
git clone https://github.com/brodecks/mediatheque-vulnerable.git
```

Placer le dossier dans `C:\wamp64\www\`.

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

Copier `.env.example` en `.env` et renseigner les valeurs :

```
DB_HOST=localhost
DB_NAME=mediatheque
DB_USER=root
DB_PASS=
```

### 4. Créer la base de données

Dans phpMyAdmin, exécuter le contenu de `config/schema.sql`.

### 5. Lancer l'application

```
http://localhost/mediatheque/index.php
```

---

## 👤 Comptes de test

| Login   | Mot de passe | Rôle           |
| ------- | ------------ | -------------- |
| `admin` | `admin`      | Administrateur |
| `alice` | `user123`    | Utilisateur    |

---

## 🔍 Méthode de travail conseillée

1. Lire chaque fichier source et repérer les vulnérabilités
2. Pour chaque faille trouvée, noter : **nom**, **fichier**, **ligne**, **impact**, **correction**
3. Corriger le code
4. Tester que l'exploitation ne fonctionne plus
5. Comparer avec `CORRECTION.md`
