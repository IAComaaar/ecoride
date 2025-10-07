# Base de données EcoRide

## Import local

### Via phpMyAdmin
1. Créer la base : `CREATE DATABASE ecoride;`
2. Importer : `database/sql/ecoride.sql`

### Via ligne de commande
```bash
mysql -u root ecoride < database/sql/ecoride.sql
Comptes de test
RôleEmailMot de 
passeAdminadmin@ecoride.frpasswordEmployéemploye@ecoride.frpasswordUtilisateurtest@test.frpassword
Structure
7 tables :

utilisateur : Comptes utilisateurs
vehicule : Véhicules des chauffeurs
preferences : Préférences chauffeurs
covoiturage : Trajets (avec colonne etat)
participation : Réservations passagers
avis : Avis sur chauffeurs
signalements : Trajets problématiques
