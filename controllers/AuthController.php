<?php

require_once __DIR__ . '/../models/User.php';

function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        $errors = [];

        if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
            $errors[] = "Tous les champs sont obligatoires.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }

        if ($password !== $password_confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        if (!preg_match('/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}/', $password)) {
            $errors[] = "Mot de passe trop faible.";
        }

        if (user_exists($email, $username)) {
            $errors[] = "Cet email ou nom d'utilisateur est déjà utilisé.";
        }

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));

            if (create_user($username, $email, $hashed_password, $token)) {
                send_confirmation_email($email, $token);
                header("Location: /login");
                exit;
            } else {
                $errors[] = "Erreur lors de la création du compte.";
            }
        }

        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }

    require __DIR__ . '/../views/auth/register.php';
}



function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors = [];

        if (empty($username) || empty($password)) {
            $errors[] = "Tous les champs sont requis.";
        } else {
            $user = get_user_by_username($username);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = "Nom d'utilisateur ou mot de passe incorrect.";
            } elseif (!$user['is_confirmed']) {
                $errors[] = "Votre compte n'est pas encore confirmé.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: /");
                exit;
                            }
        }

        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }

    require __DIR__ . '/../views/auth/login.php';
}


function confirm_account() {
    if (!isset($_GET['token'])) {
        echo "<p style='color:red;'>Token manquant.</p>";
        return;
    }

    $token = $_GET['token'];

    if (confirm_user($token)) {
        echo "<p style='color:green;'>Votre compte a été confirmé avec succès !</p>";
        echo "<p><a href='/login'>Se connecter</a></p>";
    } else {
        echo "<p style='color:red;'>Lien invalide ou déjà utilisé.</p>";
    }
}


function logout() {
    session_unset();
    session_destroy();
    header("Location: /login");
    exit;
}


function forgot_password() {
    global $pdo;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $stmt = $pdo->prepare("UPDATE users SET reset_token = :token WHERE id = :id");
                $stmt->execute([':token' => $token, ':id' => $user['id']]);

                $link = "https://localhost:8443/reset?token=$token";
                $subject = "Réinitialisation du mot de passe Camagru";
                $message = "Bonjour,\n\nCliquez ici pour réinitialiser votre mot de passe :\n$link";
                mail($email, $subject, $message, "From: no-reply@camagru.local");
            }
        }

        echo "<p>Si un compte existe avec cet e‑mail, un lien de réinitialisation a été envoyé.</p>";
        return;
    }

    require __DIR__ . '/../views/auth/forgot.php';
}


function reset_password() {
    global $pdo;

    $token = $_GET['token'] ?? '';
    if (!$token) {
        echo "Lien invalide.";
        return;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = :token");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Lien invalide ou expiré.";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pass = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if ($pass === $confirm && preg_match('/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}/', $pass)) {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = :pass, reset_token = NULL WHERE id = :id");
            $stmt->execute([':pass' => $hashed, ':id' => $user['id']]);

            echo "<p>Mot de passe réinitialisé. <a href=\"/login\">Se connecter</a></p>";
            return;
        } else {
            echo "<p style='color:red;'>Mots de passe invalides ou différents.</p>";
        }
    }

    require __DIR__ . '/../views/auth/reset.php';
}
