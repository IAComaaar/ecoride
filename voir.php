<?php
session_start();
require_once 'connexion.php';  // Ne gardez qu'une seule connexion

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_GET['id'])) {
    die('Erreur : ID du covoiturage manquant.');
}

$id = intval($_GET['id']);

$sql = "SELECT c.*, u.pseudo, u.email, v.marque, v.modele, v.energie
        FROM covoiturage c
        JOIN utilisateur u ON c.id_chauffeur = u.id_user
        JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        WHERE c.id_covoiturage = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$trajet = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['participer'])) {
    if(!isset($_SESSION['id_user'])) {
        // Rediriger vers login avec l'ID du trajet en paramètre URL
        header('Location: login.php?redirect=trajet&id_trajet=' . $id);
        exit;
    } else {
        // L'utilisateur est connecté, continuer avec le traitement normal
        $userId = $_SESSION['id_user'];

        // Vérifier les crédits
        $stmt = $pdo->prepare("SELECT credit FROM utilisateur WHERE id_user = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['credit'] < 2) {
            echo "<div class='alert alert-danger text-center'>Crédits insuffisants.</div>";
        } elseif ($trajet['nb_places'] < 1) {
            echo "<div class='alert alert-danger text-center'>Plus aucune place disponible.</div>";
        } else {
            // Vérifier si l'utilisateur est déjà inscrit
            $stmt = $pdo->prepare("SELECT * FROM participation WHERE id_user = ? AND id_covoiturage = ?");
            $stmt->execute([$userId, $id]);

            if ($stmt->rowCount() > 0) {
                echo "<div class='alert alert-info text-center'>Vous êtes déjà inscrit à ce trajet.</div>";
            } else {
                // Ajouter la participation
                $pdo->prepare("INSERT INTO participation (id_user, id_covoiturage, status, confirmation)
                VALUES (?, ?, 'confirmé', 1)")->execute([$userId, $id]);

                // Déduire les crédits
                $pdo->prepare("UPDATE utilisateur SET credit = credit - 2 WHERE id_user = ?")->execute([$userId]);
                
                // Diminuer le nombre de places
                $pdo->prepare("UPDATE covoiturage SET nb_places = nb_places - 1 WHERE id_covoiturage = ?")->execute([$id]);

                echo "<div class='alert alert-success text-center'>Participation confirmée ✅</div>";
            }
        }
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
     <nav class="navbar navbar-dark bg-sucess"
        <div class="container-fluid">
           <a class="navbar-brand" href="/index.php">EcoRide</a>
        </div>
    </nav>

    <div class="container mt-5">
    <h1 class="text-center mb-4">Détail du trajet</h1>

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
                📧 <strong>Contact :</strong> <?php echo htmlspecialchars($trajet['email']); ?><br>

                <?php if (strtolower($trajet['energie']) === 'électrique') : ?>
                    <span class="badge bg-success">Éco-responsable 🌱</span>
                <?php endif; ?>
            </p>

            <form method="POST" class="mt-4">
                <button type="submit" name="participer" class="btn btn-success w-100">Participer à ce trajet</button>
            </form>

            <div class="text-center mt-3">
                <a href="/recherche.php" class="btn btn-outline-secondary">⬅ Retour à la recherche</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>