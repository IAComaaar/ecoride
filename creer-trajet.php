<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'auth-check.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pour simuler un utilisateur connecté (à retirer quand le système login sera prêt)
$_SESSION['id_user'] = 1;

$id_user = $_SESSION['id_user'];

$message = ""; // Message de confirmation si trajet créé

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation des champs
    $depart = htmlspecialchars($_POST['ville_depart']);
    $arrivee = htmlspecialchars($_POST['ville_arrivee']);
    $date = $_POST['date'];
    $heure_depart = $_POST['heure_depart'];
    $heure_arrivee = $_POST['heure_arrivee'];
    $prix = floatval($_POST['prix']);
    $nb_places = intval($_POST['nb_places']);
    $id_vehicule = intval($_POST['id_vehicule']);

    // Requête d'insertion
    $stmt = $pdo->prepare("INSERT INTO covoiturage 
        (id_chauffeur, id_vehicule, ville_depart, ville_arrivee, date, heure_depart, heure_arrivee, prix, nb_places) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $id_user,
        $id_vehicule,
        $depart,
        $arrivee,
        $date,
        $heure_depart,
        $heure_arrivee,
        $prix,
        $nb_places
    ]);

    $message = "🚗 Trajet ajouté avec succès !";
}

// Afin de vérifié que l'utilisateur est bien connecté
if(!isset($_SESSION['id_user'])) {
    die("Accès refusé : vous devez être connecté pour créer un trajet.");
}

// Afin de récupérer les véhicules enregistrés par l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM vehicule WHERE id_user = ?");
$stmt->execute([$id_user]);
$vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = ""; //Afin d'afficher plus tard
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un trajet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">EcoRide</a>
        <ul class="navbar-nav ms-auto flex-row gap-3">
        <?php
        $current_page = basename($_SERVER['PHP_SELF']);
        $pages_avec_mon_compte = ['ajouter-vehicule.php', 'creer-trajet.php'];
        if (in_array($current_page, $pages_avec_mon_compte)) {
            echo '<li class="nav-item">
              <a class="nav-link" href="/ecoride/mon-espace.php">Mon compte</a>
          </li>';}
          ?>
          </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Créer un nouveau trajet</h2>

    <?php if ($message): ?>
        <div class="alert alert-success text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (count($vehicules) > 0): ?>
    <form method="POST">
        <div class="mb-3">
            <label for="ville_depart" class="form-label">Ville de départ</label>
            <input type="text" class="form-control" name="ville_depart" required>
        </div>

        <div class="mb-3">
            <label for="ville_arrivee" class="form-label">Ville d’arrivée</label>
            <input type="text" class="form-control" name="ville_arrivee" required>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" required>
        </div>

        <div class="mb-3">
            <label for="heure_depart" class="form-label">Heure de départ</label>
            <input type="time" class="form-control" name="heure_depart" required>
        </div>

        <div class="mb-3">
            <label for="heure_arrivee" class="form-label">Heure d’arrivée</label>
            <input type="time" class="form-control" name="heure_arrivee" required>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix (€)</label>
            <input type="number" class="form-control" name="prix" step="0.5" required>
        </div>

        <div class="mb-3">
            <label for="nb_places" class="form-label">Nombre de places</label>
            <input type="number" class="form-control" name="nb_places" min="1" required>
        </div>

        <div class="mb-3">
            <label for="id_vehicule" class="form-label">Choisir un véhicule</label>
            <select name="id_vehicule" class="form-select" required>
                <?php foreach ($vehicules as $v): ?>
                    <option value="<?php echo $v['id_vehicule']; ?>">
                        <?php echo $v['marque'] . ' ' . $v['modele'] . ' (' . $v['energie'] . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Créer le trajet</button>
    </form>
<?php else: ?>
    <div class="alert alert-warning text-center">
        Vous devez enregistrer un véhicule avant de créer un trajet.
    </div>
<?php endif; ?>
</div>
</body>
</html>

</div>
    
</body>
</html>