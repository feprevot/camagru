<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/../models/social.php';

function like_image() {
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non autorisé']);
        return;
    }

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!isset($data['image_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'image_id manquant']);
        return;
    }

    $result = toggle_like($_SESSION['user_id'], $data['image_id']);
    $count = count_likes($data['image_id']);
    echo json_encode(['status' => $result, 'likes' => $count]);
}

function comment_image() {
    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Non autorisé']);
        return;
    }

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!isset($data['image_id'], $data['content']) || trim($data['content']) === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Données incomplètes']);
        return;
    }

    add_comment($_SESSION['user_id'], $data['image_id'], $data['content']);

    global $pdo;
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    echo json_encode([
        'status' => 'success',
        'username' => $user['username'],
        'content' => htmlspecialchars($data['content'])
    ]);
}
