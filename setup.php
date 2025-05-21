<?php
// setup.php - Script unique pour configurer la base de données
session_start();
require_once 'connexion.php';

// Créer les tables nécessaires
$pdo->exec("CREATE TABLE IF NOT EXISTS vehicule (
    id_vehicule INT AUTO_INCREMENT PRIMARY KEY,
    id_proprietaire INT,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee INT,
    couleur VARCHAR(50),
    places INT NOT NULL,
    energie VARCHAR(50),
    FOREIGN KEY (id_proprietaire) REFERENCES utilisateur(id_user)
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS covoiturage (
    id_covoiturage INT AUTO_INCREMENT PRIMARY KEY,
    id_chauffeur INT,
    id_vehicule INT,
    ville_depart VARCHAR(100) NOT NULL,
    ville_arrivee VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    heure_depart TIME NOT NULL,
    heure_arrivee TIME NOT NULL,
    prix DECIMAL(6,2) NOT NULL,
    nb_places INT NOT NULL,
    description TEXT,
    etat VARCHAR(50) DEFAULT 'non démarré',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS participation (
    id_participation INT AUTO_INCREMENT PRIMARY KEY,
    id_covoiturage INT,
    id_user INT,
    status VARCHAR(50) DEFAULT 'confirmé',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Ajouter la colonne credit si nécessaire
try {
    $pdo->query("SELECT credit FROM utilisateur LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE utilisateur ADD COLUMN credit INT DEFAULT 10");
}

// Ajouter un véhicule, un trajet et une réservation si l'utilisateur est connecté
if (isset($_SESSION['id_user'])) {
    $userId = $_SESSION['id_user'];
    
    // Créer un véhicule si l'utilisateur n'en a pas
    $stmt = $pdo->prepare("SELECT id_vehicule FROM vehicule WHERE id_proprietaire = ? LIMIT 1");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO vehicule (id_proprietaire, marque, modele, annee, couleur, places, energie) 
                VALUES (?, 'Renault', 'Zoe', 2023, 'Bleu', 4, 'électrique')")->execute([$userId]);
    }
    
    // Créer un trajet si l'utilisateur n'en a pas
    $stmt = $pdo->prepare("SELECT id_covoiturage FROM covoiturage WHERE id_chauffeur = ? LIMIT 1");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("SELECT id_vehicule FROM vehicule WHERE id_proprietaire = ? LIMIT 1");
        $stmt->execute([$userId]);
        $vehicle = $stmt->fetch();
        if ($vehicle) {
            $pdo->prepare("INSERT INTO covoiturage (id_chauffeur, id_vehicule, ville_depart, ville_arrivee, date, heure_depart, 
                    heure_arrivee, prix, nb_places, description, etat) 
                    VALUES (?, ?, 'Paris', 'Lyon', '2025-06-01', '08:00:00', '12:00:00', 25.00, 3, 
                    'Trajet exemple', 'non démarré')")->execute([$userId, $vehicle['id_vehicule']]);
        }
    }
    
    // Créer un autre utilisateur et un trajet pour pouvoir faire une réservation
    $otherUserEmail = 'autre@exemple.com';
    $stmt = $pdo->prepare("SELECT id_user FROM utilisateur WHERE email = ?");
    $stmt->execute([$otherUserEmail]);
    if (!$stmt->fetch()) {
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) 
                VALUES ('Autre', 'Utilisateur', ?, ?, 'utilisateur')")->execute([$otherUserEmail, $password]);
        $otherUserId = $pdo->lastInsertId();
        
        $pdo->prepare("INSERT INTO vehicule (id_proprietaire, marque, modele, annee, couleur, places, energie) 
                VALUES (?, 'Toyota', 'Prius', 2022, 'Vert', 4, 'hybride')")->execute([$otherUserId]);
        $otherVehicleId = $pdo->lastInsertId();
        
        $pdo->prepare("INSERT INTO covoiturage (id_chauffeur, id_vehicule, ville_depart, ville_arrivee, date, heure_depart, 
                heure_arrivee, prix, nb_places, description, etat) 
                VALUES (?, ?, 'Lyon', 'Paris', '2025-06-02', '09:00:00', '13:00:00', 30.00, 3, 
                'Trajet retour', 'non démarré')")->execute([$otherUserId, $otherVehicleId]);
        $otherRideId = $pdo->lastInsertId();
    } else {
        // Trouver un trajet d'un autre utilisateur
        $stmt = $pdo->prepare("SELECT id_covoiturage FROM covoiturage WHERE id_chauffeur != ? LIMIT 1");
        $stmt->execute([$userId]);
        $otherRide = $stmt->fetch();
        if ($otherRide) {
            $otherRideId = $otherRide['id_covoiturage'];
        }
    }
    
    // Créer une réservation si l'utilisateur n'en a pas
    if (isset($otherRideId)) {
        $stmt = $pdo->prepare("SELECT id_participation FROM participation WHERE id_user = ? LIMIT 1");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO participation (id_covoiturage, id_user, status) VALUES (?, ?, 'confirmé')")
                ->execute([$otherRideId, $userId]);
            $pdo->prepare("UPDATE covoiturage SET nb_places = nb_places - 1 WHERE id_covoiturage = ?")
                ->execute([$otherRideId]);
        }
    }
}

echo "Configuration terminée. <a href='mon-espace.php'>Retour à mon espace</a>";
?>