<?php
namespace App\Models;

use App\Config\Database;
use App\Helpers\Upload;

class Photo {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO photos_annonce (annonce_id, photo_path, is_principal, ordre) 
                VALUES (:annonce_id, :photo_path, :is_principal, :ordre)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function getByAnnonce($annonceId) {
        $stmt = $this->db->prepare("SELECT * FROM photos_annonce WHERE annonce_id = :annonce_id ORDER BY ordre");
        $stmt->execute(['annonce_id' => $annonceId]);
        return $stmt->fetchAll();
    }
    
    public function getPrincipal($annonceId) {
        $stmt = $this->db->prepare("SELECT * FROM photos_annonce WHERE annonce_id = :annonce_id AND is_principal = 1 LIMIT 1");
        $stmt->execute(['annonce_id' => $annonceId]);
        return $stmt->fetch();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM photos_annonce WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function delete($id) {
        $photo = $this->getById($id);
        if ($photo) {
            // Supprimer le fichier physique
            $subDir = '';
            Upload::deleteFile($photo['photo_path'], $subDir);
            
            // Supprimer de la base
            $stmt = $this->db->prepare("DELETE FROM photos_annonce WHERE id = :id");
            $success = $stmt->execute(['id' => $id]);
            
            // Si c'était la photo principale, définir une nouvelle
            if ($photo['is_principal']) {
                $this->setNewPrincipal($photo['annonce_id']);
            }
            
            return $success;
        }
        return false;
    }
    
    private function setNewPrincipal($annonceId) {
        $stmt = $this->db->prepare("SELECT id FROM photos_annonce WHERE annonce_id = :annonce_id ORDER BY ordre LIMIT 1");
        $stmt->execute(['annonce_id' => $annonceId]);
        $result = $stmt->fetch();
        
        if ($result) {
            $stmt = $this->db->prepare("UPDATE photos_annonce SET is_principal = 1 WHERE id = :id");
            $stmt->execute(['id' => $result['id']]);
        }
    }
    
    public function countByAnnonce($annonceId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM photos_annonce WHERE annonce_id = :annonce_id");
        $stmt->execute(['annonce_id' => $annonceId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
}