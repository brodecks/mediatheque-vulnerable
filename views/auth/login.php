<!-- =============================================================
     FICHIER : views/auth/login.php
     Rôle    : Formulaire de connexion
     ============================================================= -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion — Médiathèque</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="login-container">
        <h1>Médiathèque</h1>
        <h2>Connexion</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <!-- ⚠️ [VULN-24] XSS (Cross-Site Scripting) réfléchi
             Le message d'erreur est affiché sans échappement HTML.
             Si $_SESSION['error'] contient du HTML/JS, il sera exécuté.
             Ici la session est contrôlée côté serveur, mais le pattern
             est dangereux : si un autre endroit du code injecte du contenu
             utilisateur dans $_SESSION['error'], le XSS est exploitable. -->
            <div class="error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?action=doLogin">
            <label>Login</label>
            <input type="text" name="login" required>

            <label>Mot de passe</label>
            <input type="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>

</html>