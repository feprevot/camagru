<h2>Paramètres du compte</h2>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php elseif ($success): ?>
    <p style="color:green;">Mise à jour réussie !</p>
<?php endif; ?>

<form method="post">
    <label>Nom d'utilisateur</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <hr>

    <label>Nouveau mot de passe</label>
    <input type="password" name="new_password" placeholder="(laisser vide si inchangé)">
    <label>Confirmer le nouveau mot de passe</label>
    <input type="password" name="confirm_password">

    <hr>

    <label>Mot de passe actuel (obligatoire)</label>
    <input type="password" name="current_password" required>

    <button type="submit">Mettre à jour</button>
</form>
