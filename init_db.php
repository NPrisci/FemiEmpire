<?php
// init_db.php
require_once __DIR__ . '/config/database.php';

echo "=== INITIALISATION DE LA BASE DE DONNÉES ===\n";

try {
    $pdo = getDB();
    echo "✅ Connexion réussie à la base : " . DB_NAME . "\n";
    
    // Vérifier si le fichier SQL existe
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("❌ Fichier database.sql non trouvé dans " . __DIR__ . "\n");
    }
    
    // Lire le fichier SQL
    $sql = file_get_contents($sqlFile);
    if (empty($sql)) {
        die("❌ Le fichier database.sql est vide\n");
    }
    
    // Exécuter chaque requête séparément
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                $count++;
                echo "✅ Requête $count exécutée\n";
            } catch (PDOException $e) {
                echo "⚠️  Erreur sur une requête : " . $e->getMessage() . "\n";
                // Continue quand même
            }
        }
    }
    
    echo "✅ $count requêtes exécutées\n";
    
    // Vérification finale
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Tables trouvées : " . (count($tables) > 0 ? implode(', ', $tables) : 'AUCUNE') . "\n";
    
    if (count($tables) > 0) {
        echo "✅ Initialisation terminée avec succès !\n";
    } else {
        echo "⚠️  Aucune table créée. Vérifiez votre fichier database.sql\n";
    }
    
} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage() . "\n");
}
?>