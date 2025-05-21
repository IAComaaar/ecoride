<?php
session_start();
require_once 'connexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Dans login.php, recherchez ce bloc de code qui gère la connexion réussie
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['pseudo'] = $user['pseudo'];
        $_SESSION['role'] = $user['role'];
    
        // Vérifier paramètre URL
        if (isset($_GET['redirect']) && $_GET['redirect'] == 'trajet' && isset($_GET['id_trajet'])) {
            $trajet_id = intval($_GET['id_trajet']);
            header('Location: voir.php?id=' . $trajet_id);
            exit;
        }
    
        // Redirection standard
        header('Location: mon-espace.php');
        exit;
    }else {
    $message = "Identifiants incorrects. ❌";
}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">EcoRide</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Connexion à votre compte</h2>

    <?php if ($message): ?>
        <div class="alert alert-danger text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['redirect']) && $_GET['redirect'] == 'trajet'): ?>
        <div class="alert alert-info text-center mb-4">
            Connectez-vous pour finaliser votre participation au trajet
        </div>
    <?php endif; ?>


    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" name="mot_de_passe" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Se connecter</button>
    </form>

    <div class="text-center mt-3">
        <a href="/inscription.php">Pas encore de compte ? Créez-en un</a>
    </div>
</div>

</body>
</html>

