<?php
// config/database.php - Version avec MySQLi

// Détecter si PDO est disponible
$usePDO = extension_loaded('pdo_mysql');

if ($usePDO) {
    // Utiliser PDO
    define('DB_HOST', getenv('MYSQLHOST') ?: 'mysql.railway.internal');
    define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
    define('DB_NAME', getenv('MYSQLDATABASE') ?: 'railway');
    define('DB_USER', getenv('MYSQLUSER') ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: 'DNQCjmIpaPXUPjaZuHDoStFNmlQrixeb');
    define('DB_CHARSET', 'utf8mb4');

    function getDB(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Erreur PDO: " . $e->getMessage());
            }
        }
        return $pdo;
    }
} else {
    // Utiliser MySQLi (fallback)
    define('DB_HOST', getenv('MYSQLHOST') ?: 'mysql.railway.internal');
    define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
    define('DB_NAME', getenv('MYSQLDATABASE') ?: 'railway');
    define('DB_USER', getenv('MYSQLUSER') ?: 'root');
    define('DB_PASS', getenv('MYSQLPASSWORD') ?: 'DNQCjmIpaPXUPjaZuHDoStFNmlQrixeb');

    function getDB() {
        static $mysqli = null;
        if ($mysqli === null) {
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            if ($mysqli->connect_error) {
                die("Erreur MySQLi: " . $mysqli->connect_error);
            }
        }
        return $mysqli;
    }
}
?>