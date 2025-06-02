<?php

require_once __DIR__ . '/src/gallery.php';

header('Content-Type: application/json');

$slug = $_GET['slug'];

$gallery = getGallery($slug);

if ($gallery === null) {
    http_response_code(404);
    echo json_encode(['error' => 'Gallery not found']);
    exit;
}

echo json_encode($gallery);

?>