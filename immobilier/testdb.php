<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de connexion à la base de données</h1>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=immobilier_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green;'>✅ Connexion à la base de données réussie !</p>";
    
    // Tester une requête
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM annonces");
    $result = $stmt->fetch();
    echo "<p>Nombre d'annonces : " . $result['count'] . "</p>";
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}