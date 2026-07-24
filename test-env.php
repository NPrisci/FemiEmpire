<?php
// test-simple.php - Version ultra simple
echo "Test PHP fonctionne !\n";
echo "PHP Version : " . phpversion() . "\n";
echo "Extensions : " . implode(', ', get_loaded_extensions()) . "\n";
?>