<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Définir les constantes nécessaires
define('APP_URL', 'http://localhost/immobilier');
define('APP_NAME', 'FASO IMMO');
define('UPLOAD_DIR', __DIR__ . '/public/uploads/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'immobilier_db');
define('DB_USER', 'root');
define('DB_PASS', '');

require_once 'app/Config/Database.php';
require_once 'app/Models/Annonce.php';
require_once 'app/Models/Photo.php';

use App\Config\Database;
use App\Models\Annonce;
use App\Models\Photo;

echo "<h1>🏠 Catalogue Immobilier - Test</h1>";

try {
    $annonceModel = new Annonce();
    $photoModel = new Photo();
    $db = Database::getInstance();
    
    // 1. Vérifier toutes les annonces
    echo "<h2>1. Toutes les annonces</h2>";
    $stmt = $db->query("SELECT id, titre, statut, zone_geographique, prix, bailleur_id FROM annonces");
    $allAnnonces = $stmt->fetchAll();
    
    if (empty($allAnnonces)) {
        echo "<p style='color:red;'>❌ Aucune annonce dans la base de données !</p>";
    } else {
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>ID</th><th>Titre</th><th>Statut</th><th>Zone</th><th>Prix</th><th>Bailleur</th></tr>";
        foreach ($allAnnonces as $a) {
            // Récupérer le nom du bailleur
            $stmt2 = $db->prepare("SELECT nom, prenom FROM users WHERE id = ?");
            $stmt2->execute([$a['bailleur_id']]);
            $user = $stmt2->fetch();
            $bailleur = $user ? $user['prenom'] . ' ' . $user['nom'] : 'Inconnu';
            
            echo "<tr>";
            echo "<td>" . $a['id'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($a['titre']) . "</strong></td>";
            echo "<td style='font-weight:bold;color:" . ($a['statut'] == 'publie' ? 'green' : ($a['statut'] == 'attente' ? 'orange' : 'red')) . ";'>" . $a['statut'] . "</td>";
            echo "<td>" . htmlspecialchars($a['zone_geographique']) . "</td>";
            echo "<td>" . number_format($a['prix'], 0, ',', ' ') . " FCFA</td>";
            echo "<td>" . htmlspecialchars($bailleur) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 2. Test de la méthode getAllPublished
    echo "<h2>2. Test de getAllPublished()</h2>";
    $annonces = $annonceModel->getAllPublished();
    echo "<p>Nombre d'annonces publiées : <strong>" . count($annonces) . "</strong></p>";
    
    if (empty($annonces)) {
        echo "<p style='color:orange;'>⚠️ Aucune annonce publiée trouvée.</p>";
        
        // Compter les annonces par statut
        echo "<h3>Répartition des statuts :</h3>";
        $stmt = $db->query("SELECT statut, COUNT(*) as count FROM annonces GROUP BY statut");
        while ($row = $stmt->fetch()) {
            echo "<p>" . $row['statut'] . " : " . $row['count'] . " annonce(s)</p>";
        }
        
        // Proposer de valider les annonces
        $stmt = $db->query("SELECT COUNT(*) as count FROM annonces WHERE statut = 'attente'");
        $attente = $stmt->fetch();
        if ($attente['count'] > 0) {
            echo "<p><a href='valider_toutes.php' style='background:#27ae60;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>✅ Valider toutes les annonces en attente</a></p>";
        }
    } else {
        echo "<p style='color:green;'>✅ " . count($annonces) . " annonce(s) publiée(s) trouvée(s)</p>";
        echo "<ul>";
        foreach ($annonces as $annonce) {
            echo "<li>📌 <strong>" . htmlspecialchars($annonce['titre']) . "</strong> - " . htmlspecialchars($annonce['zone_geographique']) . " - " . number_format($annonce['prix'], 0, ',', ' ') . " FCFA</li>";
        }
        echo "</ul>";
    }
    
    // 3. Vérifier les photos
    echo "<h2>3. Vérification des photos</h2>";
    $stmt = $db->query("SELECT id, annonce_id, photo_path, is_principal FROM photos_annonce");
    $photos = $stmt->fetchAll();
    
    if (empty($photos)) {
        echo "<p style='color:orange;'>⚠️ Aucune photo trouvée dans la base de données.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID Photo</th><th>Annonce ID</th><th>Chemin</th><th>Principal</th><th>Fichier existe</th></tr>";
        foreach ($photos as $p) {
            $exists = file_exists(UPLOAD_DIR . $p['photo_path']) ? '✅ Oui' : '❌ Non';
            echo "<tr>";
            echo "<td>" . $p['id'] . "</td>";
            echo "<td>" . $p['annonce_id'] . "</td>";
            echo "<td>" . htmlspecialchars($p['photo_path']) . "</td>";
            echo "<td>" . ($p['is_principal'] ? '⭐ Oui' : 'Non') . "</td>";
            echo "<td>" . $exists . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 4. Tester directement la requête
    echo "<h2>4. Test direct de la requête</h2>";
    $sql = "SELECT a.*, u.nom, u.prenom FROM annonces a 
            JOIN users u ON a.bailleur_id = u.id 
            WHERE a.statut = 'publie'
            ORDER BY a.date_publication DESC, a.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $directResults = $stmt->fetchAll();
    
    echo "<p>Résultats directs : <strong>" . count($directResults) . "</strong></p>";
    
    if (!empty($directResults)) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Titre</th><th>Bailleur</th><th>Statut</th><th>Prix</th></tr>";
        foreach ($directResults as $r) {
            echo "<tr>";
            echo "<td>" . $r['id'] . "</td>";
            echo "<td>" . htmlspecialchars($r['titre']) . "</td>";
            echo "<td>" . htmlspecialchars($r['prenom'] . ' ' . $r['nom']) . "</td>";
            echo "<td style='color:green;'>" . $r['statut'] . "</td>";
            echo "<td>" . number_format($r['prix'], 0, ',', ' ') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 5. Afficher les chemins
    echo "<h2>5. Informations système</h2>";
    echo "<p>Dossier uploads : " . UPLOAD_DIR . "</p>";
    echo "<p>Le dossier existe : " . (is_dir(UPLOAD_DIR) ? '✅ Oui' : '❌ Non') . "</p>";
    echo "<p>URL de base : " . APP_URL . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Erreur : " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>