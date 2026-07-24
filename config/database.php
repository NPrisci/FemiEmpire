<?php
// config/database.php - Version avec chargement dynamique

// Tenter de charger l'extension manuellement
if (!extension_loaded('pdo_mysql')) {
    // Essayer différents chemins
    $possible_paths = [
        '/usr/lib/php/20230831/pdo_mysql.so',
        '/usr/lib/php/20220829/pdo_mysql.so',
        '/usr/lib/php/20210902/pdo_mysql.so',
        '/usr/lib/php/20200930/pdo_mysql.so',
        '/usr/local/lib/php/extensions/no-debug-non-zts-20230831/pdo_mysql.so',
        '/usr/local/lib/php/extensions/no-debug-non-zts-20220829/pdo_mysql.so',
        '/usr/local/lib/php/extensions/no-debug-non-zts-20210902/pdo_mysql.so',
        '/usr/local/lib/php/extensions/no-debug-non-zts-20200930/pdo_mysql.so',
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            @dl($path);
            if (extension_loaded('pdo_mysql')) {
                break;
            }
        }
    }
}

// Si toujours pas chargée, donner un message d'erreur
if (!extension_loaded('pdo_mysql')) {
    die("❌ L'extension PDO MySQL n'est pas chargée.<br>
    <br>
    <strong>Solutions :</strong><br>
    1. Ajoutez un fichier Dockerfile à la racine<br>
    2. Ou ajoutez un fichier nixpacks.toml<br>
    3. Contactez le support Railway pour activer PDO MySQL<br>
    <br>
    <strong>Extensions actuellement chargées :</strong><br>
    " . implode(', ', get_loaded_extensions()));
}

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
            $dsn = "mysql:host=" . DB_HOST .
                   ";port=" . DB_PORT .
                   ";dbname=" . DB_NAME .
                   ";charset=" . DB_CHARSET;

            error_log("Tentative de connexion à: " . DB_HOST . ":" . DB_PORT);

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

            error_log("✅ Connexion MySQL réussie !");

        } catch (PDOException $e) {
            error_log("❌ Erreur MySQL: " . $e->getMessage());
            die("Erreur MySQL : " . $e->getMessage());
        }
    }

    return $pdo;
}
?>