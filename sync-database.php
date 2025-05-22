<?php
// sync-database.php
require_once 'connexion.php';

echo "<h1>Synchronisation complète de la base de données</h1>";

try {
    // 1. Ajouter toutes les colonnes manquantes dans utilisateur
    echo "<h2>Table utilisateur</h2>";
    
    // Colonne pseudo
    try {
        $pdo->exec("ALTER TABLE utilisateur ADD COLUMN pseudo VARCHAR(50)");
        echo "✅ Colonne pseudo ajoutée<br>";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "✅ Colonne pseudo existe déjà<br>";
        }
    }
    
    // Autres colonnes possibles
    $colonnes_utilisateur = [
        'telephone' => 'VARCHAR(20)',
        'date_naissance' => 'DATE',
        'adresse' => 'TEXT',
        'ville' => 'VARCHAR(100)',
        'code_postal' => 'VARCHAR(10)',
        'photo_profil' => 'VARCHAR(255)',
        'note_moyenne' => 'DECIMAL(3,2) DEFAULT 5.00',
        'nb_trajets' => 'INT DEFAULT 0',
        'statut' => 'ENUM("actif", "inactif", "suspendu") DEFAULT "actif"',
        'date_derniere_connexion' => 'DATETIME'
    ];
    
    foreach ($colonnes_utilisateur as $nom => $type) {
        try {
            $pdo->exec("ALTER TABLE utilisateur ADD COLUMN $nom $type");
            echo "✅ Colonne $nom ajoutée<br>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "✅ Colonne $nom existe déjà<br>";
            }
        }
    }
    
    // 2. Compléter la table vehicule
    echo "<h2>Table vehicule</h2>";
    
    $colonnes_vehicule = [
        'kilometrage' => 'INT',
        'consommation' => 'DECIMAL(4,2)',
        'photo' => 'VARCHAR(255)',
        'description' => 'TEXT',
        'date_ajout' => 'DATETIME DEFAULT CURRENT_TIMESTAMP'
    ];
    
    foreach ($colonnes_vehicule as $nom => $type) {
        try {
            $pdo->exec("ALTER TABLE vehicule ADD COLUMN $nom $type");
            echo "✅ Colonne $nom ajoutée<br>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "✅ Colonne $nom existe déjà<br>";
            }
        }
    }
    
    // 3. Compléter la table covoiturage
    echo "<h2>Table covoiturage</h2>";
    
    $colonnes_covoiturage = [
        'adresse_depart' => 'TEXT',
        'adresse_arrivee' => 'TEXT',
        'points_intermediaires' => 'TEXT',
        'preferences' => 'TEXT',
        'conditions' => 'TEXT',
        'statut' => 'ENUM("actif", "complet", "annule", "termine") DEFAULT "actif"',
        'places_reservees' => 'INT DEFAULT 0',
        'distance_km' => 'INT',
        'co2_economise' => 'DECIMAL(6,2)',
        'recurrent' => 'BOOLEAN DEFAULT FALSE',
        'jours_recurrence' => 'VARCHAR(20)'
    ];
    
    foreach ($colonnes_covoiturage as $nom => $type) {
        try {
            $pdo->exec("ALTER TABLE covoiturage ADD COLUMN $nom $type");
            echo "✅ Colonne $nom ajoutée<br>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "✅ Colonne $nom existe déjà<br>";
            }
        }
    }
    
    // 4. Compléter la table participation
    echo "<h2>Table participation</h2>";
    
    $colonnes_participation = [
        'message' => 'TEXT',
        'note_chauffeur' => 'INT',
        'note_passager' => 'INT',
        'commentaire_chauffeur' => 'TEXT',
        'commentaire_passager' => 'TEXT',
        'date_annulation' => 'DATETIME',
        'motif_annulation' => 'TEXT'
    ];
    
    foreach ($colonnes_participation as $nom => $type) {
        try {
            $pdo->exec("ALTER TABLE participation ADD COLUMN $nom $type");
            echo "✅ Colonne $nom ajoutée<br>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "✅ Colonne $nom existe déjà<br>";
            }
        }
    }
    
    // 5. Mettre à jour les données existantes
    echo "<h2>Mise à jour des données</h2>";
    
    // Donner des pseudos aux utilisateurs qui n'en ont pas
    $pdo->exec("UPDATE utilisateur SET pseudo = CONCAT(prenom, ' ', SUBSTRING(nom, 1, 1), '.') WHERE pseudo IS NULL OR pseudo = ''");
    echo "✅ Pseudos mis à jour<br>";
    
    // Mettre les statuts par défaut
    $pdo->exec("UPDATE utilisateur SET statut = 'actif' WHERE statut IS NULL");
    $pdo->exec("UPDATE covoiturage SET statut = 'actif' WHERE statut IS NULL");
    echo "✅ Statuts mis à jour<br>";
    
    echo "<h2>✅ Synchronisation terminée !</h2>";
    echo "<p><a href='/voir.php'>Tester voir.php</a> | <a href='/mon-espace.php'>Mon espace</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Erreur : " . $e->getMessage() . "</p>";
}
?>