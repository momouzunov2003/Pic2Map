<?php

require_once __DIR__ . '/src/db.php';
define('APP_ROOT', '/pic2map');

$stmt = dbQuery("SELECT slug FROM galleries ORDER BY RANDOM() LIMIT 1");
$gallery = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$gallery) {
    header('Redirect: /');
}
else {
    $slug = $gallery['slug'];
    header('Location: '. APP_ROOT . '/gallery/' . $slug);
}
exit;
?>