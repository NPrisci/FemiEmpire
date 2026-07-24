<?php
// ================================================
// config/database.php
// Connexion PostgreSQL (Render)
// ================================================

define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASSWORD'));
define('DB_PORT', getenv('DB_PORT') ?: 5432);

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {

        $dsn = "pgsql:host=" . DB_HOST .
               ";port=" . DB_PORT .
               ";dbname=" . DB_NAME;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }

    return $pdo;
}