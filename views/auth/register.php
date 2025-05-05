<h2>Register</h2>

<?php if (!empty($errors)): ?>
    <ul class="error">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="/register">
    <label>Username</label>
    <input name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

    <label>E‑mail</label>
    <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

    <label>Password</label>
    <input type="password" name="password" required
           pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}"
           title="8+ chars, 1 upper, 1 digit">

    <label>Confirm password</label>
    <input type="password" name="password_confirm" required>

    <button type="submit">Create my account</button>
</form>

<p>Already registered ? <a href="/login">Login</a></p>
