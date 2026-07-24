<?php
// init_db.php - Version NON BLOQUANTE
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log dans les logs Apache
function logMessage($msg) {
    error_log("[INIT] " . $msg);
    echo $msg . "\n";
}

logMessage("=== DÉBUT INITIALISATION ===");

try {
    // Vérifier si le fichier config existe
    if (!file_exists(__DIR__ . '/config/database.php')) {
        logMessage("❌ Fichier config/database.php manquant");
        exit(1);
    }
    
    require_once __DIR__ . '/config/database.php';
    
    // Vérifier si les extensions sont chargées
    if (!extension_loaded('pdo_mysql')) {
        logMessage("❌ Extension PDO MySQL non chargée");
        exit(1);
    }
    
    logMessage("✅ Extension PDO MySQL chargée");
    
    $pdo = getDB();
    logMessage("✅ Connexion réussie à la base");
    
    // Vérifier si les tables existent déjà
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        logMessage("✅ Tables déjà existantes : " . implode(', ', $tables));
        exit(0); // Sortie propre
    }
    
    // Créer les tables
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        logMessage("⚠️  Fichier database.sql manquant, création d'une structure minimale");
        createMinimalStructure($pdo);
        exit(0);
    }
    
    $sql = file_get_contents($sqlFile);
    if (empty(trim($sql))) {
        logMessage("⚠️  Fichier database.sql vide, création d'une structure minimale");
        createMinimalStructure($pdo);
        exit(0);
    }
    
    // Exécuter le SQL
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                $count++;
            } catch (PDOException $e) {
                logMessage("⚠️  Erreur sur une requête : " . $e->getMessage());
            }
        }
    }
    
    logMessage("✅ $count requêtes exécutées");
    
    // Vérification finale
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    logMessage("✅ Tables créées : " . implode(', ', $tables));
    
} catch (Exception $e) {
    logMessage("❌ Erreur : " . $e->getMessage());
    logMessage("Trace : " . $e->getTraceAsString());
    // Sortie avec succès quand même pour que Apache démarre
    exit(0);
}

function createMinimalStructure($pdo) {
    logMessage("Création d'une structure minimale...");
    
    $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ";
    
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }
    
    logMessage("✅ Structure minimale créée");
}

logMessage("=== INITIALISATION TERMINÉE ===");
?>