<!-- views/layout_guest.php -->
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Camagru</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<header>
    <h1><a href="/">Camagru</a></h1>
    <nav>
        <a href="/">Gallery</a>
        <a href="/login">Login</a> |
        <a href="/register">Register</a>
    </nav>
</header>

<div class="auth-wrapper">
    <?php include $content; ?>
</div>

<footer>© Camagru 2025</footer>
</body>
</html>
