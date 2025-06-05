<?php
require_once __DIR__ . '/src/gallery.php';

    if (!isset($_GET['slug']) || !galleryExists($_GET['slug'])) {
    http_response_code(404);
    exit('Gallery not found');
}
downloadGallery($_GET['slug']);
exit;
