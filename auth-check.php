<?php

require_once 'connexion.php'; 
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'connexion.php';
$stmt = $pdo->prepare("SELECT id_user, role FROM utilisateur WHERE id_user = :id");
$stmt->execute([$_SESSION['id_user']]);
$user = $stmt->fetch();
?>