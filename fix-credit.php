<?php
// fix-credit.php
require_once 'connexion.php';

try {
    // Ajouter la colonne credit
    $pdo->exec("ALTER TABLE utilisateur ADD COLUMN credit INT DEFAULT 10");
    echo "Colonne credit ajoutée avec succès ! Tous les utilisateurs commencent avec 10 crédits.<br>";
    echo "<a href='/inscription.php'>Retour à l'inscription</a>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "La colonne credit existe déjà !<br>";
        echo "<a href='/inscription.php'>Retour à l'inscription</a>";
    } else {
        echo "Erreur : " . $e->getMessage();
    }
}
?>