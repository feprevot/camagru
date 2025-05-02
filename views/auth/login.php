<h2>Connexion</h2>

<form method="POST" action="/login">
    <label for="username">Nom d'utilisateur</label><br>
    <input type="text" id="username" name="username" required><br><br>

    <label for="password">Mot de passe</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <button type="submit">Se connecter</button>
</form>

<p>Pas encore inscrit ? <a href="/register">Créer un compte</a></p>
<p><a href="/forgot">Mot de passe oublié ?</a></p>
