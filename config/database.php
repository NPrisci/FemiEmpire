<?php
// config/database.php

define('DB_HOST', getenv('MYSQLHOST'));
define('DB_PORT', getenv('MYSQLPORT'));
define('DB_NAME', getenv('MYSQLDATABASE'));
define('DB_USER', getenv('MYSQLUSER'));
define('DB_PASS', getenv('MYSQLPASSWORD'));
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST .
               ";port=" . DB_PORT .
               ";dbname=" . DB_NAME .
               ";charset=" . DB_CHARSET;

        try {
            $pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            
            // === AJOUT : Vérification automatique des tables ===
            checkAndCreateTables($pdo);
            
        } catch (PDOException $e) {
            die("Erreur MySQL : " . $e->getMessage());
        }
    }

    return $pdo;
}

// Nouvelle fonction pour vérifier et créer les tables
function checkAndCreateTables($pdo) {
    try {
        // Vérifier si la table users existe (ou votre table principale)
        $result = $pdo->query("SHOW TABLES LIKE 'users'");
        
        if ($result->rowCount() === 0) {
            // Les tables n'existent pas, on les crée
            $sqlFile = __DIR__ . '/../database.sql';
            
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $queries = array_filter(array_map('trim', explode(';', $sql)));
                
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        try {
                            $pdo->exec($query);
                        } catch (PDOException $e) {
                            // Log l'erreur mais continue
                            error_log("Erreur d'initialisation : " . $e->getMessage());
                        }
                    }
                }
                
                error_log("✅ Tables initialisées automatiquement");
            } else {
                error_log("⚠️  Fichier database.sql manquant");
            }
        }
    } catch (PDOException $e) {
        error_log("Erreur de vérification des tables : " . $e->getMessage());
    }
}
?>