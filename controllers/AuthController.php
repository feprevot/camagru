<?php

require_once __DIR__ . '/../models/User.php';
function register()
{
    $errors = [];

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username         = trim($_POST['username'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $password         = $_POST['password']         ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if ($username === '' || $email === '' || $password === '' || $password_confirm === '') {
            $errors[] = "Tous les champs sont obligatoires.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "invalid email format.";
        }

        if ($password !== $password_confirm) {
            $errors[] = "Passwords do not match.";
        }

        if (!preg_match('/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}/', $password)) {
            $errors[] = "Mot de passe trop faible.";
        }

        if (user_exists($email, $username)) {
            $errors[] = "Mail or username already exists.";
        }

        if (empty($errors)) {

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $token  = bin2hex(random_bytes(32));

            if (create_user($username, $email, $hashed, $token)) {
                send_confirmation_email($email, $token);

                $content = __DIR__.'/../views/auth/register_success.php';
                include __DIR__.'/../views/layout_guest.php';
                return;
            }

            $errors[] = "Error creating user.";
        }
    }

   
    $content = __DIR__.'/../views/auth/register.php';
    include __DIR__.'/../views/layout_guest.php';
}




function login() {

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $errors[] = "Tous les champs sont requis.";
        } else {
            $user = get_user_by_username($username);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = "Username or password is incorrect.";
            } elseif (!$user['is_confirmed']) {
                $errors[] = "Your account is not confirmed. Please check your email.";
            } else {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: /");
                exit;
            }
        }
    }

    $content = __DIR__.'/../views/auth/login.php';
    include __DIR__.'/../views/layout_guest.php';
}



function confirm_account() {

    $token = $_GET['token'] ?? null;
    if (!$token) {
        $content = __DIR__.'/../views/auth/confirm_error.php';
        include __DIR__.'/../views/layout_guest.php';
        return;
    }

    if (confirm_user($token)) {
        $content = __DIR__.'/../views/auth/confirm_success.php';
    } else {
        $content = __DIR__.'/../views/auth/confirm_error.php';
    }
    include __DIR__.'/../views/layout_guest.php';
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

                $pdo->prepare("UPDATE users SET reset_token = :token WHERE id = :id")
                    ->execute([':token' => $token, ':id' => $user['id']]);

                $link = "https://localhost:8443/reset?token=$token";
                mail(
                    $email,
                    "Reset Password",
                    "Hello,\n\nClick here to reset your password:\n$link",
                    "From: no-reply@camagru.local"
                );
            }
        }

        $content = __DIR__ . '/../views/auth/forgot_done.php';
        include __DIR__ . '/../views/layout_guest.php';
        return;
    }

    $content = __DIR__ . '/../views/auth/forgot.php';
    include __DIR__ . '/../views/layout_guest.php';
}


function reset_password() {
    global $pdo;

    $token = $_GET['token'] ?? '';
    if (!$token) {
        $content = __DIR__.'/../views/auth/reset_invalid.php';
        include __DIR__.'/../views/layout_guest.php';
        return;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = :token");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        $content = __DIR__.'/../views/auth/reset_invalid.php';
        include __DIR__.'/../views/layout_guest.php';
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pass     = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];

        if ($pass !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (!preg_match('/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}/', $pass)) {
            $errors[] = "Mot de passe trop faible.";
        }

        if (empty($errors)) {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = :pass, reset_token = NULL WHERE id = :id")
                ->execute([':pass' => $hashed, ':id' => $user['id']]);

            /* vue de succès */
            $content = __DIR__.'/../views/auth/reset_done.php';
            include __DIR__.'/../views/layout_guest.php';
            return;
        }

        $resetErrors = $errors;
    }

  
    $content = __DIR__.'/../views/auth/reset.php';
    include __DIR__.'/../views/layout_guest.php';
}

