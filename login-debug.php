<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Contenu du fichier login.php</h1>";
echo "<pre>";
if (file_exists('login.php')) {
    echo htmlspecialchars(file_get_contents('login.php'));
} else {
    echo "Le fichier login.php n'existe pas.";
}
echo "</pre>";
?>