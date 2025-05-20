# 🌱 EcoRide – Mise en place

## Préambule

Ce projet fonctionne avec un environnement local de type **XAMPP** incluant **Apache**, **PHP 8+**, et **MySQL**.  
Aucun gestionnaire de paquets comme `npm` ou `composer` n’est requis.

---

## Dépendances

Aucune dépendance externe à installer. Tous les scripts front-end utilisent **Bootstrap**, et les graphiques sont générés avec **Chart.js** via un CDN.

---

## Base de données & Authentification

1. Placez le dossier `/ecoride` dans `htdocs/` de XAMPP.
2. Démarrez Apache et MySQL via le panneau de contrôle XAMPP.
3. Dans `phpMyAdmin`, créez une base de données nommée `ecoride`.
4. Importez le fichier SQL fourni (`bdd/ecoride.sql`).

5. Ouvrez le fichier `connexion.php` et vérifiez la configuration suivante :
```php
$pdo = new PDO('mysql:host=localhost;dbname=ecoride', 'root', '');
```

> L’authentification repose sur une gestion de rôles (`utilisateur`, `employe`, `admin`) via **sessions PHP**, avec stockage sécurisé des mots de passe grâce à `password_hash()`.

---

## Lancement

### Backend

- Le backend s'exécute automatiquement dès qu'un fichier `.php` est accédé via le navigateur.
- Lancer le projet en visitant :  
  ```
  http://localhost/ecoride/
  ```

### Frontend

- Le site utilise **Bootstrap** pour un rendu responsive.
- Aucun framework JS requis.

---

## Options de sécurité & debug

- Prévention des injections SQL avec **requêtes préparées (PDO)**.
- Protection XSS via `htmlspecialchars()`.
- Vérification d’identité sur chaque page restreinte via `$_SESSION`.
- Pour le debug PHP :
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

## Fonctionnalités de l’administration

- Création de comptes employés
- Suspension de comptes utilisateurs
- Affichage de graphiques via **Chart.js**
  - Nombre de trajets par jour
  - Crédits gagnés

---

## Remarques

- Le projet ne dépend d'aucun framework externe pour mettre en avant une logique **100% native PHP**.
- Tous les scripts JS ou CSS sont chargés via CDN pour limiter la configuration.

---

<small>EcoRide - Projet ECF 2025 🌱</small>
