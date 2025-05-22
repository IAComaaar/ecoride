<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'connexion.php';

// Récupérer l'ID utilisateur de la session
$userId = $_SESSION['id_user'];

// Initialiser la variable pour les messages
$annulationMessage = "";

// Traitement de l'annulation d'une réservation
if (isset($_POST['annuler']) && isset($_POST['annuler_id'])) {
    $idCovoiturage = intval($_POST['annuler_id']);
    $idUser = $userId;

    try {
        // Vérifier si la table participation existe
        $result = $pdo->query("SHOW TABLES LIKE 'participation'");
        if ($result->rowCount() > 0) {
            // Pour marquer comme annulé
            $stmt = $pdo->prepare("UPDATE participation SET status = 'annulé' WHERE id_user = ? AND id_covoiturage = ?");
            $stmt->execute([$idUser, $idCovoiturage]);

            // Pour rembourser les crédits
            $result = $pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'credit'");
            if ($result->rowCount() > 0) {
                $stmt = $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE id_user = ?");
                $stmt->execute([$idUser]);
            }

            // Pour remettre une place dans le covoiturage
            $stmt = $pdo->prepare("UPDATE covoiturage SET nb_places = nb_places + 1 WHERE id_covoiturage = ?");
            $stmt->execute([$idCovoiturage]);

            $annulationMessage = "<div class='alert alert-success text-center'>Trajet annulé. Vos crédits ont été remboursés ✅</div>";
        } else {
            $annulationMessage = "<div class='alert alert-warning text-center'>La table participation n'existe pas encore.</div>";
        }
    } catch (PDOException $e) {
        $annulationMessage = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
    }
}

// Afin de démarrer un trajet
if (isset($_POST['demarrer']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);
    try {
        // Vérifier si la colonne etat existe
        $result = $pdo->query("SHOW COLUMNS FROM covoiturage LIKE 'etat'");
        if ($result->rowCount() > 0) {
            $pdo->prepare("UPDATE covoiturage SET etat = 'en cours' WHERE id_covoiturage = ?")->execute([$idTrajet]);
            $annulationMessage = "<div class='alert alert-warning text-center'>Trajet démarré ! 🚗💨</div>";
        } else {
            $annulationMessage = "<div class='alert alert-warning text-center'>La colonne etat n'existe pas dans la table covoiturage.</div>";
        }
    } catch (PDOException $e) {
        $annulationMessage = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
    }
}

// Et afin de terminer un trajet
if (isset($_POST['terminer']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);
    try {
        // Vérifier si la colonne etat existe
        $result = $pdo->query("SHOW COLUMNS FROM covoiturage LIKE 'etat'");
        if ($result->rowCount() > 0) {
            $pdo->prepare("UPDATE covoiturage SET etat = 'terminé' WHERE id_covoiturage = ?")->execute([$idTrajet]);
            $annulationMessage = "<div class='alert alert-success text-center'>Trajet terminé ! Les passagers vont être notifiés ✅</div>";
        } else {
            $annulationMessage = "<div class='alert alert-warning text-center'>La colonne etat n'existe pas dans la table covoiturage.</div>";
        }
    } catch (PDOException $e) {
        $annulationMessage = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
    }
}

// Pour l'annulation d'un trajet
if (isset($_POST['annuler_trajet']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);

    try {
        // Vérifier si la table participation existe
        $result = $pdo->query("SHOW TABLES LIKE 'participation'");
        if ($result->rowCount() > 0) {
            // Récupérer les participants au trajet
            $stmt = $pdo->prepare("SELECT id_user FROM participation WHERE id_covoiturage = ?");
            $stmt->execute([$idTrajet]);
            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Marquer les participations comme annulées + rembourser
            foreach ($participants as $participant) {
                $idPassager = $participant['id_user'];

                $pdo->prepare("UPDATE participation SET status = 'annulé' WHERE id_user = ? AND id_covoiturage = ?")
                    ->execute([$idPassager, $idTrajet]);

                // Vérifier si la colonne credit existe
                $result = $pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'credit'");
                if ($result->rowCount() > 0) {
                    $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE id_user = ?")
                        ->execute([$idPassager]);
                }
            }

            // Supprimer le trajet ou le désactiver
            $pdo->prepare("DELETE FROM covoiturage WHERE id_covoiturage = ?")->execute([$idTrajet]);

            $annulationMessage = "<div class='alert alert-info text-center'>Trajet annulé. Les passagers ont été notifiés (simulation). ✅</div>";
        } else {
            $annulationMessage = "<div class='alert alert-success text-center'>Trajet annulé ! Les réservations seront gérées automatiquement. ✅ </div>";
        }
    } catch (PDOException $e) {
        $annulationMessage = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
    }
}

// Récupérer les informations de l'utilisateur
try {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user = ['email' => 'Erreur de chargement', 'credit' => 0];
    $annulationMessage .= "<div class='alert alert-danger text-center'>Erreur lors du chargement du profil : " . $e->getMessage() . "</div>";
}

// Récupérer les réservations de l'utilisateur
try {
    $reservations = [];
    $result = $pdo->query("SHOW TABLES LIKE 'participation'");
    if ($result->rowCount() > 0) {
        $stmt = $pdo->prepare("SELECT c.*, p.status 
                           FROM participation p
                           JOIN covoiturage c ON c.id_covoiturage = p.id_covoiturage 
                           WHERE p.id_user = ?");
        $stmt->execute([$userId]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $annulationMessage .= "<div class='alert alert-danger text-center'>Erreur lors du chargement des réservations : " . $e->getMessage() . "</div>";
}

// Récupérer les trajets créés par l'utilisateur
try {
    $stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE id_chauffeur = ?");
    $stmt->execute([$userId]);
    $mesTrajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mesTrajets = [];
    $annulationMessage .= "<div class='alert alert-danger text-center'>Erreur lors du chargement de vos trajets : " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon espace 🌱</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<!-- Navbar -->
 <nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">EcoRide</a>
        <div class="text-end mb-3">
            <a href="/deconnexion.php" class="btn btn-danger">Déconnexion</a>
        </div>
    </div>
 </nav>

 <div class="container mt-5">
    <?php if (!empty($annulationMessage)) echo $annulationMessage; ?>
    <h1 class="mb-4 text-center">Bienvenue dans votre espace</h1>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Vos informations 👤</h5>
            <!-- Utiliser email sans vérifier si pseudo existe -->
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email'] ?? 'Non disponible'); ?></p>
            <!-- Vérifier si credit existe avant de l'afficher -->
            <?php if (isset($user['credit'])): ?>
                <p><strong>Crédits restants : </strong><?php echo $user['credit']; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <h3>Vos trajets réservés 🧾</h3>
    <?php if (!empty($reservations)): ?>
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
                            <?php if ($trajet['status'] === 'confirmé'): ?>
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
        <a href="/ajouter-vehicule.php" class="btn btn-outline-primary btn-sm">Ajouter un véhicule ➕</a>
        <a href="/creer-trajet.php" class="btn btn-outline-success btn-sm">Proposer un trajet ➕</a>
    </div>

    <hr class="my-5">

    <h3> Vos trajets créés 🚘</h3>
    <?php if (!empty($mesTrajets)): ?>
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
                            <!-- Affichage de l'état -->
                            <?php if (isset($trajet['etat'])): ?>
                                <p><strong>État :</strong> <?php echo htmlspecialchars($trajet['etat']); ?></p>
                                <!-- Boutons selon l'état -->
                                 <?php if (isset($trajet['etat']) && $trajet['etat'] === 'non démarré'): ?>
                                    <form method="POST" class="mt-2">
                                        <input type="hidden" name="id_trajet" value="<?php echo $trajet['id_covoiturage']; ?>">
                                        <button type="submit" name="demarrer" class="btn btn-warning btn-sm">Démarrer le trajet</button>
                                    </form>
                                    <form method="POST" class="mt-2">
                                        <input type="hidden" name="id_trajet" value="<?php echo $trajet['id_covoiturage']; ?>">
                                        <button type="submit" name="annuler_trajet" class="btn btn-danger btn-sm">Annuler ce trajet</button>
                                    </form>
                                    <?php elseif (isset($trajet['etat']) && $trajet['etat'] === 'en cours'): ?>
                                        <form method="POST" class="mt-2">
                                            <input type="hidden" name="id_trajet" value="<?php echo $trajet['id_covoiturage']; ?>">
                                            <button type="submit" name="terminer" class="btn btn-success btn-sm">Arrivée à destination</button>
                                        </form>
                                        <form method="POST" class="mt-2">
                                            <input type="hidden" name="id_trajet" value="<?php echo $trajet['id_covoiturage']; ?>">
                                            <button type="submit" name="annuler_trajet" class="btn btn-danger btn-sm">Annuler le trajet (urgence)</button>
                                        </form>
                                        <?php elseif (isset($trajet['etat']) && $trajet['etat'] === 'terminé'): ?>
                                            <p class="text-success"><strong>✅ Trajet terminé</strong></p>
                                             <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">Aucun trajet créé pour l'instant.</p>
    <?php endif; ?>
 </div>
</body>
</html>