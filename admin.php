<?php
session_start();
require_once 'connexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$msgEmploye = "";

if (isset($_POST['creer_employe'])) {
    $pseudo = htmlspecialchars($_POST['employe_pseudo']);
    $email = htmlspecialchars($_POST['employe_email']);
    $mdp = $_POST['employe_mdp'];
    $hash = password_hash($mdp, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateur (pseudo, email, mot_de_passe, role, credit) VALUES (?, ?, ?, 'employe', 0)");
        $stmt->execute([$pseudo, $email, $hash]);
        $msgEmploye = "Compte employé créé avec succès. ✅";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $msgEmploye = "⚠️ Un compte avec cet email existe déjà.";
        } else {
            throw $e;
        }
    }
}

// Suspendre un utilisateur
if (isset($_POST['suspendre']) && isset($_POST['suspendre_id'])) {
    $id = intval($_POST['suspendre_id']);

    // Empêcher de suspendre l'admin lui-même par accident
    if ($id !== $_SESSION['id_user']) {
        $stmt = $pdo->prepare("UPDATE utilisateur SET suspendu = 1 WHERE id_user = ?");
        $stmt->execute([$id]);
        header("Location: admin.php"); // rafraîchir la page
        exit;
    }
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Afin de protéger l'accès
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    die("Accès réservé à l'administrateur. ⛔");
}

// Les covoiturages par jour
$stmt = $pdo->query("SELECT date, COUNT(*) AS total FROM covoiturage GROUP BY date ORDER BY date ASC");
$statsCovoit = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Les crédits gagnés par jour (2 crédits par trajet)
$stmt = $pdo->query("
    SELECT date, COUNT(*) * 2 AS credits 
    FROM covoiturage 
    GROUP BY date 
    ORDER BY date ASC
");

$statsCredits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Le total des crédits
$stmt = $pdo->query("SELECT COUNT(*) * 2 AS total_credits FROM covoiturage");
$totalCredits = $stmt->fetch(PDO::FETCH_ASSOC)['total_credits'] ?? 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - EcoRide</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/ecoride/index.php">EcoRide Admin</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Tableau de bord administrateur 👑</h2>
    <p class="text-center text-muted">Bienvenue, vous pouvez voir les statistiques, créer des comptes employés et suspendre des utilisateurs.</p>

<div class="row mt-5">
 <div class="col-md-6">
    <h5 class="text-center">Covoiturages par jour 📆</h5>
    <canvas id="chartCovoit"></canvas>
</div>
<div class="col-md-6">
    <h5 class="text-center">Crédits gagnés par jour 💰</h5>
    <canvas id="chartCredits"></canvas>
</div>
</div>

<div class="mt-5 text-center">
    <h4>Crédits totaux gagnés par la plateforme 🎯 : <?= $totalCredits ?> ⚡</h4>
</div>
</div>

<script>
const ctxCovoit = document.getElementById('chartCovoit').getContext('2d');
const chartCovoit = new Chart(ctxCovoit, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($statsCovoit, 'date')) ?>,
        datasets: [{
            label: 'Nombre de covoiturages',
            data: <?= json_encode(array_column($statsCovoit, 'total')) ?>,
            backgroundColor: 'rgba(25, 135, 84, 0.7)'
        }]
    }
});

const ctxCredits = document.getElementById('chartCredits').getContext('2d');
const chartCredits = new Chart(ctxCredits, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($statsCredits, 'date')) ?>,
        datasets: [{
            label: 'Crédits gagnés',
            data: <?= json_encode(array_column($statsCredits, 'credits')) ?>,
            borderColor: 'rgba(255, 193, 7, 1)',
            fill: false
        }]
    }
});
</script>

<hr class="my-5">

<h3 class="text-center mb-4">Créer un compte employé 👷</h3>

<form method="POST" class="mb-5 w-50 mx-auto">
    <div class="mb-3">
        <label class="form-label">Pseudo</label>
        <input type="text" name="employe_pseudo" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="employe_email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="employe_mdp" class="form-control" required>
    </div>
    <button type="submit" name="creer_employe" class="btn btn-primary w-100">Créer le compte employé</button>
</form>

<hr class="my-5">

<h3 class="text-center mb-4">Gérer les comptes utilisateurs 🛑</h3>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Pseudo</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Crédits</th>
            <th>État</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM utilisateur WHERE id_user != {$_SESSION['id_user']}");
        $comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comptes as $compte) :
        ?>
        <tr>
            <td><?= $compte['id_user'] ?></td>
            <td><?= htmlspecialchars($compte['pseudo']) ?></td>
            <td><?= htmlspecialchars($compte['email']) ?></td>
            <td><?= $compte['role'] ?></td>
            <td><?= $compte['credit'] ?></td>
            <td>
                <?php if ($compte['suspendu']) : ?>
                    <span class="badge bg-danger">Suspendu</span>
                <?php else : ?>
                    <span class="badge bg-success">Actif</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!$compte['suspendu']) : ?>
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="suspendre_id" value="<?= $compte['id_user'] ?>">
                    <button type="submit" name="suspendre" class="btn btn-sm btn-warning">Suspendre</button>
                </form>
                <?php else : ?>
                    <em class="text-muted">-</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (!empty($msgEmploye)) : ?>
    <div class="alert alert-info text-center"><?= $msgEmploye ?></div>
<?php endif; ?>


</body>
</html>
