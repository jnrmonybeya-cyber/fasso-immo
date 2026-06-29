<?php
namespace App\Models;

use App\Config\Database;

class Visite {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO demandes_visite (client_id, annonce_id, date_visite, heure_visite, message) 
                VALUES (:client_id, :annonce_id, :date_visite, :heure_visite, :message)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM demandes_visite WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getByClient($clientId) {
        $sql = "SELECT dv.*, a.titre FROM demandes_visite dv 
                JOIN annonces a ON dv.annonce_id = a.id 
                WHERE dv.client_id = :client_id 
                ORDER BY dv.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll();
    }
    
    public function getByAnnonce($annonceId) {
        $sql = "SELECT dv.*, u.nom, u.prenom, u.email, u.telephone FROM demandes_visite dv 
                JOIN users u ON dv.client_id = u.id 
                WHERE dv.annonce_id = :annonce_id 
                ORDER BY dv.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['annonce_id' => $annonceId]);
        return $stmt->fetchAll();
    }
    
    public function getWaiting() {
        $sql = "SELECT dv.*, u.nom, u.prenom, a.titre 
                FROM demandes_visite dv 
                JOIN users u ON dv.client_id = u.id 
                JOIN annonces a ON dv.annonce_id = a.id 
                WHERE dv.statut = 'attente' 
                ORDER BY dv.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function validate($id) {
        $sql = "UPDATE demandes_visite SET statut = 'validee' WHERE id = :id AND statut = 'attente'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function refuse($id) {
        $sql = "UPDATE demandes_visite SET statut = 'refusee' WHERE id = :id AND statut = 'attente'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function countByClient($clientId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM demandes_visite WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $clientId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function countWaiting() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM demandes_visite WHERE statut = 'attente'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}