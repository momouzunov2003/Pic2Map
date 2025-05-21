<?php
require_once __DIR__ . '/src/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['slug']) || !isset($_FILES['images'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$slug = preg_replace('/[^a-zA-Z0-9-_]/', '', $_POST['slug']);
$stmt = dbQuery("SELECT id FROM galleries WHERE slug = :slug", [
    ':slug' => $slug
]);
$gallery = $stmt->fetch(PDO::FETCH_ASSOC);
$galleryId = $gallery['id'];
if (!$galleryId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'gallery not found']);
    exit;
}

$uploadDir = __DIR__ . '/public/uploads/' . $slug;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadedFiles = $_FILES['images'];
$results = [];

for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
    $tmpName = $uploadedFiles['tmp_name'][$i];
    $originalName = basename($uploadedFiles['name'][$i]);
    $targetPath = $uploadDir . '/' . uniqid() . '-' . $originalName;

    if (getimagesize($tmpName)) {
        if (move_uploaded_file($tmpName, $targetPath)) {
            dbQuery("INSERT INTO images (gallery_id, filename) VALUES (:gallery_id, :filename)", [
                ':gallery_id' => $galleryId,
                ':filename' => $targetPath
            ]);
            $results[] = ['file' => $originalName, 'status' => 'uploaded'];
        } else {
            $results[] = ['file' => $originalName, 'status' => 'failed'];
        }
    } else {
        $results[] = ['file' => $originalName, 'status' => 'invalid'];
    }
}

echo json_encode(['status' => 'done', 'results' => $results]);

?>