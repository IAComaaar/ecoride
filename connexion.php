<?php
if (getenv('JAWSDB_COBALT_URL')) {
    // Configuration Heroku avec JawsDB
    $dbUrl = parse_url(getenv('JAWSDB_COBALT_URL'));
    
    $dbHost = $dbUrl['host'];
    $dbUser = $dbUrl['user'];
    $dbPassword = $dbUrl['pass'];
    $dbName = ltrim($dbUrl['path'], '/');
    
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données Heroku : " . $e->getMessage());
    }
} else {
    // Configuration locale (XAMPP)
    $dbHost = 'localhost';
    $dbUser = 'root';  // À adapter selon votre configuration locale
    $dbPassword = '';  // À adapter selon votre configuration locale
    $dbName = 'ecoride';  // À adapter selon votre configuration locale
    
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données locale : " . $e->getMessage());
    }
}
?>