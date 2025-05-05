<h2>Register</h2>

<form method="POST" action="/register">
    <label for="username">Username</label><br>
    <input type="text" id="username" name="username" required><br><br>

    <label for="email">e-mail</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Password</label><br>
    <input type="password" id="password" name="password" required
           pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}"
           title="8 chars, 1 caps, 1 special char"><br><br>

    <label for="password_confirm">Confirm it</label><br>
    <input type="password" id="password_confirm" name="password_confirm" required><br><br>

    <button type="submit">Create my account</button>
</form>

<p>Already registered? <a href="/login">Login</a></p>
