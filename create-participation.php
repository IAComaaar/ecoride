<?php
// create-participation.php
require_once 'connexion.php';

try {
    // Créer la table participation
    $sql = "CREATE TABLE IF NOT EXISTS participation (
        id_participation INT AUTO_INCREMENT PRIMARY KEY,
        id_covoiturage INT,
        id_user INT,
        status VARCHAR(50) DEFAULT 'confirmé',
        date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
        message TEXT,
        note_chauffeur INT,
        note_passager INT,
        commentaire_chauffeur TEXT,
        commentaire_passager TEXT,
        date_annulation DATETIME,
        motif_annulation TEXT,
        FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage) ON DELETE CASCADE,
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "✅ Table participation créée avec succès !<br>";
    echo "<a href='/voir.php'>Retour à voir.php</a>";
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>