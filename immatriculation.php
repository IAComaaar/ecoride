<?php
require_once 'connexion.php';

try {
    $pdo->exec("ALTER TABLE vehicule ADD COLUMN immatriculation VARCHAR(20)");
    echo "Colonne immatriculation ajoutée avec succès!";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>