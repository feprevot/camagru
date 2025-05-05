<?php

require_once __DIR__ . '/../config/database.php';

function save_image($user_id, $filename) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO images (user_id, filename) VALUES (:user_id, :filename)");
    return $stmt->execute([
        ':user_id' => $user_id,
        ':filename' => $filename
    ]);
}


function get_user_images($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM images WHERE user_id = :id ORDER BY created_at DESC");
    $stmt->execute([':id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_public_images($limit, $offset) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT images.*, users.username FROM images JOIN users ON images.user_id = users.id ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
