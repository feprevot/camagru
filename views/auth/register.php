<h2>Inscription</h2>

<form method="POST" action="/register">
    <label for="username">Nom d'utilisateur</label><br>
    <input type="text" id="username" name="username" required><br><br>

    <label for="email">Adresse e-mail</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Mot de passe</label><br>
    <input type="password" id="password" name="password" required
           pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}"
           title="Au moins 8 caractères, avec une majuscule, une minuscule et un chiffre"><br><br>

    <label for="password_confirm">Confirmation du mot de passe</label><br>
    <input type="password" id="password_confirm" name="password_confirm" required><br><br>

    <button type="submit">Créer mon compte</button>
</form>

<p>Déjà inscrit ? <a href="/login">Connexion</a></p>
