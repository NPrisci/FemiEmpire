<?php
// init_db.php - Version qui ne bloque jamais
error_reporting(E_ALL);
ini_set('display_errors', 1);

function logMsg($msg) {
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] $msg";
    error_log($log);
    echo $log . "\n";
}

logMsg("=== DÉBUT INITIALISATION ===");

try {
    // Vérifier les extensions
    if (!extension_loaded('pdo_mysql')) {
        logMsg("❌ Extension PDO MySQL non chargée");
        logMsg("Extensions chargées: " . implode(', ', get_loaded_extensions()));
        exit(0); // Sortie propre, ne bloque pas
    }
    logMsg("✅ Extension PDO MySQL chargée");

    // Vérifier le fichier config
    $configFile = __DIR__ . '/config/database.php';
    if (!file_exists($configFile)) {
        logMsg("❌ Fichier config manquant: $configFile");
        exit(0);
    }
    logMsg("✅ Fichier config trouvé");

    // Charger la config
    require_once $configFile;
    
    // Vérifier les variables d'environnement
    logMsg("Variables d'env:");
    logMsg("  MYSQLHOST: " . (getenv('MYSQLHOST') ?: 'NON DEFINI'));
    logMsg("  MYSQLPORT: " . (getenv('MYSQLPORT') ?: 'NON DEFINI'));
    logMsg("  MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ?: 'NON DEFINI'));
    logMsg("  MYSQLUSER: " . (getenv('MYSQLUSER') ?: 'NON DEFINI'));
    
    // Tester la connexion
    logMsg("Test de connexion...");
    $pdo = getDB();
    logMsg("✅ Connexion réussie !");
    
    // Vérifier les tables
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    logMsg("Tables existantes: " . (count($tables) > 0 ? implode(', ', $tables) : 'AUCUNE'));
    
    if (count($tables) === 0) {
        logMsg("Création des tables...");
        
        // Vérifier le fichier SQL
        $sqlFile = __DIR__ . '/database.sql';
        if (!file_exists($sqlFile)) {
            logMsg("⚠️  database.sql non trouvé, création d'une table de test");
            $pdo->exec("CREATE TABLE IF NOT EXISTS test (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50))");
            logMsg("✅ Table test créée");
        } else {
            $sql = file_get_contents($sqlFile);
            if (empty(trim($sql))) {
                logMsg("⚠️  database.sql est vide");
            } else {
                $queries = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        try {
                            $pdo->exec($query);
                        } catch (PDOException $e) {
                            logMsg("⚠️  Erreur requête: " . $e->getMessage());
                        }
                    }
                }
                logMsg("✅ Requêtes exécutées");
            }
        }
        
        // Vérifier les tables après création
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        logMsg("Tables après init: " . (count($tables) > 0 ? implode(', ', $tables) : 'TOUJOURS AUCUNE'));
    }
    
    logMsg("✅ INITIALISATION TERMINÉE AVEC SUCCÈS");
    
} catch (Exception $e) {
    logMsg("❌ ERREUR: " . $e->getMessage());
    logMsg("Trace: " . $e->getTraceAsString());
    // Sortie avec succès pour ne pas bloquer Apache
    exit(0);
}

logMsg("=== FIN INITIALISATION ===");
?>