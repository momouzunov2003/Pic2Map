<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
            filename TEXT NOT NULL,
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

function dbQuery(string $sql, array $params = []): PDOStatement
{
    $stmt = getDbConnection()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

?>