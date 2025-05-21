<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Correction du fichier login.php</h1>";

if (file_exists('login.php')) {
    $content = file_get_contents('login.php');
    
    if (strpos($content, '$_POST["pseudo"]') !== false) {
        $modified = str_replace('$_SESSION["pseudo"] = $_POST["pseudo"];', '// $_SESSION["pseudo"] = $_POST["email"]; // Commenté car "pseudo" n\'existe pas', $content);
        
        if (file_put_contents('login.php', $modified)) {
            echo "<p style='color:green'>Le fichier login.php a été corrigé avec succès!</p>";
        } else {
            echo "<p style='color:red'>Erreur: Impossible d'écrire dans le fichier login.php.</p>";
        }
    } else {
        echo "<p>Aucune référence à \$_POST[\"pseudo\"] trouvée dans login.php.</p>";
        echo "<p>Contenu actuel :</p>";
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    }
} else {
    echo "<p style='color:red'>Le fichier login.php n'existe pas.</p>";
}

echo "<p><a href='/login.php'>Retour à la page de connexion</a></p>";
?>