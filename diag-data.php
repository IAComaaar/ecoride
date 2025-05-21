<?php
session_start();
require_once 'connexion.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic des données utilisateur</h1>";

if (!isset($_SESSION['id_user'])) {
    echo "<p style='color:red'>Aucun utilisateur connecté. Session ID manquant.</p>";
    echo "<p>Variables de session disponibles:</p>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    exit;
}

$userId = $_SESSION['id_user'];
echo "<p>Utilisateur connecté avec ID: " . htmlspecialchars($userId) . "</p>";

// Vérifier la table utilisateur
echo "<h2>Vérification de l'utilisateur</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color:green'>Utilisateur trouvé :</p>";
        echo "<ul>";
        foreach ($user as $key => $value) {
            echo "<li>" . htmlspecialchars($key) . ": " . htmlspecialchars($value) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>Aucun utilisateur trouvé avec cet ID.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur lors de la recherche de l'utilisateur : " . $e->getMessage() . "</p>";
}

// Vérifier la table covoiturage (trajets créés)
echo "<h2>Vérification des trajets créés</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE id_chauffeur = ?");
    $stmt->execute([$userId]);
    $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($trajets) > 0) {
        echo "<p style='color:green'>Trajets trouvés (" . count($trajets) . ") :</p>";
        foreach ($trajets as $index => $trajet) {
            echo "<div style='margin-bottom:10px; border:1px solid #ccc; padding:10px;'>";
            echo "<h4>Trajet #" . ($index + 1) . "</h4>";
            echo "<ul>";
            foreach ($trajet as $key => $value) {
                echo "<li>" . htmlspecialchars($key) . ": " . htmlspecialchars($value) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    } else {
        echo "<p style='color:orange'>Aucun trajet créé par cet utilisateur.</p>";
        
        // Vérifier si la table contient des trajets
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM covoiturage");
        $result = $stmt->fetch();
        echo "<p>Total des trajets dans la base : " . $result['total'] . "</p>";
        
        if ($result['total'] > 0) {
            $stmt = $pdo->query("SELECT id_chauffeur, COUNT(*) as nb FROM covoiturage GROUP BY id_chauffeur");
            $chauffeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p>Répartition des trajets par chauffeur :</p>";
            echo "<ul>";
            foreach ($chauffeurs as $chauffeur) {
                echo "<li>Chauffeur ID " . $chauffeur['id_chauffeur'] . ": " . $chauffeur['nb'] . " trajet(s)</li>";
            }
            echo "</ul>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur lors de la recherche des trajets : " . $e->getMessage() . "</p>";
}

// Vérifier la table participation (réservations)
echo "<h2>Vérification des réservations</h2>";
try {
    // Vérifier si la table existe
    $result = $pdo->query("SHOW TABLES LIKE 'participation'");
    if ($result->rowCount() == 0) {
        echo "<p style='color:orange'>La table 'participation' n'existe pas encore.</p>";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM participation WHERE id_user = ?");
        $stmt->execute([$userId]);
        $participations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($participations) > 0) {
            echo "<p style='color:green'>Réservations trouvées (" . count($participations) . ") :</p>";
            foreach ($participations as $index => $participation) {
                echo "<div style='margin-bottom:10px; border:1px solid #ccc; padding:10px;'>";
                echo "<h4>Réservation #" . ($index + 1) . "</h4>";
                echo "<ul>";
                foreach ($participation as $key => $value) {
                    echo "<li>" . htmlspecialchars($key) . ": " . htmlspecialchars($value) . "</li>";
                }
                echo "</ul>";
                echo "</div>";
            }
        } else {
            echo "<p style='color:orange'>Aucune réservation trouvée pour cet utilisateur.</p>";
            
            // Vérifier si la table contient des participations
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM participation");
            $result = $stmt->fetch();
            echo "<p>Total des réservations dans la base : " . $result['total'] . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Erreur lors de la recherche des réservations : " . $e->getMessage() . "</p>";
}

// Création d'exemples de données
echo "<h2>Assistant de création de données</h2>";
echo "<p>Si vous n'avez pas de données, vous pouvez utiliser les boutons ci-dessous pour créer des exemples.</p>";

// Formulaire pour créer un trajet exemple
echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
echo "<input type='hidden' name='action' value='create_trajet'>";
echo "<button type='submit' style='background-color:#4CAF50;color:white;padding:10px;border:none;cursor:pointer;'>Créer un trajet exemple</button>";
echo "</form><br>";

// Formulaire pour créer une réservation exemple
echo "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
echo "<input type='hidden' name='action' value='create_reservation'>";
echo "<button type='submit' style='background-color:#2196F3;color:white;padding:10px;border:none;cursor:pointer;'>Créer une réservation exemple</button>";
echo "</form>";

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_trajet') {
        try {
            // Créer un trajet exemple
            $stmt = $pdo->prepare("INSERT INTO covoiturage 
                (id_chauffeur, id_vehicule, ville_depart, ville_arrivee, date, heure_depart, heure_arrivee, prix, nb_places, description) 
                VALUES (?, 1, 'Paris', 'Lyon', '2025-06-01', '08:00:00', '12:00:00', 25.00, 3, 'Trajet exemple créé automatiquement')");
            $stmt->execute([$userId]);
            
            $id = $pdo->lastInsertId();
            echo "<p style='color:green'>Trajet exemple créé avec succès (ID: $id)!</p>";
            echo "<p>Rechargez la page pour voir les détails.</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red'>Erreur lors de la création du trajet : " . $e->getMessage() . "</p>";
        }
    } elseif ($_POST['action'] === 'create_reservation') {
        try {
            // Vérifier si la table participation existe
            $result = $pdo->query("SHOW TABLES LIKE 'participation'");
            if ($result->rowCount() == 0) {
                // Créer la table si elle n'existe pas
                $pdo->exec("CREATE TABLE participation (
                    id_participation INT AUTO_INCREMENT PRIMARY KEY,
                    id_covoiturage INT,
                    id_user INT,
                    status VARCHAR(50) DEFAULT 'confirmé',
                    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage),
                    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user)
                )");
                echo "<p style='color:green'>Table participation créée!</p>";
            }
            
            // Vérifier s'il existe des trajets
            $stmt = $pdo->query("SELECT id_covoiturage FROM covoiturage LIMIT 1");
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($trajet) {
                // Créer une réservation exemple
                $stmt = $pdo->prepare("INSERT INTO participation (id_covoiturage, id_user, status) VALUES (?, ?, 'confirmé')");
                $stmt->execute([$trajet['id_covoiturage'], $userId]);
                
                $id = $pdo->lastInsertId();
                echo "<p style='color:green'>Réservation exemple créée avec succès (ID: $id)!</p>";
                echo "<p>Rechargez la page pour voir les détails.</p>";
            } else {
                echo "<p style='color:orange'>Aucun trajet disponible pour créer une réservation. Créez d'abord un trajet.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color:red'>Erreur lors de la création de la réservation : " . $e->getMessage() . "</p>";
        }
    }
}

echo "<p><a href='/mon-espace.php' style='display:inline-block;margin-top:20px;padding:10px;background-color:#f44336;color:white;text-decoration:none;'>Retour à mon espace</a></p>";
?>