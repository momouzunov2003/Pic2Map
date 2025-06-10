<?php
require_once __DIR__ . '/../config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// For SQLite connection
function getDbConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dbPath = __DIR__ . '/../db/database.sqlite';

        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0755, true);
        }

        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        initializeDatabase($pdo);
    }

    return $pdo;
}

// For MariaDB connection
// function getDbConnection(): PDO {
//     static $pdo = null;

//     if ($pdo === null) {
//         $host = DB_HOST;       // Change if needed
//         $dbname = DB_NAME; // Replace with your actual database name
//         $username = DB_USER;   // Replace with your MariaDB username
//         $password = DB_PASS;   // Replace with your MariaDB password
//         $charset = 'utf8mb4';

//         $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

//         $options = [
//             PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//             PDO::ATTR_EMULATE_PREPARES   => false,
//         ];

//         try {
//             $pdo = new PDO($dsn, $username, $password, $options);
//         } catch (PDOException $e) {
//             throw new RuntimeException('Database connection failed: ' . $e->getMessage());
//         }
//     }

//     return $pdo;
// }

function initializeDatabase(PDO $pdo): void {
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='galleries'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("
            CREATE TABLE galleries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT NOT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
	    ");

        $pdo->exec("
            CREATE TABLE images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            gallery_id TEXT NOT NULL,
            url TEXT NOT NULL,
            thumbnail_url TEXT NOT NULL,
            latitude REAL,
            longitude REAL,
            device_maker TEXT,
            device_model TEXT,
            taken_at TEXT,
            uploaded_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (gallery_id) REFERENCES galleries(id)
            )
	    ");
    }
}

function dbQuery(string $sql, array $params = []): PDOStatement {
    $stmt = getDbConnection()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

?>