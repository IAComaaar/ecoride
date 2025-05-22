<?php 
session_start(); 
require_once 'connexion.php';  

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);  

$success_message = ""; 
if (isset($_GET['success']) && $_GET['success'] == 1) {     
    $success_message = "Compte créé avec succès ! Vous pouvez maintenant vous connecter. ✅"; 
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
        
        // Gestion de la redirection         
        if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {             
            $redirect_url = urldecode($_GET['redirect']);             
            // Sécurité : vérifier que c'est une URL interne             
            if (strpos($redirect_url, '/') === 0 && strpos($redirect_url, '//') === false) {                 
                header('Location: ' . $redirect_url);             
            } else {                 
                header('Location: mon-espace.php');             
            }         
        } elseif (isset($_GET['trajet_id'])) {             
            $trajet_id = intval($_GET['trajet_id']);             
            header('Location: voir.php?id=' . $trajet_id . '&connected=1');         
        } else {             
            // Solution de secours si la redirection automatique échoue             
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'voir.php?id=') !== false) {                 
                preg_match('/voir\.php\?id=(\d+)/', $_SERVER['HTTP_REFERER'], $matches);                 
                if (!empty($matches[1])) {                     
                    header('Location: mon-espace.php?connected=1&from_trajet=' . $matches[1]);                 
                } else {                     
                    header('Location: mon-espace.php');                 
                }             
            } else {                 
                header('Location: mon-espace.php');             
            }         
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="/index.php">EcoRide</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                    <li class="nav-item">
                        <a class="nav-link active" href="/login.php">Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h3><i class="fas fa-sign-in-alt"></i> Connexion</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success" role="alert">
                                <?= $success_message ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Mot de passe
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-sign-in-alt"></i> Se connecter
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">Pas encore de compte ?</p>
                            <a href="/register.php" class="btn btn-outline-success">
                                <i class="fas fa-user-plus"></i> Créer un compte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>