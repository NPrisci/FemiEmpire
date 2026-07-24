<?php
// check_extensions.php
echo "=== VÉRIFICATION DES EXTENSIONS PHP ===\n\n";

$extensions = [
    'pdo' => 'PDO de base',
    'pdo_mysql' => 'PDO MySQL',
    'mysqli' => 'MySQLi',
    'mysql' => 'MySQL (ancien)'
];

foreach ($extensions as $ext => $name) {
    $loaded = extension_loaded($ext);
    echo ($loaded ? '✅' : '❌') . " $ext ($name) : " . ($loaded ? 'chargée' : 'NON CHARGÉE') . "\n";
}

echo "\n";

// Afficher les extensions chargées
echo "Extensions chargées :\n";
$loaded_extensions = get_loaded_extensions();
foreach ($loaded_extensions as $ext) {
    if (strpos($ext, 'mysql') !== false || strpos($ext, 'pdo') !== false) {
        echo "  - $ext\n";
    }
}

echo "\n";

// Tester la connexion
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL est chargé, test de connexion...\n";
    try {
        require_once 'config/database.php';
        $pdo = getDB();
        echo "✅ Connexion réussie !\n";
    } catch (Exception $e) {
        echo "❌ Erreur : " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ PDO MySQL n'est pas chargé. Impossible de se connecter.\n";
    echo "\nSOLUTIONS :\n";
    echo "1. Créez un fichier .user.ini avec : extension=pdo_mysql.so\n";
    echo "2. Ou utilisez un Dockerfile avec : docker-php-ext-install pdo_mysql\n";
    echo "3. Ou configurez Nixpacks avec php83Extensions.pdo_mysql\n";
}
?>