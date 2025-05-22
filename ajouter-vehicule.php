<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'auth-check.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Afin de vérifier si connecté
if (!isset($_SESSION['id_user'])) {
    die("Accès refusé : vous devez être connecté pour ajouter un véhicule.");
}

$id_user = $_SESSION['id_user'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation des données
    $marque = htmlspecialchars($_POST['marque']);
    $modele = htmlspecialchars($_POST['modele']);
    $couleur = htmlspecialchars($_POST['couleur']);
    $immatriculation = htmlspecialchars($_POST['plaque']);
    $energie = htmlspecialchars($_POST['energie']);

    // Insertion en base
    $stmt = $pdo->prepare("INSERT INTO vehicule 
        (id_proprietaire, marque, modele, couleur, immatriculation, energie)
        VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $id_user,
        $marque,
        $modele,
        $couleur,
        $immatriculation,
        $energie
    ]);

    $message = "Véhicule ajouté avec succès ! 🚘";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un véhicule</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">EcoRide</a>
        <ul class="navbar-nav ms-auto flex-row gap-3">
        <?php
        $current_page = basename($_SERVER['PHP_SELF']);
        $pages_avec_mon_compte = ['ajouter-vehicule.php', 'proposer-trajet.php'];
        if (in_array($current_page, $pages_avec_mon_compte)) {
            echo '<li class="nav-item">
              <a class="nav-link" href="/ecoride/mon-espace.php">Mon compte</a>
          </li>';}
          ?>
          </ul>
    </div>
</nav>
</ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Ajouter un véhicule</h2>

    <?php if ($message): ?>
        <div class="alert alert-success text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="marque" class="form-label">Marque</label>
                <input type="text" class="form-control" name="marque" required>
            </div>

            <div class="mb-3">
            <label for="modele" class="form-label">Modèle</label>
            <input type="text" class="form-control" name="modele" required>
            </div>

            <div class="mb-3">
            <label for="couleur" class="form-label">Couleur</label>
            <input type="text" class="form-control" name="couleur" required>
            </div>

            <div class="mb-3">
            <label for="plaque" class="form-label">Plaque d'immatriculation</label>
            <input type="text" class="form-control" name="plaque" required>
            </div>

            <div class="mb-3">
            <label for="energie" class="form-label">Énergie</label>
            <select name="energie" class="form-select" required>
                <option value="essence">Essence</option>
                <option value="diesel">Diesel</option>
                <option value="électrique">Électrique</option>
                <option value="hybride">Hybride</option>
            </select>
            </div>

        <button type="submit" class="btn btn-success w-100">Ajouter le véhicule</button>
        </form>
</div>
    
</body>
</html>