<?php
// The root directory of the application relative to the web server root
define('APP_ROOT', '/pic2map');
// The maximum file size for uploads, each file must not exceed this limit
define ('MAX_FILE_SIZE', 15 * 1024 * 1024); // 15MB
// The type of images allowed for upload
$allowed_types = ['image/jpeg', 'image/png', 'image/tiff', 'image/webp'];
// Email addresses of the administrators to receive contact form submissions
define('ADMIN_EMAILS', 'georgi.iliev533@outlook.com, uzunovvv03@gmail.com');

// Database type - sqlite or mysql
define('DB_TYPE', 'mysql'); 
// Database connection details
define('DB_HOST', 'localhost');
define('DB_NAME', 'pic2map');
define('DB_USER', 'root');
define('DB_PASS', 'password');
?>