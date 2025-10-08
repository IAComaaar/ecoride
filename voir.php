<?php
session_start();
require_once 'connexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_GET['id'])) {
    die('Erreur : ID du covoiturage manquant.');
}

$id = intval($_GET['id']);

$sql = "SELECT c.*, u.pseudo, u.email, u.note_moyenne, v.marque, v.modele, v.energie
        FROM covoiturage c
        JOIN utilisateur u ON c.id_chauffeur = u.id_user
        JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        WHERE c.id_covoiturage = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$trajet = $stmt->fetch(PDO::FETCH_ASSOC);

$message = "";

// Traitement du bouton participer
if (isset($_POST['participer']) && isset($_SESSION['id_user'])) {
    $userId = $_SESSION['id_user'];
    
    $stmt = $pdo->prepare("SELECT credit FROM utilisateur WHERE id_user = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if ($user['credit'] >= 2) {
        $stmt = $pdo->prepare("SELECT * FROM participation WHERE id_user = ? AND id_covoiturage = ?");
        $stmt->execute([$userId, $id]);

        if ($stmt->rowCount() == 0) {
            $pdo->prepare("INSERT INTO participation (id_user, id_covoiturage, status) VALUES (?, ?, 'confirmé')")
                ->execute([$userId, $id]);
            $pdo->prepare("UPDATE utilisateur SET credit = credit - 2 WHERE id_user = ?")
                ->execute([$userId]);
            $pdo->prepare("UPDATE covoiturage SET nb_places = nb_places - 1 WHERE id_covoiturage = ?")
                ->execute([$id]);
            
            $message = "<div class='alert alert-success text-center'>🎉 Trajet réservé avec succès ! <a href='/mon-espace.php' class='btn btn-sm btn-success ms-2'>Voir mes réservations</a></div>";
        } else {
            $message = "<div class='alert alert-info text-center'>Vous êtes déjà inscrit à ce trajet.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>Crédits insuffisants.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du trajet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="/ecoride/index.php">EcoRide</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/ecoride/index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/ecoride/recherche.php">Covoiturages</a>
                    </li>
                    <?php if (isset($_SESSION['id_user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/ecoride/mon-espace.php">Mon compte</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/ecoride/login.php">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Détail du trajet</h1>

        <!-- Message de confirmation -->
        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">
                    🚗 <?php echo htmlspecialchars($trajet['ville_depart']); ?> ➔ <?php echo htmlspecialchars($trajet['ville_arrivee']); ?>
                </h5>
                <p class="card-text">
                    📅 <strong>Date :</strong> <?php echo htmlspecialchars($trajet['date']); ?><br>
                    🕒 <strong>Heure départ :</strong> <?php echo htmlspecialchars($trajet['heure_depart']); ?><br>
                    🕒 <strong>Heure arrivée :</strong> <?php echo htmlspecialchars($trajet['heure_arrivee']); ?><br>
                    💰 <strong>Prix :</strong> <?php echo htmlspecialchars($trajet['prix']); ?> €<br>
                    🧍‍♂️ <strong>Places restantes :</strong> <?php echo htmlspecialchars($trajet['nb_places']); ?><br>
                    🚘 <strong>Véhicule :</strong> <?php echo htmlspecialchars($trajet['marque']) . ' ' . htmlspecialchars($trajet['modele']); ?><br>
                    ⚡ <strong>Énergie :</strong> <?php echo htmlspecialchars($trajet['energie']); ?><br>
                    👤 <strong>Chauffeur :</strong> <?php echo htmlspecialchars($trajet['pseudo']); ?><br>
                    ⭐ <strong>Note :</strong> 
                    <?php if ($trajet['note_moyenne']): ?>
                        <?php echo number_format($trajet['note_moyenne'], 1); ?>/5 
                        <?php 
                        $stars = round($trajet['note_moyenne']);
                        for($i = 1; $i <= 5; $i++) {
                            echo $i <= $stars ? '⭐' : '☆';
                        }
                        ?>
                    <?php else: ?>
                        Nouveau chauffeur
                    <?php endif; ?><br>
                    📧 <strong>Contact :</strong> <?php echo htmlspecialchars($trajet['email']); ?><br>

                    <?php if (strtolower($trajet['energie']) === 'électrique') : ?>
                        <span class="badge bg-success">Éco-responsable 🌱</span>
                    <?php endif; ?>
                </p>

                <!-- Boutons de participation -->
                <?php if (isset($_SESSION['id_user'])): ?>
                    <!-- Utilisateur connecté -->
                    <form method="POST" class="mt-4">
                        <button type="submit" name="participer" class="btn btn-success w-100">Participer à ce trajet</button>
                    </form>
                <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <div class="mt-4">
                    <a href="/ecoride/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-success">Se connecter pour participer</a>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="/ecoride/recherche.php" class="btn btn-outline-secondary">⬅ Retour à la recherche</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>