<?php
session_start();
require_once 'connexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$success_message = "";
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "✅ Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
}

// Si déjà connecté, rediriger vers l'espace utilisateur
if (isset($_SESSION['id_user'])) {
    header('Location: mon-espace.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // Connexion réussie
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['role'] = $user['role'];
        
// Après connexion réussie
if (isset($_GET['trajet_id'])) {
    $trajet_id = intval($_GET['trajet_id']);
    header('Location: mon-espace.php?from_reservation=1&trajet_id=' . $trajet_id);
} else {
    header('Location: mon-espace.php');
}
exit;
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - EcoRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            font-family: inherit;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: white;
            background-size: cover;
            background-position: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .login-header {
            background-color: #198754;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
        }
        .login-body {
            padding: 30px;
        }
        .btn-login {
            background-color: #198754;
            border-color: #198754;
            padding: 10px 0;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: #146c43;
            border-color: #146c43;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(20, 108, 67, 0.3);
        }
        .login-footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
        }
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #198754;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .eco-badge {
            background-color: #dcf5e6;
            color: #198754;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <div class="login-card card">
                    <div class="login-header">
                        <h3 class="mb-0 text-center">Connexion</h3>
                    </div>
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                    
                    <div class="login-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="login.php">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="votre@email.com">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-login btn-success">Se connecter</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="login-footer">
                        <p class="mb-0">Pas encore de compte ? <a href="inscription.php" class="text-success">S'inscrire</a></p>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="index.php" class="text-dark text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>