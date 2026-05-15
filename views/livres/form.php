<!-- =============================================================
     FICHIER : views/livres/form.php
     Rôle    : Formulaire d'ajout / modification d'un livre
     ============================================================= -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= isset($livre) ? 'Modifier' : 'Ajouter' ?> un livre</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="form-container">
<h1><?= isset($livre) ? 'Modifier' : 'Ajouter' ?> un livre</h1>

<!-- ⚠️ [VULN-29] CSRF — aucun token dans ce formulaire
     Il manque un champ caché <input type="hidden" name="csrf_token" value="...">
     généré côté serveur et vérifié dans le contrôleur.
     Sans ce mécanisme, le formulaire peut être soumis depuis n'importe quel site. -->

<form method="POST" action="index.php?action=<?= isset($livre) ? 'update' : 'store' ?>">
    <?php if (isset($livre)): ?>
        <input type="hidden" name="id" value="<?= $livre['id'] ?>">
    <?php endif; ?>

    <label>Titre</label>
    <!-- ⚠️ [VULN-30] XSS — les valeurs pré-remplies ne sont pas échappées
         Payload dans la BDD : "><script>alert(1)</script>
         Cela ferme l'attribut value et injecte du JavaScript. -->
    <input type="text" name="titre" value="<?= isset($livre) ? $livre['titre'] : '' ?>">

    <label>Auteur</label>
    <input type="text" name="auteur" value="<?= isset($livre) ? $livre['auteur'] : '' ?>">

    <label>Genre</label>
    <input type="text" name="genre" value="<?= isset($livre) ? $livre['genre'] : '' ?>">

    <label>Année</label>
    <input type="number" name="annee" value="<?= isset($livre) ? $livre['annee'] : '' ?>">

    <button type="submit"><?= isset($livre) ? 'Modifier' : 'Ajouter' ?></button>
    <a href="index.php?action=livres">Annuler</a>
</form>
</div>
</body>
</html>
