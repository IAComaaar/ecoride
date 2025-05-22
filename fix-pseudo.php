<?php
// fix-pseudo.php
require_once 'connexion.php';

try {
    // Ajouter la colonne pseudo
    $pdo->exec("ALTER TABLE utilisateur ADD COLUMN pseudo VARCHAR(50)");
    echo "Colonne pseudo ajoutée avec succès !<br>";
    echo "<a href='/voir.php'>Retour à voir.php</a>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "La colonne pseudo existe déjà !<br>";
        echo "<a href='/voir.php'>Retour à voir.php</a>";
    } else {
        echo "Erreur : " . $e->getMessage();
    }
}
?>