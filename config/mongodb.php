<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Détection de l'environnement
    if (getenv('MONGODB_URI')) {
        // Production
        $mongoUri = getenv('MONGODB_URI');
    } else {
        // Local
        $mongoUri = 'mongodb://localhost:27017';
    }
    
    $mongoClient = new MongoDB\Client($mongoUri);
    $mongodb = $mongoClient->ecoride;
    
    // Test connexion
    $mongodb->command(['ping' => 1]);
    
} catch (Exception $e) {
    die("Erreur connexion MongoDB : " . $e->getMessage());
}

// Export pour utilisation dans d'autres fichiers
return $mongodb;