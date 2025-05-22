<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions légales - EcoRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
                    <?php if (isset($_SESSION['id_user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/mon-espace.php">Mon compte</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login.php">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Mentions légales</h1>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-success">📝 Éditeur du site</h3>
                        <p><strong>EcoRide</strong><br>
                        Plateforme de covoiturage écologique<br>
                        Email : contact@ecoride.fr</p>
                        
                        <h3 class="text-success mt-4">🌐 Hébergement</h3>
                        <p><strong>Heroku, Inc.</strong><br>
                        Base de données : JawsDB (MySQL)<br>
                        Hébergement cloud sécurisé</p>
                        
                        <h3 class="text-success mt-4">🔒 Données personnelles</h3>
                        <p>Les données personnelles collectées (nom, prénom, email) sont utilisées uniquement pour :</p>
                        <ul>
                            <li>Le fonctionnement de la plateforme de covoiturage</li>
                            <li>La mise en relation entre conducteurs et passagers</li>
                            <li>La gestion des réservations</li>
                        </ul>
                        <p>Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression de vos données.</p>
                        
                        <h3 class="text-success mt-4">🍪 Cookies</h3>
                        <p>Ce site utilise uniquement des cookies de session nécessaires au fonctionnement de l'authentification. Aucun cookie de tracking n'est utilisé.</p>
                        
                        <h3 class="text-success mt-4">⚖️ Responsabilité</h3>
                        <p>EcoRide met en relation les utilisateurs mais n'est pas responsable des trajets effectués. Chaque utilisateur est responsable de ses actes.</p>
                        
                        <h3 class="text-success mt-4">🌱 Engagement écologique</h3>
                        <p>EcoRide s'engage à promouvoir une mobilité durable en favorisant le partage des trajets et en réduisant l'empreinte carbone des déplacements.</p>
                    </div>
                </div>
                
                <div class="text-center mt-4 mb-5">
                    <a href="/index.php" class="btn btn-success">🏠 Retour à l'accueil</a>
                    <a href="mailto:contact@ecoride.fr" class="btn btn-outline-success">📧 Nous contacter</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>