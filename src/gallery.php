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

    return null;
}
 
?>