<?php
require_once 'connexion.php';

try {
    $pdo->exec("ALTER TABLE covoiturage ADD COLUMN etat VARCHAR(50) DEFAULT 'non démarré'");
    echo "Colonne etat ajoutée avec succès !<br>";
    echo "<a href='/mon-espace.php'>Retour à mon espace</a>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "La colonne etat existe déjà !<br>";
        echo "<a href='/mon-espace.php'>Retour à mon espace</a>";
    } else {
        echo "Erreur : " . $e->getMessage();
    }
}
?>