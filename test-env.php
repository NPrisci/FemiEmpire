<?php
// public/check.php
require_once __DIR__ . '/../config/database.php';

echo "<h1>🔍 Diagnostic de la base de données</h1>";

try {
    $pdo = getDB();
    echo "<p style='color:green'>✅ Connexion réussie</p>";
    
    // Afficher les tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<h2>📊 Tables trouvées :</h2>";
        echo "<ul>";
        foreach ($tables as $table) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<li><strong>$table</strong> : $count enregistrements</li>";
        }
        echo "</ul>";
        echo "<p style='color:green'>✅ Tout fonctionne correctement !</p>";
    } else {
        echo "<p style='color:red'>❌ Aucune table trouvée</p>";
        echo "<p><a href='/init_db.php'>Cliquez ici pour initialiser la base</a></p>";
    }
    
    // Afficher la configuration
    echo "<h2>⚙️ Configuration</h2>";
    echo "<ul>";
    echo "<li>Hôte : " . DB_HOST . "</li>";
    echo "<li>Port : " . DB_PORT . "</li>";
    echo "<li>Base : " . DB_NAME . "</li>";
    echo "<li>Utilisateur : " . DB_USER . "</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Erreur : " . $e->getMessage() . "</p>";
}
?>