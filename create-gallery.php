<?php 
require_once __DIR__ . '/src/gallery.php';
define('APP_ROOT', '/pic2map');

header('Content-Type: application/json');
$slug = createGallery();
// echo json_encode(['slug' => $slug]);
header('Location: ' . APP_ROOT . '/gallery/' . $slug);
?>