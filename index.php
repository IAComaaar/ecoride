<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide - Accueil</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="/ecoride/index.php">EcoRide</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="/ecoride/index.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/ecoride/recherche.php">Covoiturages</a>
            </li>
            
            <?php
            if (isset($_SESSION['id_user'])) {
                if (basename($_SERVER['PHP_SELF']) !== 'mon-espace.php') {
                    echo '<li class="nav-item">
                    <a class="nav-link" href="/ecoride/mon-espace.php">Mon compte</a>
                    </li>';}
                } else {
                    echo '<li class="nav-item">
                    <a class="nav-link" href="/ecoride/login.php">Connexion</a>
                    </li>';}
                    ?>
                    </ul>
                </div>
            </div>
        </nav>
    <!-- Hero section -->
     <section class="text-center p-5 bg-light">
        <div class="container">
            <h1 class="mb-4">Voyagez autrement, voyagez EcoRide 🚗</h1>
           <a href="/ecoride/recherche.php" class="btn btn-success btn-lg">Rechercher un covoiturage</a>
        </div>
        </section>
<body class="d-flex flex-column min-vh-100">

    <!-- CONTENU PRINCIPAL -->
    <main class="flex-fill">
        
    </main>

    <!-- FOOTER EN BAS -->
    <footer class="bg-dark text-light text-center py-3 mt-auto">
        © 2025 EcoRide | <a href="mentions-legales.php" class="text-light text-decoration-underline">Mentions légales</a> |
        <a href="mailto:contact@ecoride.fr" class="text-light">contact@ecoride.fr</a>
    </footer>

</body>

      <!-- Bootstrap JS -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>