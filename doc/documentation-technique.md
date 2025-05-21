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

### Structure du projet