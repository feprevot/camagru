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
