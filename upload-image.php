<?php
require_once __DIR__ . '/src/db.php';

$allowed_types = ['image/jpeg', 'image/png', 'image/tiff', 'image/webp'];

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed ']);
    exit;
} 

if (!isset($_POST['slug'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No slug provided']);
    exit;
}

if (!isset($_FILES['images'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No files uploaded']);
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
if (!is_array($uploadedFiles['name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file upload']);
    exit;
}

function getGps($exifCoord, $hemisphere) {
    $degrees = count($exifCoord) > 0 ? eval('return ' . $exifCoord[0] . ';') : 0;
    $minutes = count($exifCoord) > 1 ? eval('return ' . $exifCoord[1] . ';') : 0;
    $seconds = count($exifCoord) > 2 ? eval('return ' . $exifCoord[2] . ';') : 0;

    $flip = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;

    return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
}

$results = [];
for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
    $tmpName = $uploadedFiles['tmp_name'][$i];
    $originalName = basename($uploadedFiles['name'][$i]);

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmpName);

    if (!in_array($mime, $allowed_types)) {
        $results[] = ['file' => $originalName, 'status' => 'Unsupported file format'];
        continue;
    }

    $targetPath = $uploadDir . '/' . uniqid() . '-' . $originalName;

    if (getimagesize($tmpName)) {
        if (move_uploaded_file($tmpName, $targetPath)) {
            $exif = @exif_read_data($targetPath);
            $latitude = $longitude = $datetime = $make = $model = null;

            if ($exif) {
                if (isset($exif['GPSLatitude'], $exif['GPSLatitudeRef'], $exif['GPSLongitude'], $exif['GPSLongitudeRef'])) {
                    $latitude = getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
                    $longitude = getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
                }

                $datetime = $exif['DateTimeOriginal'] ?? null;
                $make = $exif['Make'] ?? null;
                $model = $exif['Model'] ?? null;

                dbQuery("INSERT INTO images (gallery_id, filename, latitude, longitude, device_maker, device_model, taken_at) VALUES (:gallery_id, :filename, :latitude, :longitude, :device_maker, :device_model, :taken_at)", [
                    ':gallery_id' => $galleryId,
                    ':filename' => $targetPath,
                    ':latitude' => $latitude,
                    ':longitude' => $longitude,
                    ':device_maker' => $make,
                    ':device_model' => $model,
                    ':taken_at' => $datetime
                ]);
                $results[] = ['file' => $originalName, 'status' => 'Uploaded'];
        } 
        else {
            $results[] = ['file' => $originalName, 'status' => 'Failed'];
        }
    } 
        else {
            $results[] = ['file' => $originalName, 'status' => 'Invalid image file'];
        }
    }
}

echo json_encode(['status' => 'done', 'results' => $results]);

?>