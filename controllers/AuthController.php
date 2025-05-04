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
