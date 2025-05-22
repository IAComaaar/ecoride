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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">EcoRide</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">Home 🏠</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/recherche.php">Covoiturages 🚘</a>
                </li>
                
                <?php if (isset($_SESSION['id_user'])) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/mon-espace.php">Mon compte 👤</a>
                    </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login.php">Connexion 👤</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    <!-- Hero section -->
     <section class="text-center p-5 bg-light">
        <div class="container">
            <h1 class="mb-4">Voyagez autrement, voyagez EcoRide 🚗</h1>
           <a href="/recherche.php" class="btn btn-success btn-lg">Rechercher un covoiturage</a>
        </div>
        </section>
<body class="d-flex flex-column min-vh-100">

    <!-- CONTENU PRINCIPAL -->
    <main class="flex-fill">
    <section class="eco-section py-5">
  <div class="container">
    <div class="row align-items-center">
      <!-- Colonne de gauche (image) -->
      <div class="col-md-6 mb-4 mb-md-0">
        <img src="/img/eco-road.jpg" class="img-fluid rounded shadow-lg" alt="Voyage écologique">
      </div>
      
      <!-- Colonne de droite (texte) -->
      <div class="col-md-6">
        <h2 class="text-success mb-3">Covoiturage responsable</h2>
        <p class="lead">Rejoignez notre communauté et participez à un mode de transport plus écologique et économique.</p>
        
        <div class="mt-4">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-flower1 text-success fs-4 me-3"></i>
            <div>
              <strong>Réduisez votre empreinte carbone</strong>
              <p class="mb-0 text-muted">Chaque trajet partagé diminue les émissions de CO₂</p>
            </div>
          </div>
          
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-piggy-bank-fill text-success fs-4 me-3"></i>
            <div>
              <strong>Économisez sur vos déplacements</strong>
              <p class="mb-0 text-muted">Partagez les frais de carburant et réduisez vos dépenses</p>
            </div>
          </div>
          
          <div class="d-flex align-items-center">
            <i class="bi bi-people-fill text-success fs-4 me-3"></i>
            <div>
              <strong>Créez des liens</strong>
              <p class="mb-0 text-muted">Rencontrez de nouvelles personnes lors de vos trajets</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    </main>

    <!-- FOOTER EN BAS -->
    <footer class="bg-dark text-light text-center py-3 mt-auto">
        © 2025 EcoRide | <a href="/mentions-legales.php" class="text-light text-decoration-underline">Mentions légales</a> |
        <a href="mailto:contact@ecoride.fr" class="text-light">contact@ecoride.fr</a>
    </footer>

</body>

      <!-- Bootstrap JS -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>