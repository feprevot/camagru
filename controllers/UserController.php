<?php
require_once __DIR__ . '/../models/User.php';

function settings() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login");
        exit;
    }

    global $pdo;
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    $success = false;
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $notif = isset($_POST['notif']) ? 1 : 0;

        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Mot de passe actuel incorrect.";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide.";
            }

            if ($new_password && $new_password !== $confirm_password) {
                $errors[] = "Les nouveaux mots de passe ne correspondent pas.";
            }

            if (empty($errors)) {
                $updateQuery  = "UPDATE users
                 SET username = :username,
                     email    = :email,
                     notif    = :notif";   

                $params = [
                    ':username' => $username,
                    ':email'    => $email,
                    ':notif'    => $notif,
                    ':id'       => $userId
                ];
                if ($new_password) {
                    $updateQuery .= ", password = :password";
                    $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
                }

                $updateQuery .= " WHERE id = :id";

                $stmt = $pdo->prepare($updateQuery);
                $stmt->execute($params);

                $success = true;
                $_SESSION['username'] = $username;
            }
        }
    }

    $content = __DIR__ . '/../views/user/settings.php';
    include __DIR__ . '/../views/layout.php';
}
