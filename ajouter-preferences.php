<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'connexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simuler un utilisateur connecté (à retirer une fois le login terminé)
$_SESSION['id_user'] = 1;

if (!isset($_SESSION['id_user'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

$id_user = $_SESSION['id_user'];
$message = "";

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fumeur = isset($_POST['fumeur']) ? 1 : 0;
    $animaux = isset($_POST['animaux']) ? 1 : 0;
    $autre = htmlspecialchars($_POST['autre']);

// Afin de vérifie si une préférence existe déjà pour cet utilisateur
    $check = $pdo->prepare("SELECT * FROM preference WHERE id_user = ?");
    $check->execute([$id_user]);

    if ($check->rowCount() > 0) {
// Mise à jour si préférence existante
        $stmt = $pdo->prepare("UPDATE preference SET fumeur = ?, animaux = ?, autre = ? WHERE id_user = ?");
        $stmt->execute([$fumeur, $animaux, $autre, $id_user]);
        $message = "🔄 Préférences mises à jour avec succès.";
    } else {
// Insertion sinon
        $stmt = $pdo->prepare("INSERT INTO preference (id_user, fumeur, animaux, autre) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_user, $fumeur, $animaux, $autre]);
        $message = "✅ Préférences enregistrées.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes préférences</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ecoride/index.php">EcoRide</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Définir vos préférences de conducteur</h2>

    <?php if ($message): ?>
        <div class="alert alert-success text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="fumeur" name="fumeur">
            <label class="form-check-label" for="fumeur">J'accepte les fumeurs</label>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="animaux" name="animaux">
            <label class="form-check-label" for="animaux">J'accepte les animaux</label>
        </div>

        <div class="mb-3">
            <label for="autre" class="form-label">Autres préférences</label>
            <textarea class="form-control" id="autre" name="autre" rows="3" placeholder="Ex : pas de clim, pas de musique, etc..."></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Enregistrer mes préférences</button>
    </form>
</div>

</body>
</html>

