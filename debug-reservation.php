<?php
// debug-reservation.php
session_start();
require_once 'connexion.php';

echo "<h1>Debug Réservation</h1>";

// Vérifier la session
echo "<h2>Session actuelle :</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Vérifier si la table participation existe et son contenu
echo "<h2>Table participation :</h2>";
try {
    $result = $pdo->query("SELECT * FROM participation")->fetchAll();
    echo "Nombre de participations : " . count($result) . "<br>";
    echo "<pre>" . print_r($result, true) . "</pre>";
} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage();
}

// Vérifier les crédits de l'utilisateur
if (isset($_SESSION['id_user'])) {
    echo "<h2>Utilisateur connecté :</h2>";
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
    $stmt->execute([$_SESSION['id_user']]);
    $user = $stmt->fetch();
    echo "<pre>" . print_r($user, true) . "</pre>";
}

echo "<h2>Variables $_GET et $_POST :</h2>";
echo "GET: <pre>" . print_r($_GET, true) . "</pre>";
echo "POST: <pre>" . print_r($_POST, true) . "</pre>";

echo "<br><a href='/voir.php?id=1'>Test voir.php?id=1</a>";
?>