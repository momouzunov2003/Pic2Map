<?php
require_once __DIR__ . '/src/db.php';
require_once __DIR__ . '/config.php';

$stmt = dbQuery("
SELECT slug
FROM galleries
WHERE EXISTS (
    SELECT images.id
    FROM images
    WHERE images.gallery_id = galleries.id
)
ORDER BY RANDOM()
LIMIT 1;

");
$gallery = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$gallery) {
    header('Location: ' . APP_ROOT . '?toast=nogalleries');
}
else {
    $slug = $gallery['slug'];
    header('Location: '. APP_ROOT . '/gallery/' . $slug);
}
exit;
?>