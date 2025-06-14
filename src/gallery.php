<?php
require_once __DIR__ . '/db.php';

function createGallery(): string {
    $slug = bin2hex(random_bytes(4));
    dbQuery("INSERT INTO galleries (slug) VALUES (:slug)", [
        ':slug' => $slug
    ]);

    $dir = __DIR__ . '/../public/uploads/' . $slug;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $slug;
}

function galleryExists(string $slug): bool {
    $stmt = dbQuery("SELECT COUNT(*) FROM galleries WHERE slug = :slug", [
        ':slug' => $slug
    ]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}

function getGallery(string $slug): ?array {
    $stmt = dbQuery("SELECT id FROM galleries WHERE slug = :slug", [
        ':slug' => $slug
    ]);

    $gallery = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gallery) {
        $stmt = dbQuery("SELECT * FROM images WHERE gallery_id = :gallery_id", [
            ':gallery_id' => $gallery['id']
        ]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $images;
    }

    return [];
}
 
function deletePhoto(): void {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No image ID provided']);
        exit;
    }

    $id = (int)$data['id'];

    $stmt = dbQuery("SELECT url, thumbnail_url, gallery_id FROM images WHERE id = :id", [':id' => $id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    $galleryDeleted = false;

    if ($image) {
        $mainPath = dirname(dirname(__DIR__)) . parse_url($image['url'], PHP_URL_PATH);
        $thumbPath = dirname(dirname(__DIR__)) . parse_url($image['thumbnail_url'], PHP_URL_PATH);

        if (file_exists($mainPath)) {
            unlink($mainPath);
        }
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }

        $galleryId = $image['gallery_id'];

        dbQuery("DELETE FROM images WHERE id = :id", [':id' => $id]);

        if ($galleryId) {
            $stmt = dbQuery("SELECT slug FROM galleries WHERE id = :id", [':id' => $galleryId]);
            $gallery = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($gallery) {
                $stmt = dbQuery("SELECT COUNT(*) as cnt FROM images WHERE gallery_id = :gallery_id", [':gallery_id' => $galleryId]);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;

                if ($count == 0) {
                    dbQuery("DELETE FROM galleries WHERE id = :id", [':id' => $galleryId]);
                    
                    $galleryDir = dirname(__DIR__) . '/public/uploads/' . $gallery['slug'];
                    if (is_dir($galleryDir)) {
                        array_map('unlink', glob("$galleryDir/*"));
                        rmdir($galleryDir);
                    }
                    $galleryDeleted = true;
                }
            }
        }
    }

    echo json_encode(['success' => true, 'galleryDeleted' => $galleryDeleted]);
}

function downloadGallery(string $slug): void {
    $images = getGallery($slug);

    $tmpFile = sys_get_temp_dir() . '/gallery_' . uniqid() . '.zip';
    $zip = new ZipArchive();

    if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        exit("Cannot open <$tmpFile>\n");
    }

    foreach ($images as $img) {
        $filePath = dirname(dirname(__DIR__ )) . parse_url($img['url'], PHP_URL_PATH);
        
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($filePath));
        } else {
            error_log("Missing image file: $filePath");
        }
    }

    if (!$zip->close()) {
        exit("Failed to finalize ZIP file");
    }

    clearstatcache();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="gallery_' . $slug . '.zip"');
    header('Content-Length: ' . filesize($tmpFile));

    readfile($tmpFile);
    unlink($tmpFile);
}
?>