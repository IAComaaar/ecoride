<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'connexion.php';

$userId = $_SESSION['id_user'];

// Initialiser la variable pour les messages
$annulationMessage = "";

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

            // Pour rembourser les crédits (si la colonne existe)
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

if (isset($_POST['demarrer']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);
    try {
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
if (isset($_POST['terminer']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);
    try {
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
if (isset($_POST['annuler_trajet']) && isset($_POST['id_trajet'])) {
    $idTrajet = intval($_POST['id_trajet']);

    try {
        $result = $pdo->query("SHOW TABLES LIKE 'participation'");
        if ($result->rowCount() > 0) {
            $stmt = $pdo->prepare("SELECT id_user FROM participation WHERE id_covoiturage = ?");
            $stmt->execute([$idTrajet]);
            $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($participants as $participant) {
                $idPassager = $participant['id_user'];

                $pdo->prepare("UPDATE participation SET status = 'annulé' WHERE id_user = ? AND id_covoiturage = ?")
                    ->execute([$idPassager, $idTrajet]);

                $result = $pdo->query("SHOW COLUMNS FROM utilisateur LIKE 'credit'");
                if ($result->rowCount() > 0) {
                    $pdo->prepare("UPDATE utilisateur SET credit = credit + 2 WHERE id_user = ?")
                        ->execute([$idPassager]);
                }
            }

           
            $pdo->prepare("DELETE FROM covoiturage WHERE id_covoiturage = ?")->execute([$idTrajet]);

            $annulationMessage = "<div class='alert alert-info text-center'>Trajet annulé. Les passagers ont été notifiés (simulation). ✅</div>";
        } else {
            $annulationMessage = "<div class='alert alert-warning text-center'>La table participation n'existe pas encore.</div>";
        }
    } catch (PDOException $e) {
        $annulationMessage = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user = ['email' => 'Erreur de chargement', 'credit' => 0];
    $annulationMessage .= "<div class='alert alert-danger text-center'>Erreur lors du chargement du profil : " . $e->getMessage() . "</div>";
}

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
            <a href="/deconnexion.php" class="btn btn-outline-danger">Déconnexion</a>
        </div>
    </div>
 </nav>

 <div class="container mt-5">
    <?php if (!empty($annulationMessage)) echo $annulationMessage; ?>
    <h1 class="mb-4 text-center">Bienvenue dans votre espace</h1>
    <div clas