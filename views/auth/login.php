<h2>Login</h2>

<form method="POST" action="/login">
    <label for="username">Username</label><br>
    <input type="text" id="username" name="username" required><br><br>

    <label for="password">Mot de passe</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>
<p><a href="/forgot">Forget your password ?</a></p>
<p>Not registered? <a href="/register">Create an account</a></p>
