<?php
namespace App\Models;

use App\Config\Database;

class Favori {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function add($clientId, $annonceId) {
        // Vérifier si existe déjà
        $stmt = $this->db->prepare("SELECT id FROM favoris WHERE client_id = :client_id AND annonce_id = :annonce_id");
        $stmt->execute(['client_id' => $clientId, 'annonce_id' => $annonceId]);
        
        if ($stmt->fetch()) {
            return false;
        }
        
        $sql = "INSERT INTO favoris (client_id, annonce_id) VALUES (:client_id, :annonce_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['client_id' => $clientId, 'annonce_id' => $annonceId]);
    }
    
    public function remove($clientId, $annonceId) {
        $sql = "DELETE FROM favoris WHERE client_id = :client_id AND annonce_id = :annonce_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['client_id' => $clientId, 'annonce_id' => $annonceId]);
    }
    
    public function getByClient($clientId) {
        $sql = "SELECT a.*, p.photo_path FROM annonces a 
                JOIN favoris f ON a.id = f.annonce_id 
                LEFT JOIN photos_annonce p ON a.id = p.annonce_id AND p.is_principal = 1
                WHERE f.client_id = :client_id AND a.statut = 'publie'
                ORDER BY f.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll();
    }
    
    public function isFavori($clientId, $annonceId) {
        $stmt = $this->db->prepare("SELECT id FROM favoris WHERE client_id = :client_id AND annonce_id = :annonce_id");
        $stmt->execute(['client_id' => $clientId, 'annonce_id' => $annonceId]);
        return $stmt->fetch() !== false;
    }
    
    public function countByClient($clientId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM favoris WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $clientId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}