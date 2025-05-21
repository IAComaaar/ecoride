<?php
require_once 'connexion.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Création de la table 'participation'</h1>";

try {
    // Vérifier si la table existe déjà
    $result = $pdo->query("SHOW TABLES LIKE 'participation'");
    $exists = ($result->rowCount() > 0);
    
    if (!$exists) {
        // Créer la table participation
        $sql = "CREATE TABLE participation (
            id_participation INT AUTO_INCREMENT PRIMARY KEY,
            id_covoiturage INT,
            id_user INT,
            date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
            statut ENUM('en_attente', 'confirmee', 'annulee') DEFAULT 'en_attente',
            FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage) ON DELETE CASCADE,
            FOREIGN KEY (id_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE
        )";
        
        $pdo->exec($sql);
        echo "<p style='color:green'>Table 'participation' créée avec succès.</p>";
    } else {
        echo "<p>La table 'participation' existe déjà.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur : " . $e->getMessage() . "</p>";
}

echo "<p><a href='/mon-espace.php'>Retourner à mon espace</a></p>";
?>