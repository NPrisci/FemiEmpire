<?php
// public/index.php - Page de test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Railway</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Test Railway - PHP fonctionne !</h1>
        <div class='info'>
            <p><strong>PHP Version:</strong> " . phpversion() . "</p>
            <p><strong>Extensions:</strong> " . implode(', ', get_loaded_extensions()) . "</p>
        </div>";

// Test de connexion
try {
    if (file_exists(__DIR__ . '/../config/database.php')) {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDB();
        echo "<p class='success'>✅ Connexion à MySQL réussie !</p>";
        
        // Afficher les tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        if (count($tables) > 0) {
            echo "<h3>📊 Tables:</h3><ul>";
            foreach ($tables as $table) {
                $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "<li><strong>$table</strong> : $count enregistrements</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>⚠️ Aucune table trouvée</p>";
        }
    } else {
        echo "<p class='error'>❌ Fichier config/database.php non trouvé</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . $e->getMessage() . "</p>";
}

echo "
        <div class='info'>
            <h3>📁 Fichiers présents:</h3>
            <ul>";
$files = ['config/database.php', 'init_db.php', 'database.sql', 'Dockerfile'];
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/../' . $file);
    echo "<li>" . ($exists ? '✅' : '❌') . " $file</li>";
}
echo "      </ul>
        </div>
    </div>
</body>
</html>";
?>