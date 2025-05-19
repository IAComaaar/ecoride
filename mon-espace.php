<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'connexion.php';

// Pour simuler un utilisateur connecté (à retirer quand le système login sera prêt)
$_SESSION['id_user'] = 1;

if (isset($_POST['annuler']) && isset($_POST['annuler_id'])) {
    $idCovoiturage = intval($_POST['annuler_id']);
    $idUser = $_SESSION['id_user'];

// Pour marquer comme annulé
    $stmt = $pdo->prepare("UPDATE participation SET status = 'annulé' WHERE id_user = ? AND id_covoiturage = ?");
    $stmt->execute([$idUser, $idCovoiturage]);

// Pour rembourser les crédits
    $stmt = $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE id_user = ?");
    $stmt->execute([$idUser]);

// Pour reemettre une place dans le covoiturage
    $stmt = $pdo->prepare("UPDATE covoiturage SET nb_places = nb_places + 1 WHERE id_covoiturage = ?");
    $stmt->execute([$idCovoiturage]);

    $annulationMessage = "<div class='alert alert-success text-center'>Trajet annulé. Vos crédits ont été remboursés ✅</div>";
}
// Afin de démarrer un trajet
if (isset($_POST['demarrer']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);
    $pdo->prepare("UPDATE covoiturage SET etat = 'en cours' WHERE id_covoiturage = ?")->execute([$idTrajet]);
    $annulationMessage = "<div class='alert alert-warning text-center'>Trajet démarré ! 🚗💨</div>";
}

// Et afin de le terminer un trajet
if (isset($_POST['terminer']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);
    $pdo->prepare("UPDATE covoiturage SET etat = 'terminé' WHERE id_covoiturage = ?")->execute([$idTrajet]);

// Afin de notifier les passagers
    $annulationMessage = "<div class='alert alert-success text-center'>Trajet terminé ! Les passagers vont être notifiés ✅</div>";
}


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pour vérifier si l'utilisateur est connecté
if(!isset($_SESSION['id_user'])) {
    die("Vous devez être connecté pour accéder à votre espace.");
}

$userId = $_SESSION['id_user'];

if (isset($_POST['annuler_trajet']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);

    // Récupérer les participants au trajet
    $stmt = $pdo->prepare("SELECT id_user FROM participation WHERE id_covoiturage = ?");
    $stmt->execute([$idTrajet]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Marquer les participations comme annulées + rembourser
    foreach ($participants as $participant) {
        $idPassager = $participant['id_user'];

        $pdo->prepare("UPDATE participation SET status = 'annulé' WHERE id_user = ? AND id_covoiturage = ?")
            ->execute([$idPassager, $idTrajet]);

        $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE id_user = ?")
            ->execute([$idPassager]);
    }

    // Supprimer le trajet ou le désactiver
    $pdo->prepare("DELETE FROM covoiturage WHERE id_covoiturage = ?")->execute([$idTrajet]);

    $annulationMessage = "<div class='alert alert-info text-center'>Trajet annulé. Les passagers ont été notifiés (simulation). ✅</div>";
}

// Pour récupérer les infos de l'utilisateur
$stmt = $pdo->prepare("SELECT pseudo, email, credit FROM utilisateur WHERE id_user = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Pour récupérer les participations
$stmt = $pdo->prepare("SELECT c.*, p.status 
                       FROM participation p
                       JOIN covoiturage c ON c.id_covoiturage = p.id_covoiturage 
                       WHERE p.id_user = ?");
$stmt->execute([$userId]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour récupérer les trajets créés par l'utilisateur (en tant que chauffeur)
$stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE id_chauffeur = ?");
$stmt->execute([$userId]);
$mesTrajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mon espace 🌱</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<!-- Navbar -->
 <nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ecoride/index.php">EcoRide</a>
        <div class="text-end mb-3">
            <a href="/ecoride/deconnexion.php" class="btn btn-outline-danger">Déconnexion</a>
        </div>
    </div>
 </nav>

 <div class="container mt-5">
    <?php if (!empty($annulationMessage)) echo $annulationMessage; ?>
    <h1 class="mb-4 text-center">Bienvenue dans votre espace</h1>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Vos informations 👤</h5>
            <p><strong>Pseudo :</strong> <?php echo htmlspecialchars($user['pseudo']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Crédits restants : </strong><?php echo $user['credit']; ?></p>
        </div>
    </div>

    <h3>Vos trajets réservés 🧾</h3>
    <?php if (count($reservations) > 0): ?>
        <div class="row mt-3">
            <?php foreach ($reservations as $trajet): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($trajet['ville_depart']); ?> ➔ <?php echo htmlspecialchars($trajet['ville_arrivee']); ?></h5>
                            <p class="card-text">
                                Date 📅 : <?php echo htmlspecialchars($trajet['date']); ?><br>
                                Prix 💰 : <?php echo htmlspecialchars($trajet['prix']); ?> €<br>
                                Places restantes 🧍‍♂️ : <?php echo $trajet['nb_places']; ?><br>
                                Statut 📌 : <span class="badge bg-<?php echo $trajet['status'] === 'annulé' ? 'danger' : 'success'; ?>"><?php echo $trajet['status']; ?></span>
                            </p>
                            <?php if ($trajet['status'] === 'confirmé') : ?>
                                <form method="POST" class="mt-2">
                                    <input type="hidden" name="annuler_id" value="<?php echo $trajet['id_covoiturage']; ?>">
                                    <button type="submit" name="annuler" class="btn btn-danger btn-sm">Annuler ce trajet</button>
                                </form>
                                <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Aucun trajet réservé pour le moment.</p>
    <?php endif; ?>

<div class="mb-4 text-end">
    <a href="/ecoride/ajouter-vehicule.php" class="btn btn-outline-primary btn-sm">Ajouter un véhicule ➕</a>
    <a href="/ecoride/creer-trajet.php" class="btn btn-outline-success btn-sm">Proposer un trajet ➕</a>
</div>

    <hr class="my-5">

    <h3> Vos trajets créés 🚘</h3>
    <?php if (count($mesTrajets) > 0): ?>
        <div class="row mt-3">
        <?php foreach ($mesTrajets as $trajet): ?>
    <div class="col-md-6 mb-4">
        <div class="card border-secondary">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($trajet['ville_depart']); ?> ➔ <?php echo htmlspecialchars($trajet['ville_arrivee']); ?></h5>
                <p class="card-text">
                    Date 📅 : <?php echo htmlspecialchars($trajet['date']); ?><br>
                    Heure 🕒 : <?php echo htmlspecialchars($trajet['heure_depart']); ?> ➔ <?php echo htmlspecialchars($trajet['heure_arrivee']); ?><br>
                    Prix 💰 : <?php echo htmlspecialchars($trajet['prix']); ?> €<br>
                    Places disponibles 🧍‍♂️ : <?php echo $trajet['nb_places']; ?>
                </p>
                <!-- Affichage de l’état -->
                <p><strong>État :</strong> <?php echo htmlspecialchars($trajet['etat']); ?></p>
                <!-- Boutons selon l’état -->
                <?php if ($trajet['etat'] === 'non démarré') : ?>
                    <form method="POST" class="mt-2">
                        <input type="hidden" name="id_trajet" value="<?php echo $trajet['id_covoiturage']; ?>">
                        <button type="submit" name="demarrer" class="btn btn-warning btn-sm">Démarrer le trajet</button>
                    </form>
                <?php elseif ($trajet['etat'] === 'en cours') : ?>
                    <form method="POST" class="mt-2">
                        <input type="hidden" name="id_trajet" value="<?php echo $trajet['id_covoiturage']; ?>">
                        <button type="submit" name="terminer" class="btn btn-success btn-sm">Arrivée à destination</button>
                    </form>
                <?php endif; ?>

                <!-- 🗑️ Bouton d'annulation -->
                <form method="POST" class="mt-2">
                    <input type="hidden" name="id_trajet" value="<?php echo $trajet['id_covoiturage']; ?>">
                    <button type="submit" name="annuler_trajet" class="btn btn-danger btn-sm">Annuler ce trajet</button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Aucun trajet créé pour l’instant.</p>
    <?php endif; ?>
 </div>
</body>
</html>