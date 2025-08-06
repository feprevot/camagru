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
            $errors[] = "Wrong current password.";
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email.";
            }

            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
            $stmt->execute([':username' => $username, ':id' => $userId]);
            if ($stmt->fetch()) {
                $errors[] = "Username already taken.";
            }

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $stmt->execute([':email' => $email, ':id' => $userId]);
            if ($stmt->fetch()) {
                $errors[] = "Email already in use.";
            }

            if ($new_password && $new_password !== $confirm_password) {
                $errors[] = "New passwords do not match.";
            }

            if (empty($errors)) {
                $query = "
                    UPDATE users
                    SET username = :username,
                        email = :email,
                        notif = :notif";

                $params = [
                    ':username' => $username,
                    ':email' => $email,
                    ':notif' => $notif,
                    ':id' => $userId
                ];

                if ($new_password) {
                    $query .= ", password = :password";
                    $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
                }

                $query .= " WHERE id = :id";

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);

                $success = true;

                $_SESSION['username'] = $username;

                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->execute([':id' => $userId]);
                $user = $stmt->fetch();
            }
        }
    }
    $content = __DIR__ . '/../views/user/settings.php';
    include __DIR__ . '/../views/layout.php';
}
