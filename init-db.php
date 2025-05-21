<?php
require_once 'connexion.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Initialisation de la base de données</h1>";

$tables = [
    "CREATE TABLE IF NOT EXISTS utilisateur (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL,
        role ENUM('utilisateur', 'employe', 'admin') DEFAULT 'utilisateur',
        date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS vehicule (
        id_vehicule INT AUTO_INCREMENT PRIMARY KEY,
        id_proprietaire INT,
        marque VARCHAR(100) NOT NULL,
        modele VARCHAR(100) NOT NULL,
        annee INT,
        couleur VARCHAR(50),
        places INT NOT NULL,
        energie VARCHAR(50),
        FOREIGN KEY (id_proprietaire) REFERENCES utilisateur(id_user) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS covoiturage (
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
        date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_chauffeur) REFERENCES utilisateur(id_user) ON DELETE CASCADE,
        FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS reservation (
        id_reservation INT AUTO_INCREMENT PRIMARY KEY,
        id_covoiturage INT,
        id_passager INT,
        date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
        statut ENUM('en_attente', 'confirmee', 'annulee') DEFAULT 'en_attente',
        FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage) ON DELETE CASCADE,
        FOREIGN KEY (id_passager) REFERENCES utilisateur(id_user) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS preferences (
        id_preference INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT,
        musique BOOLEAN DEFAULT 0,
        animaux BOOLEAN DEFAULT 0,
        fumeur BOOLEAN DEFAULT 0,
        discussion BOOLEAN DEFAULT 0,
        FOREIGN KEY (id_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE
    )"
];


foreach ($tables as $sql) {
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>Table créée avec succès.</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Erreur lors de la création de la table : " . $e->getMessage() . "</p>";
    }
}

// Afin de créér du compte administrateur
$checkSql = "SELECT * FROM utilisateur WHERE email = 'admin@admin.fr'";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute();

if ($checkStmt->rowCount() == 0) {
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) 
            VALUES ('Admin', 'System', 'admin@admin.fr', :password, 'admin')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':password', $password);
    
    try {
        $stmt->execute();
        echo "<p style='color: green;'>Compte administrateur créé avec succès!</p>";
        echo "<p>Email: admin@admin.fr<br>Mot de passe: admin123</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Erreur lors de la création du compte administrateur : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Le compte administrateur existe déjà.</p>";
}

echo "<p><a href='/'>Retour à l'accueil</a></p>";
?>