<?php
// The root directory of the application relative to the web server root
define('APP_ROOT', '/pic2map');
// The maximum file size for uploads, each file must not exceed this limit
define ('MAX_FILE_SIZE', 15 * 1024 * 1024); // 15MB
// The type of images allowed for upload
$allowed_types = ['image/jpeg', 'image/png', 'image/tiff', 'image/webp'];

?>