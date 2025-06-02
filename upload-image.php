<?php
require_once __DIR__ . '/src/db.php';

define ('MAX_FILE_SIZE', 15 * 1024 * 1024); // 15MB

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
if (!$gallery) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'gallery not found']);
    exit;
}
$galleryId = $gallery['id'];

$uploadDir = __DIR__ . '/public/uploads/' . $slug;
$webDir = rtrim(dirname(preg_replace('#/+#', '/', $_SERVER['SCRIPT_NAME'])), '/') . '/public/uploads/';

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

function createWebPThumbnail(string $sourcePath, string $destinationPath, int $thumbWidth = 200): bool
{
    try {
        if (!extension_loaded('imagick')) {
            throw new Exception('Imagick extension is not available.');
        }

        $image = new Imagick();
        $image->readImage($sourcePath . '[0]');
        $image->setImageFormat('webp');
        $image->thumbnailImage($thumbWidth, 0);

        if (!$image->writeImage($destinationPath)) {
            throw new Exception('Failed to write WebP thumbnail.');
        }

        $image->clear();
        $image->destroy();

        return true;

    } catch (Exception $e) {
        error_log('Thumbnail creation failed: ' . $e->getMessage());
        return false;
    }
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

    if ($uploadedFiles['size'][$i] > MAX_FILE_SIZE) {
        $results[] = ['file' => $originalName, 'status' => 'File too large. Max allowed is ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB'];
        continue;
    }

    $newFileName = uniqid() . '-' . $originalName;
    $targetPath = $uploadDir . '/' . $newFileName;
    $targetWebPath = $webDir . $slug . '/' . $newFileName;

    $newThumbnailFileNameWebp = 'thumb-' . $newFileName . '.webp';
    $thumbnailPath = $uploadDir . '/' . $newThumbnailFileNameWebp;
    $thumbnailWebPath = $webDir . $slug . '/' . $newThumbnailFileNameWebp;
    if (!createWebPThumbnail($tmpName, $thumbnailPath)) {
        $results[] = ['file' => $originalName, 'status' => 'Failed to create thumbnail'];
        continue;
    }

    if (getimagesize($tmpName)) {
        if (move_uploaded_file($tmpName, $targetPath)) {
            $exif = @exif_read_data($targetPath);
            $latitude = $longitude = $datetime = $make = $model = null;

            if ($exif) {
                if (isset($exif['GPSLatitude'], $exif['GPSLatitudeRef'], $exif['GPSLongitude'], $exif['GPSLongitudeRef'])) {
                    $latitude = getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
                    $longitude = getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
                }
            }

            $datetime = $exif['DateTimeOriginal'] ?? null;
            $make = $exif['Make'] ?? null;
            $model = $exif['Model'] ?? null;

            dbQuery("INSERT INTO images (gallery_id, url, thumbnail_url, latitude, longitude, device_maker, device_model, taken_at) VALUES (:gallery_id, :url, :thumbnail_url, :latitude, :longitude, :device_maker, :device_model, :taken_at)", [
                ':gallery_id' => $galleryId,
                ':url' => $targetWebPath,
                ':thumbnail_url' => $thumbnailWebPath,
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

echo json_encode(['status' => 'done', 'results' => $results]);

?>