<?php
session_start();
require_once 'connexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $mot_de_passe_confirme = $_POST['mot_de_passe_confirme'];

    // Vérification simple du mot de passe
    if ($mot_de_passe !== $mot_de_passe_confirme) {
        $message = "❌ Les mots de passe ne correspondent pas.";
    } else {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO utilisateur (pseudo, email, mot_de_passe, role, credit) VALUES (?, ?, ?, 'passager', 20)");
        try {
            $stmt->execute([$pseudo, $email, $hash]);
            header('Location: login.php?success=1');
            exit;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $message = "❗ Un compte avec cet email existe déjà.";
            } else {
                throw $e;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">EcoRide</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Créer un compte</h2>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="pseudo" class="form-label">Pseudo</label>
            <input type="text" class="form-control" name="pseudo" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" name="mot_de_passe" required>
        </div>

        <div class="mb-3">
            <label for="mot_de_passe_confirme" class="form-label">Confirmer le mot de passe</label>
            <input type="password" class="form-control" name="mot_de_passe_confirme" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Créer mon compte</button>
    </form>
    
    <div class="mt-4 text-center">
    <?php if (!empty($message) && strpos($message, 'existe déjà') !== false): ?>
        <!-- Si l'email existe déjà, proposer d'aller à la connexion -->
        <a href="login.php" class="btn btn-success">
            <i class="bi bi-arrow-left"></i> Aller à la connexion
        </a>
    <?php else: ?>
        <!-- Sinon, retour à l'accueil et lien vers connexion -->
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Retour à l'accueil
        </a>
        <a href="login.php" class="btn btn-outline-success ms-2">
            <i class="bi bi-person"></i> Déjà un compte ?
        </a>
    <?php endif; ?>
</div>

</div>

</body>
</html>
