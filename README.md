# EcoRide 🌿  
**Projet ECF 2025** – Application de covoiturage éco-responsable

## 🧠 Concept

**EcoRide** est une plateforme web de covoiturage local, conçue pour encourager une mobilité durable et collaborative.  
Les utilisateurs peuvent créer ou rejoindre des trajets en échange de crédits. Un système d’authentification avec rôles (utilisateur, employé, admin) gère les permissions et fonctionnalités disponibles.

---

## 🧱 Stack technique

- **Backend** : PHP 8+ (sans framework)
- **Base de données** : MySQL
- **Frontend** : HTML, CSS, JavaScript (Bootstrap via CDN)
- **Graphiques** : [Chart.js](https://www.chartjs.org/) via CDN
- **Environnement** : Local via [XAMPP](https://www.apachefriends.org/index.html)
- **Gestion des utilisateurs** : Sessions PHP & `password_hash()`
- **Sécurité** :
  - Requêtes préparées (PDO) pour éviter les injections SQL
  - Protection XSS avec `htmlspecialchars()`

---

## ⚙️ Installation & Lancement

### Prérequis

- [XAMPP](https://www.apachefriends.org/index.html) installé avec :
  - Apache activé
  - MySQL activé
- PHP 8+

### Étapes

1. Cloner ou copier le dossier `/ecoride` dans le répertoire `htdocs/` de XAMPP.
2. Démarrer **Apache** et **MySQL** via le panneau XAMPP.
3. Accéder à [phpMyAdmin](http://localhost/phpmyadmin) :
   - Créer une base de données nommée `ecoride`
   - Importer le fichier SQL : `bdd/ecoride.sql`
4. Vérifier la configuration de connexion dans `connexion.php` :
   ```php
   $pdo = new PDO('mysql:host=localhost;dbname=ecoride', 'root', '');
   ```
5. Lancer le projet :  
   👉 [http://localhost/ecoride/](http://localhost/ecoride/)

---

## 📦 Dépendances

Aucune installation via npm ou composer requise. Tous les scripts sont chargés via CDN.

- [Bootstrap 5](https://getbootstrap.com/)
- [Chart.js](https://www.chartjs.org/)

---

## 🔐 Authentification

Système de rôles :
- **Utilisateur** : créer/rejoindre un trajet
- **Employé** : gestion de comptes
- **Admin** : supervision globale

Sessions PHP sécurisées avec `password_hash()` pour le stockage des mots de passe.

---

## 📊 Fonctionnalités principales

- Connexion / inscription sécurisée
- Gestion des trajets (création, participation, annulation)
- Système de **crédits** :
  - +20 crédits à l'inscription
  - -2 crédits pour chaque participation ou création
  - Crédits remboursés si annulation
- Tableau de bord administrateur :
  - Suspension de comptes
  - Statistiques (trajets par jour, crédits gagnés) via Chart.js

---

## 🎨 UI & Responsiveness

- Design 100% responsive avec **Bootstrap**
- Interface adaptée aux formats desktop, tablette et mobile
- Maquettes disponibles dans `doc/maquettes/Maquette.pdf`

### Typographie

- **Titres** : Montserrat  
  - H1 : Bold, 32px  
  - H2 : SemiBold, 24px  
  - H3 : Medium, 20px  
- **Texte** : Roboto  
  - Texte principal : 16px  
  - Texte secondaire : 14px  

---

## 🛠️ Débogage

Activer les erreurs PHP pendant le développement :
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

## 🧪 Workflow Git recommandé

- Branche principale : `main`
- Développement : `dev`
- Fonctionnalités : `feature/<nom>`

```bash
git checkout -b feature/<nom>
# après test
git merge feature/<nom> -> dev
# après validation finale
git merge dev -> main
```

---

## 📁 Structure utile

```
/ecoride/
│
├── bdd/               # Fichier SQL
├── doc/maquettes/     # Maquettes PDF
├── includes/          # Fichiers partagés (auth_check.php, connexion.php, etc.)
├── public/            # Pages accessibles
├── admin/             # Espace d'administration
├── assets/            # CSS / JS / images
```

---

## 📚 Remarques

Ce projet met l'accent sur une logique **100% native PHP**, sans frameworks.  
Idéal pour comprendre les bases du web dynamique avec un MVC simplifié.

---