<?php
// config/database.php - Version avec vérification des extensions

// Vérifier que les extensions sont chargées
if (!extension_loaded('pdo_mysql')) {
    die("❌ L'extension PDO MySQL n'est pas chargée. Veuillez ajouter 'extension=pdo_mysql.so' dans php.ini");
}

define('DB_HOST', getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'railway');
define('DB_USER', getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        // Vérification supplémentaire
        if (!extension_loaded('pdo_mysql')) {
            throw new Exception("PDO MySQL extension is not loaded");
        }

        try {
            $dsn = "mysql:host=" . DB_HOST .
                   ";port=" . DB_PORT .
                   ";dbname=" . DB_NAME .
                   ";charset=" . DB_CHARSET;

            $pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5
                ]
            );

        } catch (PDOException $e) {
            die("Erreur MySQL : " . $e->getMessage());
        }
    }

    return $pdo;
}
?>