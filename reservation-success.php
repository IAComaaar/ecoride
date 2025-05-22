<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['trajet_id'])) {
    header('Location: mon-espace.php');
    exit;
}

$trajet_id = intval($_GET['trajet_id']);
$userId = $_SESSION['id_user'];
$message = "";

// FAIRE LA RÉSERVATION
try {
    // Vérifier les crédits
    $stmt = $pdo->prepare("SELECT credit FROM utilisateur WHERE id_user = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user['credit'] >= 2) {
        // Vérifier si pas déjà inscrit
        $stmt = $pdo->prepare("SELECT * FROM participation WHERE id_user = ? AND id_covoiturage = ?");
        $stmt->execute([$userId, $trajet_id]);
        
        if ($stmt->rowCount() == 0) {
            // Effectuer la réservation
            $pdo->prepare("INSERT INTO participation (id_user, id_covoiturage, status) VALUES (?, ?, 'confirmé')")
                ->execute([$userId, $trajet_id]);
            $pdo->prepare("UPDATE utilisateur SET credit = credit - 2 WHERE id_user = ?")
                ->execute([$userId]);
            $pdo->prepare("UPDATE covoiturage SET nb_places = nb_places - 1 WHERE id_covoiturage = ?")
                ->execute([$trajet_id]);
            
            $message = "success";
        } else {
            $message = "already_booked";
        }
    } else {
        $message = "no_credits";
    }
} catch (Exception $e) {
    $message = "error";
}

// Récupérer les infos du trajet
$stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE id_covoiturage = ?");
$stmt->execute([$trajet_id]);
$trajet = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - EcoRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="/index.php">EcoRide</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($message === "success"): ?>
                    <div class="alert alert-success text-center">
                        <h2>🎉 Trajet réservé avec succès !</h2>
                        <p>Votre réservation a été confirmée pour le trajet :</p>
                        <h4><?php echo htmlspecialchars($trajet['ville_depart']); ?> → <?php echo htmlspecialchars($trajet['ville_arrivee']); ?></h4>
                        <p><strong>Date :</strong> <?php echo htmlspecialchars($trajet['date']); ?></p>
                        <p><strong>Prix :</strong> <?php echo htmlspecialchars($trajet['prix']); ?> €</p>
                    </div>
                <?php elseif ($message === "already_booked"): ?>
                    <div class="alert alert-info text-center">
                        <h2>ℹ️ Déjà réservé</h2>
                        <p>Vous êtes déjà inscrit à ce trajet.</p>
                    </div>
                <?php elseif ($message === "no_credits"): ?>
                    <div class="alert alert-danger text-center">
                        <h2>❌ Crédits insuffisants</h2>
                        <p>Il vous faut au moins 2 crédits pour réserver un trajet.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger text-center">
                        <h2>❌ Erreur</h2>
                        <p>Une erreur est survenue lors de la réservation.</p>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="/mon-espace.php" class="btn btn-success btn-lg">Voir mes réservations</a>
                    <a href="/recherche.php" class="btn btn-outline-secondary btn-lg ms-2">Nouveau trajet</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>