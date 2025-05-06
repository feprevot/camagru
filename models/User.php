<?php

require_once __DIR__ . '/../config/database.php';

function user_exists($email, $username) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
    $stmt->execute([
        ':email' => $email,
        ':username' => $username
    ]);

    return $stmt->fetch() !== false;
}

function create_user($username, $email, $hashed_password, $token) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, confirmation_token) VALUES (:username, :email, :password, :token)");

    return $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashed_password,
        ':token' => $token
    ]);
}


function get_user_by_username($username) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function confirm_user($token) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE confirmation_token = :token AND is_confirmed = 0");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        $update = $pdo->prepare("UPDATE users SET is_confirmed = 1, confirmation_token = NULL WHERE id = :id");
        return $update->execute([':id' => $user['id']]);
    }

    return false;
}


function send_confirmation_email($email, $token) {
    $subject = "Confirmation of your account";
    $link = "https://localhost:8443/confirm?token=" . urlencode($token);

    $message = "Hi there!\n\n Please confirm your account by clicking the link below:\n";
    $headers = "From: no-reply@camagru.local";

    return mail($email, $subject, $message, $headers);
}
