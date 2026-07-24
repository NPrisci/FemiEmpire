<?php
// diagnostic.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fonction de log
function logMessage($msg) {
    error_log($msg);
    echo $msg . "\n";
}

echo "<pre>";
echo "=== DIAGNOSTIC COMPLET ===\n\n";

// 1. Vérifier PHP
echo "1. VERSION PHP : " . phpversion() . "\n\n";

// 2. Vérifier les extensions
echo "2. EXTENSIONS :\n";
$extensions = ['pdo_mysql', 'mysql', 'mysqli'];
foreach ($extensions as $ext) {
    echo "   " . ($ext ? '✅' : '❌') . " $ext : " . (extension_loaded($ext) ? 'chargée' : 'non chargée') . "\n";
}
echo "\n";

// 3. Variables d'environnement
echo "3. VARIABLES D'ENVIRONNEMENT :\n";
$vars = ['MYSQLHOST', 'MYSQLPORT', 'MYSQLDATABASE', 'MYSQLUSER', 'MYSQLPASSWORD'];
foreach ($vars as $var) {
    $val = getenv($var);
    if ($var === 'MYSQLPASSWORD') {
        $val = $val ? '***DEFINI***' : 'NON DEFINI';
    }
    echo "   $var : " . ($val ?: 'NON DEFINI') . "\n";
}
echo "\n";

// 4. Test inclusion config
echo "4. TEST INCLUSION CONFIG :\n";
$configFile = __DIR__ . '/config/database.php';
if (file_exists($configFile)) {
    echo "   ✅ Fichier config trouvé : $configFile\n";
    try {
        require_once $configFile;
        echo "   ✅ Inclusion réussie\n";
    } catch (Exception $e) {
        echo "   ❌ Erreur inclusion : " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ Fichier config non trouvé : $configFile\n";
}
echo "\n";

// 5. Test connexion
echo "5. TEST CONNEXION :\n";
try {
    if (function_exists('getDB')) {
        $pdo = getDB();
        echo "   ✅ Connexion réussie\n";
        
        // Test requête
        $result = $pdo->query("SELECT 1");
        echo "   ✅ Requête test réussie\n";
        
        // Afficher les tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "   📊 Tables : " . (count($tables) > 0 ? implode(', ', $tables) : 'AUCUNE') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur : " . $e->getMessage() . "\n";
    echo "   Trace : " . $e->getTraceAsString() . "\n";
}
echo "\n";

// 6. Fichiers présents
echo "6. FICHIERS :\n";
$files = [
    'config/database.php',
    'init_db.php',
    'database.sql',
    'public/index.php',
    'Procfile'
];
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    echo "   " . (file_exists($path) ? '✅' : '❌') . " $file\n";
}
echo "\n";

echo "=== FIN DIAGNOSTIC ===\n";
echo "</pre>";
?>