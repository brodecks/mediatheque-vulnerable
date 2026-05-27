<!-- =============================================================
     FICHIER : views/livres/index.php
     Rôle    : Liste des livres et barre de recherche
     ============================================================= -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Livres — Médiathèque</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <nav>
        <span>Connecté : <?= $_SESSION['user'] ?></span>
        <a href="index.php?action=logout">Déconnexion</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="index.php?action=create">Ajouter un livre</a>
        <?php endif; ?>
    </nav>

    <h1>Catalogue des livres</h1>

    <!-- Barre de recherche -->
    <form class="search-form" method="GET" action="index.php">
        <input type="hidden" name="action" value="livres">
        <!-- ⚠️ [VULN-25] XSS réfléchi dans la barre de recherche
         Le terme de recherche est réaffiché sans htmlspecialchars().
         Payload : ?action=livres&search=<script>alert('XSS')</script>
         Le script s'exécute dans le navigateur de la victime. -->
        <input type="text" name="search" value="<?= $search ?>">
        <button type="submit">Rechercher</button>
    </form>

    <?php if (!empty($search)): ?>
        <!-- ⚠️ [VULN-26] XSS réfléchi — deuxième occurrence -->
        <p>Résultats pour : <strong><?= $search ?></strong></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Genre</th>
                <th>Année</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <!-- ⚠️ [VULN-27] XSS stocké
                 Les données issues de la base de données sont affichées sans échappement.
                 Si un attaquant a inséré du JavaScript via le formulaire d'ajout,
                 il s'exécutera pour tous les utilisateurs qui consultent cette page. -->
                    <td><?= $livre['id'] ?></td>
                    <td><?= $livre['titre'] ?></td>
                    <td><?= $livre['auteur'] ?></td>
                    <td><?= $livre['genre'] ?></td>
                    <td><?= $livre['annee'] ?></td>
                    <td>
                        <a href="index.php?action=edit&id=<?= $livre['id'] ?>">Modifier</a>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <!-- ⚠️ [VULN-28] CSRF via lien GET — voir LivreController::delete() -->
                            <form method="POST" action="index.php?action=delete">
                                <input type="hidden" name="id" value="<?= $livre['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>