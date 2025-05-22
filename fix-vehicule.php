<?php
// fix-vehicule.php
require_once 'connexion.php';

try {
    // Ajouter la colonne immatriculation
    $pdo->exec("ALTER TABLE vehicule ADD COLUMN immatriculation VARCHAR(20)");
    echo "Colonne immatriculation ajoutée avec succès !<br>";
    echo "<a href='/ajouter-vehicule.php'>Retour à ajouter véhicule</a>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "La colonne immatriculation existe déjà !<br>";
        echo "<a href='/ajouter-vehicule.php'>Retour à ajouter véhicule</a>";
    } else {
        echo "Erreur : " . $e->getMessage();
    }
}
?>