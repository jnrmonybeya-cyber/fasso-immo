<?php
namespace App\Models;

use App\Config\Database;

class Annonce {
    private $db;
    private $photoModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->photoModel = new Photo();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT a.*, u.nom, u.prenom, u.email, u.telephone 
                                    FROM annonces a 
                                    JOIN users u ON a.bailleur_id = u.id 
                                    WHERE a.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function findPublished($id) {
        $stmt = $this->db->prepare("SELECT a.*, u.nom, u.prenom, u.email, u.telephone 
                                    FROM annonces a 
                                    JOIN users u ON a.bailleur_id = u.id 
                                    WHERE a.id = :id AND a.statut = 'publie'");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function getAllPublished($filters = []) {
        $sql = "SELECT a.*, u.nom, u.prenom FROM annonces a 
                JOIN users u ON a.bailleur_id = u.id 
                WHERE a.statut = 'publie'";
        $params = [];
        
        if (!empty($filters['zone'])) {
            $sql .= " AND a.zone_geographique LIKE :zone";
            $params[':zone'] = '%' . $filters['zone'] . '%';
        }
        
        if (!empty($filters['type_bien'])) {
            $sql .= " AND a.type_bien = :type";
            $params[':type'] = $filters['type_bien'];
        }
        
        $sql .= " ORDER BY a.date_publication DESC, a.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getByBailleur($bailleurId) {
        $stmt = $this->db->prepare("SELECT * FROM annonces WHERE bailleur_id = :bailleur_id ORDER BY created_at DESC");
        $stmt->execute(['bailleur_id' => $bailleurId]);
        return $stmt->fetchAll();
    }
    
    public function getByStatus($status) {
        $stmt = $this->db->prepare("SELECT a.*, u.nom, u.prenom FROM annonces a 
                                    JOIN users u ON a.bailleur_id = u.id 
                                    WHERE a.statut = :status 
                                    ORDER BY a.created_at DESC");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $sql = "INSERT INTO annonces (bailleur_id, titre, description, type_bien, zone_geographique, prix, surface, statut) 
                VALUES (:bailleur_id, :titre, :description, :type_bien, :zone, :prix, :surface, :statut)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'bailleur_id' => $data['bailleur_id'],
            'titre' => $data['titre'],
            'description' => $data['description'],
            'type_bien' => $data['type_bien'],
            'zone' => $data['zone'],
            'prix' => $data['prix'],
            'surface' => $data['surface'] ?? null,
            'statut' => $data['statut'] ?? 'attente'
        ]);
        
        if ($success) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        
        $allowedFields = ['titre', 'description', 'type_bien', 'zone_geographique', 'prix', 'surface', 'statut'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE annonces SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        // Supprimer les photos associées
        $photos = $this->photoModel->getByAnnonce($id);
        foreach ($photos as $photo) {
            $this->photoModel->delete($photo['id']);
        }
        
        $stmt = $this->db->prepare("DELETE FROM annonces WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM annonces");
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function countByStatus() {
        $stmt = $this->db->query("SELECT statut, COUNT(*) as count FROM annonces GROUP BY statut");
        $stats = [];
        while ($row = $stmt->fetch()) {
            $stats[$row['statut']] = $row['count'];
        }
        return $stats;
    }
    
    public function getWaitingForValidation() {
        $stmt = $this->db->prepare("SELECT a.*, u.nom, u.prenom FROM annonces a 
                                    JOIN users u ON a.bailleur_id = u.id 
                                    WHERE a.statut = 'attente' 
                                    ORDER BY a.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function validate($id) {
        $sql = "UPDATE annonces SET statut = 'publie', date_publication = NOW() WHERE id = :id AND statut = 'attente'";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute(['id' => $id]);
        
        // Vérifier si une ligne a été modifiée
        if ($success && $stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
    
    public function retire($id) {
        $sql = "UPDATE annonces SET statut = 'retire' WHERE id = :id AND statut = 'publie'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function incrementViews($id) {
        $sql = "UPDATE annonces SET nb_vues = nb_vues + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}