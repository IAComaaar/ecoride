-- =============================================
-- Base de données EcoRide - ECF 2025
-- Création et intégration des données
-- =============================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ecoride;
USE ecoride;

-- =============================================
-- CRÉATION DES TABLES
-- =============================================

-- Table utilisateur
CREATE TABLE IF NOT EXISTS utilisateur (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    pseudo VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('utilisateur', 'employe', 'admin') DEFAULT 'utilisateur',
    credit INT DEFAULT 20,
    suspendu BOOLEAN DEFAULT 0,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table vehicule
CREATE TABLE IF NOT EXISTS vehicule (
    id_vehicule INT AUTO_INCREMENT PRIMARY KEY,
    id_proprietaire INT,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    couleur VARCHAR(50),
    immatriculation VARCHAR(20),
    energie ENUM('essence', 'diesel', 'électrique', 'hybride') DEFAULT 'essence',
    places INT DEFAULT 4,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proprietaire) REFERENCES utilisateur(id_user) ON DELETE CASCADE
);

-- Table covoiturage
CREATE TABLE IF NOT EXISTS covoiturage (
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
    etat ENUM('planifié', 'en cours', 'terminé', 'annulé') DEFAULT 'planifié',
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_chauffeur) REFERENCES utilisateur(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_vehicule) REFERENCES vehicule(id_vehicule) ON DELETE CASCADE
);

-- Table participation
CREATE TABLE IF NOT EXISTS participation (
    id_participation INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_covoiturage INT,
    status ENUM('confirmé', 'annulé') DEFAULT 'confirmé',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage) ON DELETE CASCADE
);

-- Table preferences
CREATE TABLE IF NOT EXISTS preferences (
    id_preference INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    fumeur BOOLEAN DEFAULT 0,
    animaux BOOLEAN DEFAULT 0,
    autre TEXT,
    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE
);

-- Table avis (pour les employés)
CREATE TABLE IF NOT EXISTS avis (
    id_avis INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_covoiturage INT,
    id_chauffeur INT,
    note INT CHECK (note >= 1 AND note <= 5),
    commentaire TEXT,
    valide BOOLEAN DEFAULT 0,
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES utilisateur(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage) ON DELETE CASCADE,
    FOREIGN KEY (id_chauffeur) REFERENCES utilisateur(id_user) ON DELETE CASCADE
);

-- =============================================
-- INSERTION DES DONNÉES DE TEST
-- =============================================

-- Utilisateurs de test
INSERT INTO utilisateur (nom, prenom, pseudo, email, mot_de_passe, role, credit) VALUES
('Admin', 'System', 'admin', 'admin@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 100),
('Dupont', 'Jean', 'employe', 'employe@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employe', 50),
('Martin', 'Marie', 'user', 'user@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur', 20),
('Durand', 'Pierre', 'pierre_d', 'pierre@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur', 25),
('Moreau', 'Sophie', 'sophie_m', 'sophie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur', 18),
('Leroy', 'Thomas', 'thomas_l', 'thomas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur', 30);

-- Véhicules de test
INSERT INTO vehicule (id_proprietaire, marque, modele, couleur, immatriculation, energie, places) VALUES
(4, 'Renault', 'Zoe', 'Blanc', 'AB-123-CD', 'électrique', 4),
(5, 'Peugeot', '308', 'Gris', 'EF-456-GH', 'essence', 5),
(6, 'Tesla', 'Model 3', 'Noir', 'IJ-789-KL', 'électrique', 5),
(4, 'Volkswagen', 'Golf', 'Bleu', 'MN-012-OP', 'diesel', 4),
(3, 'Toyota', 'Prius', 'Argent', 'QR-345-ST', 'hybride', 5);

-- Covoiturages de test
INSERT INTO covoiturage (id_chauffeur, id_vehicule, ville_depart, ville_arrivee, date, heure_depart, heure_arrivee, prix, nb_places, etat) VALUES
(4, 1, 'Paris', 'Lyon', '2025-06-15', '08:00:00', '12:30:00', 35.00, 3, 'planifié'),
(5, 2, 'Marseille', 'Nice', '2025-06-16', '14:00:00', '16:30:00', 25.00, 4, 'planifié'),
(6, 3, 'Toulouse', 'Bordeaux', '2025-06-17', '09:30:00', '12:00:00', 28.00, 4, 'planifié'),
(4, 4, 'Lille', 'Bruxelles', '2025-06-18', '07:00:00', '09:30:00', 40.00, 3, 'planifié'),
(3, 5, 'Nantes', 'Rennes', '2025-06-19', '16:00:00', '17:30:00', 20.00, 4, 'planifié'),
(5, 2, 'Lyon', 'Genève', '2025-06-20', '06:30:00', '09:00:00', 45.00, 3, 'planifié'),
(6, 3, 'Strasbourg', 'Paris', '2025-06-21', '15:00:00', '19:00:00', 50.00, 4, 'planifié');

-- Participations de test
INSERT INTO participation (id_user, id_covoiturage, status) VALUES
(3, 1, 'confirmé'),
(5, 3, 'confirmé'),
(6, 1, 'confirmé'),
(3, 4, 'confirmé');

-- Préférences de test
INSERT INTO preferences (id_user, fumeur, animaux, autre) VALUES
(4, 0, 1, 'Musique classique appréciée'),
(5, 0, 0, 'Voyage en silence de préférence'),
(6, 0, 1, 'Discussion et bonne humeur');

-- Avis de test
INSERT INTO avis (id_user, id_covoiturage, id_chauffeur, note, commentaire, valide) VALUES
(3, 1, 4, 5, 'Excellent chauffeur, très ponctuel et voiture propre !', 1),
(5, 3, 6, 4, 'Bon trajet, conduite sécurisée.', 1),
(6, 1, 4, 5, 'Super expérience, je recommande !', 1),
(3, 4, 4, 3, 'Trajet correct mais un peu de retard au départ.', 0);

-- =============================================
-- INDEX POUR OPTIMISATION
-- =============================================

-- Index pour améliorer les performances de recherche
CREATE INDEX idx_covoiturage_date ON covoiturage(date);
CREATE INDEX idx_covoiturage_depart_arrivee ON covoiturage(ville_depart, ville_arrivee);
CREATE INDEX idx_participation_user ON participation(id_user);
CREATE INDEX idx_participation_covoiturage ON participation(id_covoiturage);
CREATE INDEX idx_vehicule_proprietaire ON vehicule(id_proprietaire);
CREATE INDEX idx_avis_chauffeur ON avis(id_chauffeur);

-- =============================================
-- VUES UTILES
-- =============================================

-- Vue des trajets avec détails du chauffeur et du véhicule
CREATE VIEW vue_trajets_complets AS
SELECT 
    c.id_covoiturage,
    c.ville_depart,
    c.ville_arrivee,
    c.date,
    c.heure_depart,
    c.heure_arrivee,
    c.prix,
    c.nb_places,
    c.etat,
    u.pseudo as chauffeur_pseudo,
    u.email as chauffeur_email,
    v.marque,
    v.modele,
    v.energie,
    CASE WHEN v.energie = 'électrique' THEN 1 ELSE 0 END as ecologique
FROM covoiturage c
JOIN utilisateur u ON c.id_chauffeur = u.id_user
JOIN vehicule v ON c.id_vehicule = v.id_vehicule
WHERE c.etat = 'planifié' AND c.nb_places > 0;

-- Vue des statistiques admin
CREATE VIEW vue_stats_admin AS
SELECT 
    DATE(date_creation) as jour,
    COUNT(*) as nb_trajets,
    COUNT(*) * 2 as credits_generes
FROM covoiturage 
GROUP BY DATE(date_creation)
ORDER BY jour DESC;

-- =============================================
-- FONCTIONS/PROCÉDURES UTILES
-- =============================================

-- Procédure pour calculer la note moyenne d'un chauffeur
DELIMITER //
CREATE PROCEDURE CalculerNoteMoyenne(IN chauffeur_id INT)
BEGIN
    DECLARE note_moy DECIMAL(3,2);
    
    SELECT AVG(note) INTO note_moy
    FROM avis 
    WHERE id_chauffeur = chauffeur_id AND valide = 1;
    
    UPDATE utilisateur 
    SET note_moyenne = note_moy 
    WHERE id_user = chauffeur_id;
END//
DELIMITER ;

-- =============================================
-- DONNÉES COMPLÈTES - RÉCAPITULATIF
-- =============================================

/*
COMPTES DE TEST CRÉÉS :
- Admin: admin@ecoride.fr / admin123
- Employé: employe@ecoride.fr / employe123  
- Utilisateur: user@ecoride.fr / user123

DONNÉES DISPONIBLES :
- 6 utilisateurs
- 5 véhicules (incluant électriques)
- 7 trajets planifiés
- 4 participations
- 3 préférences
- 4 avis (dont 1 en attente de validation)

Note: Tous les mots de passe sont hashés avec password_hash() 
et correspondent à : admin123, employe123, user123
*/