<?php

require_once 'connexion.php'; 
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'connexion.php';
$stmt = $pdo->prepare("SELECT suspendu FROM utilisateur WHERE id_user = ?");
$stmt->execute([$_SESSION['id_user']]);
$user = $stmt->fetch();
if ($user && $user['suspendu'] == 1) {
    session_destroy();
    header('Location: login.php?error=account_suspended');
    exit;
}
?>