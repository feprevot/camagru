<?php
require_once __DIR__ . '/../config/database.php';

function toggle_like($user_id, $image_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = :user_id AND image_id = :image_id");
    $stmt->execute(['user_id' => $user_id, 'image_id' => $image_id]);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND image_id = :image_id");
        $stmt->execute(['user_id' => $user_id, 'image_id' => $image_id]);
        return 'unliked';
    } else {
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, image_id) VALUES (:user_id, :image_id)");
        $stmt->execute(['user_id' => $user_id, 'image_id' => $image_id]);
        return 'liked';
    }
}

function count_likes($image_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE image_id = :id");
    $stmt->execute([':id' => $image_id]);
    return $stmt->fetchColumn();
}

function has_liked($user_id, $image_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = :user_id AND image_id = :image_id");
    $stmt->execute(['user_id' => $user_id, 'image_id' => $image_id]);
    return (bool)$stmt->fetch();
}

function add_comment($user_id, $image_id, $content) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO comments (user_id, image_id, content) VALUES (:user_id, :image_id, :content)");
    $stmt->execute([
        'user_id' => $user_id,
        'image_id' => $image_id,
        'content' => htmlspecialchars($content)
    ]);
}

function get_comments($image_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE image_id = :image_id ORDER BY created_at ASC");
    $stmt->execute(['image_id' => $image_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
