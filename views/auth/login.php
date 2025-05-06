<h2>Login</h2>

<?php if (!empty($errors)): ?>
    <ul class="error">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="/login">
    <label for="username">Username</label>
    <input id="username" name="username" required>

    <label for="password">Password</label>
    <input id="password" name="password" type="password" required>

    <button type="submit">Login</button>
</form>

<p class="here"><a href="/forgot">Forgot password ?</a></p>
<p class="here" >Not registered ? <a href="/register">Create an account</a></p>
