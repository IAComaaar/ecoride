<?php
require_once 'auth-check.php'; //Même chose que pour employe

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}
?>