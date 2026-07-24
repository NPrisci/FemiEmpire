<?php
// config/database.php

// Tenter de charger l'extension dynamiquement
if (!extension_loaded('pdo_mysql')) {
    // Essayer de charger l'extension
    if (function_exists('dl')) {
        @dl('pdo_mysql.so');
        @dl('php_pdo_mysql.dll'); // Pour Windows
    }
}

// Si toujours pas chargée, donner un message clair
if (!extension_loaded('pdo_mysql')) {
    die("❌ L'extension PDO MySQL n'est pas chargée.<br>
    Veuillez ajouter 'extension=pdo_mysql.so' dans votre php.ini<br>
    <br>
    <strong>Solutions :</strong><br>
    1. Créez un fichier .user.ini avec extension=pdo_mysql.so<br>
    2. Utilisez un Dockerfile<br>
    3. Contactez le support de Railway pour activer PDO MySQL");
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
                    PDO::ATTR_TIMEOUT => 10
                ]
            );

        } catch (PDOException $e) {
            die("Erreur MySQL : " . $e->getMessage());
        }
    }

    return $pdo;
}
?>