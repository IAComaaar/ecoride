<?php
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Début du diagnostic...<br>";
flush();

echo "Vérification de la variable d'environnement JAWSDB_COBALT_URL...<br>";
$jawsdb_url = getenv('JAWSDB_COBALT_URL');
if (!$jawsdb_url) {
    echo "ERREUR: Variable JAWSDB_COBALT_URL non trouvée.<br>";
    echo "Variables disponibles : <pre>";
    print_r($_ENV);
    echo "</pre>";
    exit;
}

echo "URL JawsDB trouvée.<br>";
flush();

$dbparts = parse_url($jawsdb_url);
$hostname = $dbparts['host'];
$username = $dbparts['user'];
$password = $dbparts['pass'];
$database = ltrim($dbparts['path'], '/');

echo "Tentative de connexion à MySQL...<br>";
flush();

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion à la base de données réussie!<br>";

    echo "Liste des tables :<br>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";

    if (in_array('utilisateur', $tables)) {
        echo "Structure de la table utilisateur :<br>";
        $columns = $pdo->query("DESCRIBE utilisateur")->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "La table 'utilisateur' n'existe pas.<br>";
    }
    
} catch (PDOException $e) {
    echo "ERREUR: Connexion à la base de données échouée : " . $e->getMessage() . "<br>";
}

echo "Diagnostic terminé.";
?>