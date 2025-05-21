EcoRide 🌿 – Mise en place
Préambule
Ce projet fonctionne avec un environnement local de type XAMPP incluant Apache, PHP 8+, et MySQL.
Aucun gestionnaire de paquets comme npm ou composer n'est requis.

📦 Dépendances
Aucune dépendance externe à installer. Tous les scripts front-end utilisent Bootstrap, et les graphiques sont générés avec Chart.js via un CDN.

🗄️ Base de données & Authentification

Placez le dossier /ecoride dans htdocs/ de XAMPP.
Démarrez Apache et MySQL via le panneau de contrôle XAMPP.
Dans phpMyAdmin, créez une base de données nommée ecoride.
Importez le fichier SQL fourni (bdd/ecoride.sql).
Ouvrez le fichier connexion.php et vérifiez la configuration suivante :

php$pdo = new PDO('mysql:host=localhost;dbname=ecoride', 'root', '');

L'authentification repose sur une gestion de rôles (utilisateur, employe, admin) via sessions PHP, avec stockage sécurisé des mots de passe grâce à password_hash().


🎨 Maquettes et charte graphique
Les maquettes de l'application se trouvent dans le dossier doc/maquettes/ :

Maquette.pdf contient :

3 maquettes bureautiques (accueil, recherche, espace utilisateur)
3 maquettes mobiles correspondantes
La palette de couleurs et la typographie utilisées



Charte graphique
Palette de couleurs

Vert principal : #198754 (navbar, boutons, accents)
Vert clair : #4caf50 (éléments secondaires, hover)
Gris foncé : #212529 (texte principal)
Gris moyen : #6c757d (texte secondaire)
Gris clair : #f8f9fa (arrière-plans)
Rouge : #dc3545 (alertes, annulations)
Jaune : #ffc107 (avertissements, boutons secondaires)

Typographie

Titres : Montserrat

H1 : Bold, 32px/2rem
H2 : SemiBold, 24px/1.5rem
H3 : Medium, 20px/1.25rem


Corps de texte : Roboto

Texte principal : Regular, 16px/1rem
Texte secondaire : Light, 14px/0.875rem




🚀 Lancement
Backend

Le backend s'exécute automatiquement dès qu'un fichier .php est accédé via le navigateur.
Lancer le projet en visitant :
http://localhost/ecoride/


Frontend

Le site utilise Bootstrap pour un rendu responsive.
Aucun framework JS requis.


🔐 Options de sécurité & debug

Prévention des injections SQL avec requêtes préparées (PDO).
Protection XSS via htmlspecialchars().
Vérification d'identité centralisée sur chaque page restreinte via le système auth_check.php.
Pour le debug PHP :

phpini_set('display_errors', 1);
error_reporting(E_ALL);

🔍 Fonctionnalités de l'administration

Création de comptes employés
Suspension de comptes utilisateurs
Affichage de graphiques via Chart.js

Nombre de trajets par jour
Crédits gagnés




💰 Système de crédits

Chaque utilisateur reçoit 20 crédits à l'inscription
Participation à un trajet : coûte 2 crédits
Création d'un trajet : coûte 2 crédits
Les crédits sont remboursés en cas d'annulation


📱 Compatibilité mobile

Interface entièrement responsive grâce à Bootstrap
Optimisée pour différentes tailles d'écran (desktop, tablette, mobile)
Organisation adaptative des éléments sur petit écran


📚 Remarques

Le projet ne dépend d'aucun framework externe pour mettre en avant une logique 100% native PHP.
Tous les scripts JS ou CSS sont chargés via CDN pour limiter la configuration.


Bonnes pratiques Git utilisées

Branche principale : main
Branche de développement : dev
Fonctionnalités développées sur des branches feature/<nom>, ex : feature/recherche

Processus utilisé :

Développement d'une fonctionnalité dans feature/...
Merge vers dev après test
Merge de dev vers main une fois l'application validée

Ce workflow permet un développement structuré, évitant les conflits et facilitant la validation étape par étape.
<small>EcoRide - Projet ECF 2025 🌱</small>