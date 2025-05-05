<?php

function show_gallery() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login");
        exit;
    }

    $content = __DIR__ . '/../views/gallery/index.php';
    include __DIR__ . '/../views/layout.php';
}


require_once __DIR__ . '/../models/Image.php';

function edit_page() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login");
        exit;
    }

    $images = get_user_images($_SESSION['user_id']);

    $content = __DIR__ . '/../views/edit/index.php';
    include __DIR__ . '/../views/layout.php';
}


function upload_image() {
    error_log("upload_image appelée");

    if (!isset($_SESSION['user_id'])) {
        error_log("Non autorisé");
        http_response_code(401);
        echo "Non autorisé";
        return;
    }

    if (!empty($_FILES['file'])) {
        $file = $_FILES['file'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $tmpName = $file['tmp_name'];
            $filename = uniqid('img_') . '.png';
            $uploadDir = __DIR__ . '/../public/uploads/';
            $destination = $uploadDir . $filename;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($tmpName, $destination)) {
                save_image($_SESSION['user_id'], $filename);
                header("Location: /edit");
                exit;
            } else {
                echo "Erreur lors de la sauvegarde.";
            }
        } else {
            echo "Erreur d'upload : " . $file['error'];
        }
        return;
    }

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!isset($data['image'])) {
        error_log("Image manquante");
        http_response_code(400);
        echo "Image manquante";
        return;
    }

    $imgData = $data['image'];

    if (preg_match('/^data:image\/png;base64,/', $imgData)) {
        $imgData = substr($imgData, strpos($imgData, ',') + 1);
        $imgData = base64_decode($imgData);

        if ($imgData === false) {
            error_log("Erreur de décodage base64");
            http_response_code(400);
            echo "Erreur de décodage";
            return;
        }

        $filename = uniqid('img_') . '.png';
        $uploadDir = __DIR__ . '/../public/uploads/';
        $path = $uploadDir . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!file_put_contents($path, $imgData)) {
            error_log("Erreur d'écriture du fichier $path");
            http_response_code(500);
            echo "Erreur lors de la sauvegarde du fichier.";
            return;
        }

        require_once __DIR__ . '/../models/Image.php';
        if (!save_image($_SESSION['user_id'], $filename)) {
            error_log("Erreur insertion BDD");
            http_response_code(500);
            echo "Erreur d'enregistrement en base de données.";
            return;
        }

        error_log("Image enregistrée avec succès !");
        echo "Image enregistrée";
    } else {
        error_log("Format non supporté");
        http_response_code(400);
        echo "Format non supporté";
    }
}

function api_gallery() {
    global $pdo;

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;

    $stmt = $pdo->prepare("SELECT images.*, users.username FROM images JOIN users ON images.user_id = users.id ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($images);
}



function delete_image() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo "Non autorisé";
        return;
    }

    $filename = $_POST['filename'] ?? '';
    if (empty($filename)) {
        http_response_code(400);
        echo "Fichier manquant";
        return;
    }

    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM images WHERE filename = :filename AND user_id = :user_id");
    $stmt->execute([
        ':filename' => $filename,
        ':user_id' => $_SESSION['user_id']
    ]);

    $image = $stmt->fetch();

    if ($image) {
        $path = __DIR__ . '/../public/uploads/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }

        $deleteStmt = $pdo->prepare("DELETE FROM images WHERE id = :id");
        $deleteStmt->execute([':id' => $image['id']]);
    }

    header("Location: /edit");
    exit;
}