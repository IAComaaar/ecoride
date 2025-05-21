<?php
require_once 'connexion.php'; 
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id_user, role FROM utilisateur WHERE id_user = :id");
$stmt->bindParam(':id', $_SESSION['id_user']);
$stmt->execute();
$user = $stmt->fetch();
?>