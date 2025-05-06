<?php

function show_gallery() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login");
        exit;
    }

    $content = __DIR__ . '/../views/gallery/index.php';
    include __DIR__ . '/../views/layout.php';
}

function show_public_gallery() {
    $content = __DIR__ . '/../views/gallery/index_public.php';
    include __DIR__ . '/../views/layout_guest.php';
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
function upload_image()
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        header("Location: /edit");
        exit("Unauthorized");
    }

    if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['file']['tmp_name'];
        $filename = uniqid('img_') . '.png';
        $uploadDir = __DIR__ . '/../public/uploads/';
        $destination = $uploadDir . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $allowedTypes = ['image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowedTypes)) {
            http_response_code(400);
            header("Location: /edit");
            exit("Unorthorized file type");
        }
        
        if (move_uploaded_file($tmpName, $destination)) {
            save_image($_SESSION['user_id'], $filename);
            header("Location: /edit");
            exit;
        } else {
            http_response_code(500);
            header("Location: /edit");
            exit("Error during upload");
        }
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    if (!$payload || !isset($payload['image'])) {
        http_response_code(400); 
        header("Location: /edit");
        exit('Bad request');
    }

    if (!preg_match('#^data:image/[^;]+;base64,#', $payload['image'])) {
        http_response_code(400); 
        header("Location: /edit");
        exit('Format non supporté');
    }

    $raw = base64_decode(substr($payload['image'], strpos($payload['image'], ',') + 1));
    if ($raw === false) { 
        http_response_code(400); 
        header("Location: /edit");
        exit('decode error'); 
    }

    $src = imagecreatefromstring($raw);
    if (!$src) { 
        http_response_code(500); 
        header("Location: /edit");
        exit('GD error'); 
    }

    $ovPath = $payload['overlay'] ?? '';
    if ($ovPath && $ovPath !== 'none') {
        $ovAbs = __DIR__ . '/../public' . $ovPath;
        if (file_exists($ovAbs)) {
            $overlay = imagecreatefrompng($ovAbs);
            $dstW = imagesx($src);
            $dstH = imagesy($src);
            $tmp = imagecreatetruecolor($dstW, $dstH);
            imagesavealpha($tmp, true);
            $trans = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
            imagefill($tmp, 0, 0, $trans);
            imagecopyresampled($tmp, $overlay, 0, 0, 0, 0, $dstW, $dstH, imagesx($overlay), imagesy($overlay));
            imagecopy($src, $tmp, 0, 0, 0, 0, $dstW, $dstH);
            imagedestroy($tmp);
            imagedestroy($overlay);
        }
    }

    $filename = uniqid('img_') . '.png';
    $uploadDir = __DIR__ . '/../public/uploads/';
    $path = $uploadDir . $filename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    imagesavealpha($src, true);
    if (!imagepng($src, $path)) {
        imagedestroy($src);
        http_response_code(500);
        header("Location: /edit");
        exit('save error');
    }
    imagedestroy($src);

    save_image($_SESSION['user_id'], $filename);
    echo 'ok';
}



require_once __DIR__ . '/../models/social.php';

function api_gallery() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'] ?? null;

    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = 5;
    $offset = ($page - 1) * $limit;

    global $pdo;
    $stmt = $pdo->prepare("
        SELECT images.id, images.filename, images.created_at,
               users.username
        FROM images
        JOIN users ON images.user_id = users.id
        ORDER BY images.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit',  $limit , PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($images as &$img) {
        $img['likes']    = count_likes($img['id']);       
        $img['comments'] = get_comments($img['id']);     
        $img['liked']    = $user_id ? has_liked($user_id, $img['id']) : false;
    }

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

