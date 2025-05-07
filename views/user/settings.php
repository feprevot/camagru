<h2>Settings</h2>

<?php if (!empty($errors)): ?>
    <ul style="color:red;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php elseif ($success): ?>
    <p style="color:green;">Update done!</p>
<?php endif; ?>

<form method="post">
    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <hr>

    <label>New password</label>
    <input type="password" name="new_password" placeholder="(leave empty to keep current password)">
    <label>Confirm the new password</label>
    <input type="password" name="confirm_password">

    <hr>

    <label>
        <input type="checkbox" name="notif"
            value="1" <?= $user['notif'] ? 'checked' : '' ?>>
        Receive notifications
    </label>
    
    <hr>

    <label>Actual password</label>
    <input type="password" name="current_password" required>

    <button type="submit">Update</button>
</form>
