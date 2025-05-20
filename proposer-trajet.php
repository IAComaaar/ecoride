<?php
session_start();
require_once 'auth-check.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Récupérer les véhicules de l’utilisateur
$stmt = $pdo->prepare("SELECT * FROM vehicule WHERE id_user = ?");
$stmt->execute([$id_user]);
$vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$success = "";
$error = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $depart = $_POST['depart'];
    $arrivee = $_POST['arrivee'];
    $date = $_POST['date'];
    $heure_depart = $_POST['heure_depart'];
    $heure_arrivee = $_POST['heure_arrivee'];
    $prix = $_POST['prix'];
    $nb_places = $_POST['nb_places'];
    $vehicule = $_POST['vehicule'];

    // Vérifier que le chauffeur a assez de crédits
    $check = $pdo->prepare("SELECT credit FROM utilisateur WHERE id_user = ?");
    $check->execute([$id_user]);
    $credit = $check->fetchColumn();

    if ($credit < 2) {
        $error = "Vous n'avez pas assez de crédits pour proposer un trajet.";
    } else {
        // Insertion du trajet
        $insert = $pdo->prepare("INSERT INTO covoiturage (ville_depart, ville_arrivee, date, heure_depart, heure_arrivee, prix, nb_places, id_vehicule, id_chauffeur)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$depart, $arrivee, $date, $heure_depart, $heure_arrivee, $prix, $nb_places, $vehicule, $id_user]);

        // Retirer 2 crédits
        $pdo->prepare("UPDATE utilisateur SET credit = credit - 2 WHERE id_user = ?")->execute([$id_user]);

        $success = "Trajet proposé avec succès !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Proposer un trajet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">EcoRide</a>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center">Proposer un trajet</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Ville de départ</label>
                <input type="text" class="form-control" name="depart" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ville d’arrivée</label>
                <input type="text" class="form-control" name="arrivee" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="date" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Heure de départ</label>
                <input type="time" class="form-control" name="heure_depart" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Heure d’arrivée</label>
                <input type="time" class="form-control" name="heure_arrivee" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Prix (€)</label>
                <input type="number" class="form-control" name="prix" required min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label">Nombre de places</label>
                <input type="number" class="form-control" name="nb_places" required min="1">
            </div>
            <div class="col-md-4">
                <label class="form-label">Véhicule</label>
                <select name="vehicule" class="form-select" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($vehicules as $v) : ?>
                        <option value="<?php echo $v['id_vehicule']; ?>">
                            <?php echo htmlspecialchars($v['marque']) . " - " . htmlspecialchars($v['modele']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Proposer le trajet</button>
    </form>
</div>

</body>
</html>
