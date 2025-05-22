# Documentation Technique EcoRide

## Réflexions initiales technologiques

Pour développer EcoRide, plusieurs options technologiques ont été étudiées. Le choix final s'est porté sur une stack LAMP simplifiée pour les raisons suivantes :

### Technologies choisies
- **PHP natif** : Choisi pour sa simplicité d'implémentation et sa compatibilité universelle avec l'hébergement web
- **MySQL** : Base de données relationnelle robuste, parfaitement adaptée au modèle de données de covoiturage
- **Bootstrap** : Framework CSS permettant un développement responsive rapide
- **JavaScript vanilla** : Fonctionnalités interactives basiques sans dépendance à des frameworks
- **Chart.js** : Bibliothèque légère pour les visualisations de données dans l'interface d'administration

### Alternatives considérées
- Framework PHP (Laravel, Symfony) : Écartés car trop complexes pour ce projet de taille moyenne
- Frameworks JS (React, Vue) : Non nécessaires pour les interactions requises
- Base NoSQL : Moins adaptée aux relations complexes entre utilisateurs, trajets et véhicules

## Configuration de l'environnement de travail

### Prérequis
- XAMPP 8.0 ou supérieur (incluant Apache, PHP, MySQL)
- Navigateur web moderne (Chrome, Firefox, Edge)
- Éditeur de code (Visual Studio Code recommandé)
- Git pour le versionnement

### Installation locale
1. **Installation XAMPP**
   - Télécharger et installer XAMPP depuis apachefriends.org
   - Démarrer les services Apache et MySQL
   
2. **Configuration du projet**
   ```bash
   # Cloner le projet
   git clone [URL_DEPOT]
   
   # Copier dans htdocs
   cp -r ecoride /xampp/htdocs/
   
   # Importer la base de données
   mysql -u root -p ecoride < bdd/ecoride.sql
   ```

3. **Configuration base de données**
   ```php
   // connexion.php (environnement local)
   $host = 'localhost';
   $dbname = 'ecoride';
   $username = 'root';
   $password = '';
   ```

### Structure du projet
```
/ecoride/
├── index.php                 # Page d'accueil
├── login.php                 # Authentification
├── inscription.php           # Création de compte
├── recherche.php             # Recherche de trajets
├── voir.php                  # Détail d'un trajet
├── mon-espace.php           # Espace utilisateur
├── admin.php                 # Interface administrateur
├── employe.php              # Interface employé
├── creer-trajet.php         # Création de trajet
├── ajouter-vehicule.php     # Ajout de véhicule
├── mentions-legales.php     # Mentions légales
├── deconnexion.php          # Déconnexion
├── connexion.php            # Configuration BDD
├── auth-check.php           # Vérification authentification
├── auth-check-admin.php     # Vérification droits admin
├── auth-check-employe.php   # Vérification droits employé
├── composer.json            # Configuration Heroku
├── Procfile                 # Configuration serveur Heroku
├── bdd/
│   └── ecoride.sql         # Structure et données
├── doc/
│   ├── maquettes/          # Wireframes et mockups
│   ├── manuel_utilisation.pdf
│   ├── charte_graphique.pdf
│   └── documentation_technique.pdf
└── README.md               # Documentation projet
```

## Modèle conceptuel de données

### Entités principales

#### Utilisateur
- **id_user** (PK) : Identifiant unique
- **nom, prenom** : Identité
- **email** : Connexion (unique)
- **mot_de_passe** : Hash sécurisé
- **role** : utilisateur|employe|admin
- **credit** : Système de paiement interne
- **date_inscription** : Traçabilité

#### Covoiturage
- **id_covoiturage** (PK) : Identifiant unique
- **id_chauffeur** (FK) : Référence utilisateur
- **id_vehicule** (FK) : Référence véhicule
- **ville_depart, ville_arrivee** : Itinéraire
- **date, heure_depart, heure_arrivee** : Planning
- **prix** : Coût par passager
- **nb_places** : Places disponibles
- **etat** : planifié|en_cours|terminé

#### Vehicule
- **id_vehicule** (PK) : Identifiant unique
- **id_proprietaire** (FK) : Référence utilisateur
- **marque, modele** : Identification
- **energie** : essence|diesel|électrique|hybride
- **immatriculation** : Plaque d'immatriculation
- **couleur** : Description

#### Participation
- **id_participation** (PK) : Identifiant unique
- **id_user** (FK) : Référence passager
- **id_covoiturage** (FK) : Référence trajet
- **status** : confirmé|annulé
- **date_inscription** : Horodatage

#### Preferences
- **id_preference** (PK) : Identifiant unique
- **id_user** (FK) : Référence utilisateur
- **fumeur, animaux** : Préférences booléennes
- **autre** : Préférences textuelles

### Relations
- Un utilisateur peut avoir plusieurs véhicules (1:N)
- Un utilisateur peut créer plusieurs covoiturages (1:N)
- Un utilisateur peut participer à plusieurs covoiturages (N:M via participation)
- Un covoiturage utilise un véhicule (N:1)
- Un utilisateur a des préférences (1:1)

## Diagramme d'utilisation

### Acteurs
- **Visiteur** : Consultation des trajets, inscription
- **Utilisateur** : Réservation et création de trajets
- **Employé** : Modération des avis
- **Administrateur** : Gestion complète de la plateforme

### Cas d'utilisation principaux
1. **Rechercher un trajet** (Visiteur, Utilisateur)
2. **Réserver un trajet** (Utilisateur)
3. **Créer un trajet** (Utilisateur)
4. **Gérer ses véhicules** (Utilisateur)
5. **Modérer les avis** (Employé)
6. **Administrer la plateforme** (Administrateur)

## Diagramme de séquence - Réservation de trajet

```
Utilisateur -> Interface : Recherche trajet
Interface -> BDD : SELECT trajets disponibles
BDD -> Interface : Résultats
Interface -> Utilisateur : Affichage trajets

Utilisateur -> Interface : Clic "Participer"
Interface -> BDD : Vérification crédits
Interface -> BDD : Vérification places
Interface -> BDD : INSERT participation
Interface -> BDD : UPDATE crédits utilisateur
Interface -> BDD : UPDATE places trajet
BDD -> Interface : Confirmation
Interface -> Utilisateur : Message succès
```

## Architecture de sécurité

### Authentification
- **Hachage des mots de passe** : `password_hash()` avec salt automatique
- **Sessions sécurisées** : Gestion via `$_SESSION`
- **Vérification des rôles** : Middleware d'autorisation

### Protection des données
- **Requêtes préparées** : Protection contre l'injection SQL
- **Échappement des sorties** : `htmlspecialchars()` contre XSS
- **Validation des entrées** : Filtrage et sécurisation des données

### Contrôle d'accès
```php
// Exemple de vérification des droits
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
```

## Documentation du déploiement

### Environnement de production (Heroku)

#### Configuration initiale
```bash
# Création de l'application
heroku create ecoride-app

# Ajout de la base de données MySQL
heroku addons:create jawsdb:kitefin

# Configuration des variables d'environnement
heroku config:get JAWSDB_URL
```

#### Fichiers de configuration
- **composer.json** : Identification du projet PHP pour Heroku
- **Procfile** : `web: vendor/bin/heroku-php-apache2`

#### Configuration base de données production
```php
// connexion.php - Détection automatique environnement
if (getenv('JAWSDB_URL')) {
    // Configuration Heroku JawsDB
    $dbUrl = parse_url(getenv('JAWSDB_URL'));
    $host = $dbUrl['host'];
    $username = $dbUrl['user'];
    $password = $dbUrl['pass'];
    $dbname = ltrim($dbUrl['path'], '/');
} else {
    // Configuration locale XAMPP
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'ecoride';
}
```

#### Déploiement
1. **Préparation du code**
   ```bash
   git add .
   git commit -m "Ready for production"
   ```

2. **Déploiement vers Heroku**
   ```bash
   git push heroku main
   ```

3. **Vérification**
   ```bash
   heroku logs --tail
   heroku open
   ```

#### Migration des données
- Import de la structure via interface JawsDB ou scripts SQL
- Création du compte administrateur en production
- Configuration des données de test

### Avantages du déploiement choisi
- **Heroku** : Plateforme simple et fiable
- **JawsDB** : Service MySQL managé
- **Déploiement automatique** : Intégration Git native
- **Scalabilité** : Possibilité d'augmenter les ressources

### Monitoring et maintenance
- **Logs Heroku** : Surveillance des erreurs
- **Métriques** : Suivi des performances via dashboard Heroku
- **Backups** : Sauvegardes automatiques JawsDB

## Performance et optimisation

### Optimisations implémentées
- **Index de base de données** : Sur les colonnes de recherche fréquente
- **Requêtes optimisées** : Jointures efficaces et limitation des résultats
- **CDN** : Bootstrap et Chart.js via CDN public

### Améliorations futures possibles
- **Cache** : Mise en cache des résultats de recherche
- **Compression** : Gzip pour les assets statiques
- **Optimisation images** : Compression et formats modernes

## Conclusion

Cette architecture simple et robuste répond parfaitement aux besoins d'EcoRide tout en restant maintenable et évolutive. Le choix d'une stack classique garantit la compatibilité et facilite les déploiements futurs.