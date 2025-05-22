<?php
session_start();
require_once 'connexion.php';

echo "<h1>Debug Participation</h1>";

// Vérifier si la table existe
try {
    $result = $pdo->query("SHOW TABLES LIKE 'participation'");
    if ($result->rowCount() > 0) {
        echo "✅ Table participation existe<br>";
        
        // Voir la structure
        $structure = $pdo->query("DESCRIBE participation")->fetchAll();
        echo "<h3>Structure :</h3>";
        foreach ($structure as $col) {
            echo $col['Field'] . " - " . $col['Type'] . "<br>";
        }
        
        // Voir le contenu
        $data = $pdo->query("SELECT * FROM participation")->fetchAll();
        echo "<h3>Contenu (" . count($data) . " lignes) :</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        
    } else {
        echo "❌ Table participation n'existe pas !";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}

// Vérifier les crédits de l'utilisateur connecté
if (isset($_SESSION['id_user'])) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
    $stmt->execute([$_SESSION['id_user']]);
    $user = $stmt->fetch();
    echo "<h3>Utilisateur connecté :</h3>";
    echo "ID : " . $user['id_user'] . "<br>";
    echo "Email : " . $user['email'] . "<br>";
    echo "Crédits : " . ($user['credit'] ?? 'NON DÉFINI') . "<br>";
}

echo "<br><a href='/voir.php'>Retour</a>";
?>