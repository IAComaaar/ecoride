<?php
require_once 'auth-check.php';  // Afin de vérifie d'abord si l'utilisateur est connecté

// Afin de vérifier si l'utilisateur a les droits d'employé
if ($_SESSION['role'] !== 'employe' && $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>