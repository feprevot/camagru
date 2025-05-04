<?php

function show_gallery() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login");
        exit;
    }

    $content = __DIR__ . '/../views/gallery/index.php';
    include __DIR__ . '/../views/layout.php';
}
