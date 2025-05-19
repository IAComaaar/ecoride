<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'employe') {
    die("Accès réservé aux employés.");
}

// Valider un avis
if (isset($_POST['valider_avis'])) {
    $pdo->prepare("UPDATE avis SET valide = 1 WHERE id_avis = ?")->execute([$_POST['valider_avis']]);
}

// Refuser un avis
if (isset($_POST['refuser_avis'])) {
    $pdo->prepare("DELETE FROM avis WHERE id_avis = ?")->execute([$_POST['refuser_avis']]);
}

// Avis à valider
$avisStmt = $pdo->query("SELECT a.*, u.pseudo, c.ville_depart, c.ville_arrivee
                         FROM avis a
                         JOIN utilisateur u ON a.id_user = u.id_user
                         JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
                         WHERE a.valide = 0");

// Avis négatifs
$problems = $pdo->query("SELECT a.*, u.pseudo, u.email, c.ville_depart, c.ville_arrivee, c.date
                         FROM avis a
                         JOIN utilisateur u ON a.id_user = u.id_user
                         JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
                         WHERE a.note <= 2 AND a.valide = 1");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Employé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Espace Employé</span>
        <a href="deconnexion.php" class="btn btn-outline-light">Déconnexion</a>
    </div>
</nav>

<div class="container mt-5">
    <h2>Avis en attente de validation</h2>
    <?php foreach ($avisStmt as $avis): ?>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong><?php echo htmlspecialchars($avis['pseudo']); ?></strong> (trajet : <?php echo $avis['ville_depart'] . " ➝ " . $avis['ville_arrivee']; ?>)</p>
                <p>Note : <?php echo $avis['note']; ?>/5</p>
                <p>Commentaire : <?php echo nl2br(htmlspecialchars($avis['commentaire'])); ?></p>
                <form method="POST" class="d-inline">
                    <button name="valider_avis" value="<?php echo $avis['id_avis']; ?>" class="btn btn-success btn-sm">Valider</button>
                </form>
                <form method="POST" class="d-inline">
                    <button name="refuser_avis" value="<?php echo $avis['id_avis']; ?>" class="btn btn-danger btn-sm">Refuser</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <hr class="my-5">

    <h2>Trajets signalés ou mal notés</h2>
    <?php foreach ($problems as $p): ?>
        <div class="alert alert-warning">
            <p><strong>Utilisateur :</strong> <?php echo $p['pseudo']; ?> (<?php echo $p['email']; ?>)</p>
            <p><strong>Trajet :</strong> <?php echo $p['ville_depart'] . " ➝ " . $p['ville_arrivee']; ?> | <?php echo $p['date']; ?></p>
            <p><strong>Note :</strong> <?php echo $p['note']; ?>/5</p>
            <p><strong>Commentaire :</strong><br> <?php echo nl2br(htmlspecialchars($p['commentaire'])); ?></p>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
