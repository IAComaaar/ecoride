# 🌿 EcoRide - Plateforme de Covoiturage Écologique

**ECF DWWM 2025** - Projet de validation du titre professionnel Développeur Web et Web Mobile

[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php)](https://www.php.net/)
[![MongoDB](https://img.shields.io/badge/MongoDB-7.0-47A248?logo=mongodb)](https://www.mongodb.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap)](https://getbootstrap.com/)

---

## 📋 Présentation

EcoRide est une plateforme de covoiturage axée sur l'écologie, permettant de :
- 🚗 Proposer des trajets en voiture (chauffeurs)
- 🧍 Réserver des places (passagers)
- 🌱 Filtrer les trajets écologiques (véhicules électriques)
- 💰 Gérer un système de crédits
- ⭐ Laisser des avis modérés

**Objectif :** Réduire l'empreinte carbone des déplacements.

---

## ✨ Fonctionnalités (13 User Stories)

### Front-end (Activité Type 1)

| US | Description | Statut |
|----|-------------|--------|
| US1 | Page d'accueil avec recherche | ✅ |
| US2 | Menu navigation | ✅ |
| US3 | Vue des covoiturages | ✅ |
| US4 | Filtres avancés | ✅ |
| US5 | Vue détaillée trajet | ✅ |
| US6 | Participation avec double confirmation | ✅ |
| US7 | Création de compte | ✅ |

### Back-end (Activité Type 2)

| US | Description | Statut |
|----|-------------|--------|
| US8 | Espace utilisateur (passager/chauffeur) | ✅ |
| US9 | Création de trajets | ✅ |
| US10 | Historique + annulation | ✅ |
| US11 | Workflow complet (démarrer → terminer → validation) | ✅ |
| US12 | Espace employé (modération + signalements) | ✅ |
| US13 | Dashboard admin (stats + suspension) | ✅ |

---

## 🛠️ Stack technique

### Backend
- **PHP 8.2** natif
- **PDO** pour MySQL
- **MongoDB PHP Library** pour NoSQL

### Frontend
- **HTML5 / CSS3**
- **Bootstrap 5.3**
- **JavaScript vanilla**

### Bases de données
- **MySQL / MariaDB** (données relationnelles)
- **MongoDB** (logs recherche, avis temporaires)

### Outils
- **Git / GitHub**
- **Composer**
- **XAMPP**

---

## 📦 Installation locale

### Prérequis
- XAMPP (Apache + MySQL)
- MongoDB Community Server
- Composer
- Git

### Installation
```bash
# 1. Cloner le repository
git clone https://github.com/IAComaaar/ecoride.git
cd ecoride

# 2. Installer les dépendances
composer install

# 3. Créer la base MySQL
# Dans phpMyAdmin : créer 'ecoride'
# Puis importer : database/sql/ecoride.sql

# 4. Configurer MongoDB
mongod  # Démarrer MongoDB
mongosh
use ecoride
db.createCollection("search_logs")
db.createCollection("pending_reviews")

# 5. Lancer l'application
# Démarrer Apache et MySQL via XAMPP
open http://localhost/ecoride/
🏗️ Architecture
Structure des fichiers
ecoride/
├── config/
│   ├── database.php       # Connexion MySQL
│   └── mongodb.php        # Connexion MongoDB
├── models/
│   ├── SearchLog.php      # Logs recherche (NoSQL)
│   └── PendingReview.php  # Avis temporaires (NoSQL)
├── database/
│   ├── README.md
│   └── sql/
│       └── ecoride.sql    # Export base MySQL
├── .gitignore
├── composer.json
└── README.md
Base de données SQL (7 tables)
utilisateur
├── vehicule
├── preferences
└── covoiturage (+ colonne 'etat')
    ├── participation (+ colonne 'date_validation')
    ├── avis
    └── signalements
Collections MongoDB
search_logs : Historique recherches
javascript{
  user_id: Number,
  search_params: {depart, arrivee, date},
  results_count: Number,
  timestamp: ISODate
}
pending_reviews : Avis en attente validation
javascript{
  covoiturage_id: Number,
  user_id: Number,
  note: Number,
  status: "pending|approved|rejected"
}

👤 Comptes de test
RôleEmailMot de passeAdminadmin@ecoride.frpasswordEmployéemploye@ecoride.frpasswordUtilisateurtest@test.frpassword

🔒 Sécurité
✅ Hachage bcrypt des mots de passe
✅ Requêtes préparées PDO (protection SQL injection)
✅ htmlspecialchars() sur toutes sorties (protection XSS)
✅ Tokens CSRF sur formulaires critiques
✅ Double confirmation actions importantes
✅ Logs d'erreur côté serveur uniquement

🚀 Déploiement
Variables d'environnement
bash# MySQL (ex: JawsDB Heroku)
JAWSDB_COBALT_URL=mysql://user:pass@host/db

# MongoDB (ex: MongoDB Atlas)
MONGODB_URI=mongodb+srv://user:pass@cluster/ecoride
Heroku
bashheroku create ecoride-prod
heroku addons:create jawsdb:kitefin
git push heroku main

👨‍💻 Auteur
Marco - Candidat DWWM 2025
📧 Email: mtnunes.pro@gmail.com
🔗 GitHub: https://github.com/IAComaaar

📝 Licence
Projet EcoRide © 2025
