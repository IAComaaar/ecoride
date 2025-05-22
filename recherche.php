<?php
session_start();
require_once 'connexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$trajets = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $depart = htmlspecialchars($_POST['depart']);
    $arrivee = htmlspecialchars($_POST['arrivee']);
    $date = $_POST['date'];

    $sql = "SELECT c.*, v.energie, u.note_moyenne,
    TIMESTAMPDIFF(MINUTE, c.heure_depart, c.heure_arrivee) AS duree
            FROM covoiturage c
            JOIN utilisateur u ON c.id_chauffeur = u.id_user
            JOIN vehicule v ON c.id_vehicule = v.id_vehicule
            WHERE c.ville_depart = :depart
              AND c.ville_arrivee = :arrivee
              AND c.date = :date";

    $params = [
        ':depart' => $depart,
        ':arrivee' => $arrivee,
        ':date' => $date
    ];

    if (!empty($_POST['eco'])) {
        $sql .= " AND v.energie = 'électrique'";
    }

    if (!empty($_POST['prix_max'])) {
        $sql .= " AND c.prix <= :prix_max";
        $params[':prix_max'] = $_POST['prix_max'];
    }

    if (!empty($_POST['duree_max'])) {
        $sql .= " AND TIMESTAMPDIFF(MINUTE, c.heure_depart, c.heure_arrivee) <= :duree_max";
        $params[':duree_max'] = $_POST['duree_max'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Recherche</title>

<!-- Bootstrap -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
    <a class="navbar-brand" href="index.php">EcoRide</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="/index.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/recherche.php">Covoiturages</a>
            </li>
            
            <?php if (isset($_SESSION['id_user'])) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="/mon-espace.php">Mon compte</a>
                </li>
                
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login.php">Connexion</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
 </nav>

 <!-- Formulaire de recherche -->
  <div class="container mt-5">
    <h1 class="text-center mb-4">Rechercher un covoiturage</h1>

    <form method="POST" action="/recherche.php">
        <div class="row mb-3">
            <div class="col">
                <label for="depart" class="form-label">Ville de départ</label>
                <input type="text" class="form-control" id="depart" name="depart" placeholder="Ex: Paris" required>
            </div>
            <div class="col">
            <label for="arrivee" class="form-label">Ville d'arrivée</label>
            <input type="text" class="form-control" id="arrivee" name="arrivee" placeholder="Ex: Lyon" required>
            </div>
            <div class="col">
            <label for="date" class="form-label">Date de départ</label>
            <input type="date" class="form-control" id="date" name="date" required>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3">
        <label for="eco" class="form-label">Trajet écologique uniquement</label><br>
        <input type="checkbox" class="form-check-input" id="eco" name="eco">
    </div>
    <div class="col-md-3">
        <label for="prix_max" class="form-label">Prix maximum (€)</label>
        <input type="number" class="form-control" id="prix_max" name="prix_max" min="0" placeholder="Ex: 15">
    </div>
    <div class="col-md-3">
        <label for="duree_max" class="form-label">Durée maximum (en minutes)</label>
        <input type="number" class="form-control" id="duree_max" name="duree_max" min="1" placeholder="Ex: 120">
    </div>
    <div class="col-md-3">
        <label for="note_min" class="form-label">Note minimale du chauffeur</label>
        <input type="number" class="form-control" id="note_min" name="note_min" step="0.1" min="0" max="5" placeholder="Ex: 4,5">
    </div>
</div>
        <button type="submit" class="btn btn-success mt-4">Rechercher</button>
    </form>

    <?php if (!empty($trajets)) : ?>
        <h2 class="mt-5 text-center">Résultats :</h2>
        <div class="row mt-3">
            <?php foreach ($trajets as $trajet) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"> 🚗 <?php echo htmlspecialchars($trajet['ville_depart']); ?> → <?php echo htmlspecialchars($trajet['ville_arrivee']); ?></h5>
                            <p class="card-text mt-2">
                            📅 <strong>Date : </strong> <?php echo htmlspecialchars($trajet['date']); ?><br>
                            💰 <strong>Prix : </strong> <?php echo htmlspecialchars($trajet['prix']); ?> €<br>
                            🧍‍♂️ <strong>Places disponibles :</strong> <?php echo htmlspecialchars($trajet['nb_places']); ?><br>
                            ⭐ <strong>Note chauffeur :</strong> 
                            <?php if ($trajet['note_moyenne']): ?>
                                <?php echo number_format($trajet['note_moyenne'], 1); ?>/5
                            <?php else: ?> 
                                Nouveau
                            <?php endif; ?>
                            </p>
                            <p class="card-text">
                                ⏱️ <strong>Durée estimée :</strong> <?php echo $trajet['duree']; ?> minutes
                            </p>

                            <?php if (!empty($trajet['eco']) && $trajet['eco']) : ?>
                                <span class="badge bg-success">Éco-responsable 🌱</span>
                            <?php endif; ?>

                        <div class="d-grid mt-3">
                            <a href="voir.php?id=<?php echo $trajet['id_covoiturage']; ?>" class="btn btn-success">Voir le trajet</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
 <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST") : ?>
    <p class="text-center mt-5">Aucun covoiturage disponible pour ces critères.</p>
<?php endif; ?>
  </div>
    
  <!--Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>